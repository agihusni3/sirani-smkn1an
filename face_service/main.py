"""
SIRANI - InsightFace ArcFace R100 Microservice
Akurasi 99.83% (LFW Benchmark) | 512-D Embeddings
Berjalan di: http://localhost:8001
"""

import base64
import io
import logging
import time
from contextlib import asynccontextmanager
from typing import List, Optional

import cv2
import numpy as np
import uvicorn
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from PIL import Image
from pydantic import BaseModel

logging.basicConfig(level=logging.INFO, format="%(asctime)s %(levelname)s %(message)s")
logger = logging.getLogger("sirani-face")

# ── Global InsightFace App ──────────────────────────────────────────────────
face_app = None


def load_insightface():
    """Load InsightFace ArcFace R100 model (auto-download on first run ~200MB)."""
    global face_app
    try:
        import insightface
        from insightface.app import FaceAnalysis
        face_app = FaceAnalysis(
            name="buffalo_l",          # ArcFace R100 + RetinaFace detector
            providers=["CPUExecutionProvider"]
        )
        face_app.prepare(ctx_id=0, det_size=(640, 640))
        logger.info("✅ InsightFace ArcFace R100 (buffalo_l) loaded successfully.")
    except Exception as e:
        logger.error(f"❌ InsightFace load error: {e}")
        face_app = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    logger.info("🚀 SIRANI Face Service starting...")
    load_insightface()
    yield
    logger.info("🛑 SIRANI Face Service stopped.")


app = FastAPI(
    title="SIRANI Face Service",
    description="InsightFace ArcFace R100 - 99.83% Accuracy",
    version="2.0.0",
    lifespan=lifespan,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:8000", "http://127.0.0.1:8000", "*"],
    allow_methods=["*"],
    allow_headers=["*"],
)


# ── Pydantic Models ─────────────────────────────────────────────────────────

class EnrollRequest(BaseModel):
    image_base64: str  # JPEG/PNG base64 string


class Candidate(BaseModel):
    id: int
    type: str                # "siswa" | "guru"
    nama: str
    embedding: List[float]   # 512-D ArcFace vector


class VerifyRequest(BaseModel):
    image_base64: str
    candidates: List[Candidate]
    threshold: float = 0.40  # Cosine similarity threshold (>=0.40 = match)


# ── Helpers ─────────────────────────────────────────────────────────────────

def decode_image(b64_str: str) -> np.ndarray:
    """Decode base64 image string ke numpy BGR array."""
    # Strip header jika ada (data:image/jpeg;base64,...)
    if "," in b64_str:
        b64_str = b64_str.split(",", 1)[1]
    img_bytes = base64.b64decode(b64_str)
    img_pil = Image.open(io.BytesIO(img_bytes)).convert("RGB")
    img_bgr = cv2.cvtColor(np.array(img_pil), cv2.COLOR_RGB2BGR)
    return img_bgr


def l2_normalize(vec: np.ndarray) -> np.ndarray:
    """L2 normalisasi vektor."""
    norm = np.linalg.norm(vec)
    return vec / norm if norm > 0 else vec


def cosine_similarity(a: np.ndarray, b: np.ndarray) -> float:
    """Hitung cosine similarity antara dua vektor L2-normalized."""
    return float(np.dot(a, b))


# ── Endpoints ───────────────────────────────────────────────────────────────

@app.get("/health")
async def health():
    return {
        "status": "ok",
        "model": "InsightFace ArcFace R100 (buffalo_l)",
        "accuracy_lfw": "99.83%",
        "embedding_dim": 512,
        "model_loaded": face_app is not None,
    }


@app.post("/enroll")
async def enroll_face(req: EnrollRequest):
    """
    Ekstrak 512-D ArcFace embedding dari gambar wajah.
    Dipanggil saat perekaman Face ID (KYC enrollment).
    """
    if face_app is None:
        raise HTTPException(503, "Model AI belum siap, coba beberapa saat lagi.")

    try:
        t0 = time.time()
        img_bgr = decode_image(req.image_base64)

        faces = face_app.get(img_bgr)

        if not faces:
            raise HTTPException(422, "Wajah tidak terdeteksi. Pastikan wajah jelas dan pencahayaan cukup.")

        # Ambil wajah dengan deteksi score tertinggi
        best_face = max(faces, key=lambda f: f.det_score)
        embedding = l2_normalize(best_face.embedding)

        elapsed_ms = round((time.time() - t0) * 1000, 1)
        logger.info(f"Enroll: det_score={best_face.det_score:.3f} | t={elapsed_ms}ms")

        return {
            "success": True,
            "embedding": embedding.tolist(),
            "embedding_dim": len(embedding),
            "det_score": float(best_face.det_score),
            "elapsed_ms": elapsed_ms,
        }

    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Enroll error: {e}")
        raise HTTPException(500, f"Terjadi kesalahan: {str(e)}")


@app.post("/verify")
async def verify_face(req: VerifyRequest):
    """
    Cocokkan wajah dari gambar dengan daftar kandidat terdaftar.
    Dipanggil oleh Smart Gate Kiosk setiap frame kamera.
    """
    if face_app is None:
        raise HTTPException(503, "Model AI belum siap.")

    try:
        t0 = time.time()
        img_bgr = decode_image(req.image_base64)

        faces = face_app.get(img_bgr)

        if not faces:
            return {"success": False, "reason": "no_face", "match": None}

        # Ambil wajah terbesar / paling terpusat
        best_face = max(faces, key=lambda f: f.det_score)
        current_emb = l2_normalize(best_face.embedding)

        # Cocokkan dengan semua kandidat
        best_sim = -1.0
        best_candidate = None

        for cand in req.candidates:
            cand_emb = l2_normalize(np.array(cand.embedding, dtype=np.float32))
            sim = cosine_similarity(current_emb, cand_emb)
            if sim > best_sim:
                best_sim = sim
                best_candidate = cand

        elapsed_ms = round((time.time() - t0) * 1000, 1)
        match_pct = round(best_sim * 100, 1)

        if best_candidate and best_sim >= req.threshold:
            logger.info(f"Verify MATCH: {best_candidate.nama} sim={best_sim:.4f} t={elapsed_ms}ms")
            return {
                "success": True,
                "reason": "match",
                "match": {
                    "id": best_candidate.id,
                    "type": best_candidate.type,
                    "nama": best_candidate.nama,
                    "similarity": round(best_sim, 4),
                    "match_pct": match_pct,
                },
                "elapsed_ms": elapsed_ms,
            }
        elif best_candidate and best_sim >= (req.threshold - 0.10):
            # Borderline — terdeteksi tapi belum cukup yakin
            logger.info(f"Verify BORDERLINE: {best_candidate.nama} sim={best_sim:.4f}")
            return {
                "success": False,
                "reason": "borderline",
                "match": {
                    "id": best_candidate.id,
                    "type": best_candidate.type,
                    "nama": best_candidate.nama,
                    "similarity": round(best_sim, 4),
                    "match_pct": match_pct,
                },
                "elapsed_ms": elapsed_ms,
            }
        else:
            return {
                "success": False,
                "reason": "no_match",
                "match": None,
                "elapsed_ms": elapsed_ms,
            }

    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Verify error: {e}")
        raise HTTPException(500, f"Terjadi kesalahan: {str(e)}")


if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host="127.0.0.1",
        port=8001,
        reload=False,
        log_level="info",
    )
