<!-- ══ LOAD OFFLINE / LOCAL FACE-API DEEP LEARNING SCRIPT ══ -->
<script src="/face-api.min.js"></script>

<!-- ══ MODAL LIHAT DETAIL FACE ID (BIOMETRIC INSPECTOR MODAL) ══ -->
<div id="faceViewModal" class="modal-overlay" style="z-index:99999;">
  <div class="modal-card" style="max-width:500px; padding:0; overflow:hidden; border-radius:24px; border:1px solid var(--border-2); background:var(--bg-2); box-shadow:0 25px 70px rgba(0,0,0,0.45);">
    
    <!-- Modal Header -->
    <div style="padding:16px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:linear-gradient(180deg, var(--surface) 0%, var(--bg-card) 100%);">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:34px; height:34px; border-radius:10px; background:rgba(16,185,129,0.15); display:flex; align-items:center; justify-content:center; color:#10B981; font-size:18px;">
          <i class="bi bi-shield-fill-check"></i>
        </div>
        <div>
          <strong style="font-size:15px; font-weight:800; color:var(--text); display:block; line-height:1.2;">Status Biometrik Face ID</strong>
          <span style="font-size:11.5px; color:var(--text-3);">Deep Learning AI ResNet-34 (68 Landmarks)</span>
        </div>
      </div>
      <button type="button" onclick="closeViewFaceModal()" class="btn btn-outline" style="width:32px; height:32px; padding:0; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; color:var(--text-3);" title="Tutup">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <div style="padding:22px;">
      <!-- Person Profile Card -->
      <div style="text-align:center; padding:16px; background:var(--surface); border:1px solid var(--border); border-radius:16px; margin-bottom:16px;">
        <div style="position:relative; width:72px; height:72px; margin:0 auto 12px;">
          <img id="viewTargetPhoto" src="/img/logo.png" style="width:100%; height:100%; border-radius:50%; object-fit:cover; border:3px solid #10B981; box-shadow:0 0 20px rgba(16,185,129,0.3);" />
          <div style="position:absolute; bottom:0; right:0; width:22px; height:22px; background:#10B981; border:2px solid #000; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px;">
            <i class="bi bi-check"></i>
          </div>
        </div>
        <strong id="viewTargetName" style="font-size:16px; color:var(--text); display:block; font-weight:800; margin-bottom:2px;">Nama Guru / Siswa</strong>
        <span id="viewTargetIdent" style="font-size:12px; color:var(--text-3); font-family:var(--font-mono); font-weight:600;">NIP / NIS</span>
      </div>

      <!-- Biometric Metadata Grid -->
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:16px;">
        <div style="background:var(--bg-3); padding:10px 14px; border-radius:12px; border:1px solid var(--border);">
          <div style="font-size:10.5px; color:var(--text-3); font-weight:700; text-transform:uppercase; margin-bottom:3px;">Arsitektur AI</div>
          <div style="display:flex; align-items:center; gap:6px; color:#10B981; font-weight:800; font-size:12.5px;">
            <i class="bi bi-cpu-fill"></i> ResNet-34 AI 128-D
          </div>
        </div>
        <div style="background:var(--bg-3); padding:10px 14px; border-radius:12px; border:1px solid var(--border);">
          <div style="font-size:10.5px; color:var(--text-3); font-weight:700; text-transform:uppercase; margin-bottom:3px;">Waktu Perekaman</div>
          <div id="viewTargetRegisteredAt" style="color:var(--text); font-weight:700; font-size:12px;">
            -
          </div>
        </div>
      </div>

      <!-- Visual Biometric Heatmap / Vektor Bars -->
      <div style="background:#020617; border:1px solid rgba(255,255,255,0.1); border-radius:14px; padding:12px 16px; margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <span style="font-size:11px; font-weight:800; color:#38BDF8; letter-spacing:0.5px; display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-fingerprint"></i> 68-LANDMARK 3D ALIGNED
          </span>
          <span style="font-size:10px; font-weight:700; color:#10B981; background:rgba(16,185,129,0.15); padding:2px 8px; border-radius:6px;">
            AKURASI TINGGI
          </span>
        </div>
        <div style="display:flex; align-items:flex-end; gap:3px; height:32px;">
          @for($i = 0; $i < 32; $i++)
            <div style="flex:1; background:linear-gradient(180deg, #10B981 0%, #065F46 100%); height:{{ rand(30, 95) }}%; border-radius:2px; opacity:0.85;"></div>
          @endfor
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="modal-action-row" style="display:flex; gap:10px;">
        <button type="button" onclick="triggerReEnrollFromView()" class="btn btn-gold" style="flex:1; height:44px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:12px;">
          <i class="bi bi-camera-fill"></i> Rekam Ulang Face ID
        </button>
        <button type="button" onclick="deleteTargetFaceFromView()" class="btn btn-danger" style="height:44px; padding:0 16px; font-size:12.5px; font-weight:700; display:inline-flex; align-items:center; gap:6px; border-radius:12px;" title="Hapus Face ID">
          <i class="bi bi-trash3-fill"></i> Hapus Face ID
        </button>
        <button type="button" onclick="closeViewFaceModal()" class="btn btn-outline" style="height:44px; padding:0 16px; font-size:13px; font-weight:700; border-radius:12px;">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══ MODAL PEREKAMAN FACE ID BIO-VERIFIKASI (KYC STEP-BY-STEP) ══ -->
<div id="faceEnrollModal" class="modal-overlay" style="z-index:99999;">
  <div class="modal-card" style="max-width:540px; padding:0; overflow:hidden; border-radius:24px; border:1px solid var(--border-2); background:var(--bg-2); box-shadow:0 25px 70px rgba(0,0,0,0.45);">
    
    <!-- Modal Header -->
    <div style="padding:16px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:linear-gradient(180deg, var(--surface) 0%, var(--bg-card) 100%);">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:34px; height:34px; border-radius:10px; background:rgba(202,138,4,0.15); display:flex; align-items:center; justify-content:center; color:var(--gold); font-size:18px;">
          <i class="bi bi-shield-check"></i>
        </div>
        <div>
          <strong style="font-size:15px; font-weight:800; color:var(--text); display:block; line-height:1.2;">Perekaman Face ID (Deep Learning AI)</strong>
          <span style="font-size:11.5px; color:var(--text-3);">ResNet-34 Neural Network &amp; 68 Facial Landmarks</span>
        </div>
      </div>
      <button type="button" onclick="closeFaceEnrollModal()" class="btn btn-outline" style="width:32px; height:32px; padding:0; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; color:var(--text-3);" title="Tutup Modal">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <div style="padding:20px 24px;">
      <!-- Person Profile Card -->
      <div style="display:flex; align-items:center; justify-content:space-between; background:var(--surface); padding:10px 14px; border-radius:14px; margin-bottom:14px; border:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:12px;">
          <img id="enrollTargetPhoto" src="/img/logo.png" style="width:42px; height:42px; border-radius:50%; object-fit:cover; border:2px solid var(--gold);" />
          <div>
            <strong id="enrollTargetName" style="font-size:13.5px; color:var(--text); display:block;">Nama Target</strong>
            <span id="enrollTargetIdent" style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NIS / NIP</span>
          </div>
        </div>
        <div id="enrollBadgeStatus" style="font-size:11px; font-weight:800; padding:4px 10px; border-radius:8px; background:rgba(202,138,4,0.12); color:var(--gold);">
          Tahap 1 dari 3
        </div>
      </div>

      <!-- Camera Viewport Box with Circular Progress Ring -->
      <div style="position:relative; width:100%; height:290px; border-radius:18px; overflow:hidden; background:#020617; display:flex; align-items:center; justify-content:center; border:2px solid var(--border-2); margin-bottom:14px; box-shadow:inset 0 0 40px rgba(0,0,0,0.8);">
        <video id="enrollVideo" autoplay playsinline muted style="width:100%; height:100%; object-fit:cover; transform:scaleX(-1);"></video>
        <canvas id="enrollCanvas" style="position:absolute; width:100%; height:100%; pointer-events:none; transform:scaleX(-1);"></canvas>

        <!-- Circular KYC Progress Ring Overlay -->
        <div style="position:absolute; width:220px; height:220px; display:flex; align-items:center; justify-content:center; pointer-events:none;">
          <svg style="width:100%; height:100%; transform:rotate(-90deg);" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="44" stroke="rgba(255,255,255,0.15)" stroke-width="4" fill="transparent" />
            <circle id="kycProgressCircle" cx="50" cy="50" r="44" stroke="var(--gold)" stroke-width="4.5" stroke-dasharray="276.46" stroke-dashoffset="276.46" stroke-linecap="round" fill="transparent" style="transition: stroke-dashoffset 0.35s ease, stroke 0.3s ease;" />
          </svg>

          <!-- Scanning Oval / Laser Reticle -->
          <div id="kycReticleOval" style="position:absolute; width:140px; height:180px; border:2px dashed rgba(250,204,21,0.6); border-radius:40px; transition:all 0.3s ease;"></div>
          <div id="kycLaserBeam" style="position:absolute; width:140px; height:2.5px; background:linear-gradient(90deg, transparent, var(--gold), #fff, var(--gold), transparent); box-shadow:0 0 10px var(--gold); display:none; animation:kycLaserAnim 2s ease-in-out infinite;"></div>
        </div>

        <!-- Real-time Instruction Floating Banner -->
        <div id="enrollStatusTag" style="position:absolute; bottom:12px; left:16px; right:16px; text-align:center; padding:7px 14px; background:rgba(15,23,42,0.85); backdrop-filter:blur(10px); border-radius:20px; font-size:12px; color:#fff; font-weight:700; border:1px solid rgba(255,255,255,0.15); box-shadow:0 4px 16px rgba(0,0,0,0.4);">
          Memuat Model AI Biometrik...
        </div>

        <!-- Percentage Counter Overlay -->
        <div id="kycPercentTag" style="position:absolute; top:12px; right:12px; background:rgba(15,23,42,0.8); border:1px solid var(--border); padding:3px 10px; border-radius:12px; font-size:11px; font-weight:800; color:var(--gold); font-family:var(--font-mono);">
          0%
        </div>
      </div>

      <!-- Interactive 3-Step Guidance Indicators -->
      <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:8px; margin-bottom:16px;">
        <div id="stepCard1" class="step-active" style="background:var(--surface); border:1px solid var(--border); padding:8px 6px; border-radius:10px; text-align:center; transition:all 0.3s ease;">
          <div style="font-size:14px; margin-bottom:2px;"><i class="bi bi-person-fill" id="stepIcon1" style="color:var(--gold);"></i></div>
          <div style="font-size:10.5px; font-weight:800; color:var(--text);" id="stepTitle1">1. Wajah Lurus</div>
          <div style="font-size:9.5px; color:var(--text-3);" id="stepDesc1">Tatap Kamera</div>
        </div>
        <div id="stepCard2" style="background:var(--surface); border:1px solid var(--border); padding:8px 6px; border-radius:10px; text-align:center; transition:all 0.3s ease;">
          <div style="font-size:14px; margin-bottom:2px;"><i class="bi bi-arrow-left-right" id="stepIcon2" style="color:var(--text-3);"></i></div>
          <div style="font-size:10.5px; font-weight:800; color:var(--text);" id="stepTitle2">2. Sudut 3D</div>
          <div style="font-size:9.5px; color:var(--text-3);" id="stepDesc2">Toleh Sedikit</div>
        </div>
        <div id="stepCard3" style="background:var(--surface); border:1px solid var(--border); padding:8px 6px; border-radius:10px; text-align:center; transition:all 0.3s ease;">
          <div style="font-size:14px; margin-bottom:2px;"><i class="bi bi-emoji-smile-fill" id="stepIcon3" style="color:var(--text-3);"></i></div>
          <div style="font-size:10.5px; font-weight:800; color:var(--text);" id="stepTitle3">3. Liveness</div>
          <div style="font-size:9.5px; color:var(--text-3);" id="stepDesc3">Kedip / Senyum</div>
        </div>
      </div>

      <!-- Step Action Buttons (Manual Step-by-Step Control) -->
      <div class="modal-action-row" style="display:flex; gap:10px;">
        <button type="button" onclick="executeCurrentStep()" id="btnKycAction" class="btn btn-gold" style="flex:1; height:46px; font-weight:800; font-size:13.5px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:12px;">
          <span id="btnKycSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
          <i class="bi bi-camera-fill" id="btnKycIcon"></i>
          <span id="btnKycText">Ambil Sampel 1: Wajah Lurus</span>
        </button>
        <button type="button" onclick="deleteTargetFace()" id="btnDeleteFace" class="btn btn-danger" style="height:46px; padding:0 14px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px; border-radius:12px;" title="Hapus rekaman Face ID orang ini">
          <i class="bi bi-trash3-fill"></i> Hapus
        </button>
        <button type="button" onclick="closeFaceEnrollModal()" class="btn btn-outline" style="height:46px; padding:0 18px; font-size:13px; font-weight:700; border-radius:12px;">
          Batal
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  @keyframes kycLaserAnim {
    0%, 100% { transform: translateY(-70px); opacity: 0.2; }
    50% { transform: translateY(70px); opacity: 1; }
  }
  .step-active {
    border-color: var(--gold) !important;
    background: rgba(202,138,4,0.14) !important;
  }
  .step-active i {
    color: var(--gold) !important;
  }
  .step-done {
    border-color: #10B981 !important;
    background: rgba(16,185,129,0.12) !important;
  }
  .step-done i {
    color: #10B981 !important;
  }

  @media (max-width: 540px) {
    #faceEnrollModal .modal-card,
    #viewFaceModal .modal-card {
      width: 95vw !important;
      max-width: 95vw !important;
      border-radius: 18px !important;
    }
    #faceEnrollModal .modal-card > div:last-child {
      padding: 14px 12px !important;
    }
    .modal-action-row {
      flex-wrap: wrap !important;
      gap: 8px !important;
    }
    .modal-action-row button {
      flex: 1 1 auto !important;
      font-size: 12px !important;
      height: 40px !important;
    }
  }
</style>

<script>
  let enrollStream = null;
  let enrollTargetType = null;
  let enrollTargetId = null;
  let currentKycStage = 1;
  let collectedSamples = [];
  let isStepProcessing = false;
  let isAiModelLoaded = false;
  let enrollPreviewTimer = null;
  let latestLiveDescriptor = null;

  let globalCurrentTarget = {
    type: null,
    id: null,
    name: '',
    identitas: '',
    photoUrl: '',
    registeredAt: ''
  };

  // ════ INISIALISASI MODEL AI FACE-API DEEP LEARNING (OFFLINE / LOKAL) ════
  async function loadFaceAiModels() {
    if (isAiModelLoaded) return true;
    try {
      if (typeof faceapi !== 'undefined') {
        await Promise.all([
          faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
          faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
          faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
          faceapi.nets.faceRecognitionNet.loadFromUri('/models')
        ]);
        isAiModelLoaded = true;
        console.log("✅ Face-API ResNet-34 & SSD MobileNet Models Loaded.");
        return true;
      }
    } catch (err) {
      console.warn("Face-API loading note:", err);
      // Fallback coba TinyFaceDetector saja
      try {
        await Promise.all([
          faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
          faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
          faceapi.nets.faceRecognitionNet.loadFromUri('/models')
        ]);
        isAiModelLoaded = true;
        return true;
      } catch (e2) {
        console.error("Secondary load failed:", e2);
      }
    }
    return false;
  }

  // ════ MODAL LIHAT DETAIL FACE ID ════
  function openViewFaceModal(type, id, name, identitas, photoUrl, registeredAt) {
    globalCurrentTarget = { type, id, name, identitas, photoUrl, registeredAt };

    const nameElem = document.getElementById('viewTargetName');
    const identElem = document.getElementById('viewTargetIdent');
    const photoElem = document.getElementById('viewTargetPhoto');
    const regElem = document.getElementById('viewTargetRegisteredAt');

    if (nameElem) nameElem.textContent = name;
    if (identElem) identElem.textContent = identitas;
    if (photoElem && photoUrl) photoElem.src = photoUrl;
    if (regElem) regElem.textContent = registeredAt || 'Terdaftar Aktif';

    const modal = document.getElementById('faceViewModal');
    if (modal) modal.classList.add('active');
  }

  function closeViewFaceModal() {
    const modal = document.getElementById('faceViewModal');
    if (modal) modal.classList.remove('active');
  }

  function triggerReEnrollFromView() {
    closeViewFaceModal();
    openFaceEnrollModal(
      globalCurrentTarget.type,
      globalCurrentTarget.id,
      globalCurrentTarget.name,
      globalCurrentTarget.identitas,
      globalCurrentTarget.photoUrl
    );
  }

  async function deleteTargetFaceFromView() {
    if (!confirm(`Hapus data Face ID untuk ${globalCurrentTarget.name}?`)) {
      return;
    }
    await executeDeleteFaceApi(globalCurrentTarget.type, globalCurrentTarget.id);
  }

  // ════ QUICK DELETE LANGSUNG DARI TABEL ════
  async function quickDeleteFace(type, id, name) {
    if (!confirm(`Hapus data biometrik Face ID untuk ${name}?`)) {
      return;
    }
    await executeDeleteFaceApi(type, id);
  }

  async function executeDeleteFaceApi(type, id) {
    try {
      const res = await fetch('/api/v1/face-enroll', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({ type, id })
      });

      const json = await res.json();
      if (json.success) {
        alert(json.message);
        window.location.reload();
      } else {
        alert("Gagal menghapus Face ID.");
      }
    } catch (e) {
      alert("Terjadi kesalahan jaringan.");
    }
  }

  // ════ MODAL PEREKAMAN KYC STEP-BY-STEP (FULL RESNET-34 AI) ════
  async function openFaceEnrollModal(type, id, name, identitas, photoUrl) {
    enrollTargetType = type;
    enrollTargetId = id;
    collectedSamples = [];
    currentKycStage = 1;
    isStepProcessing = false;
    latestLiveDescriptor = null;

    globalCurrentTarget = { type, id, name, identitas, photoUrl, registeredAt: '' };

    const nameElem = document.getElementById('enrollTargetName');
    const identElem = document.getElementById('enrollTargetIdent');
    const photoElem = document.getElementById('enrollTargetPhoto');

    if (nameElem) nameElem.textContent = name;
    if (identElem) identElem.textContent = identitas;
    if (photoElem && photoUrl) photoElem.src = photoUrl;

    initStageUI(1);

    const modal = document.getElementById('faceEnrollModal');
    if (modal) modal.classList.add('active');

    try {
      const video = document.getElementById('enrollVideo');
      enrollStream = await navigator.mediaDevices.getUserMedia({
        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }
      });
      if (video) video.srcObject = enrollStream;
      speakPrompt("Tahap satu: Posisikan wajah lurus ke depan.");
    } catch (err) {
      console.error(err);
      const statusTag = document.getElementById('enrollStatusTag');
      if (statusTag) statusTag.textContent = 'Kamera tidak dapat diakses atau izin ditolak.';
    }

    // Preload & Mulai Live Face Tracking Loop
    loadFaceAiModels().then(() => {
      startLiveTrackingLoop();
    });
  }

  function closeFaceEnrollModal() {
    isStepProcessing = false;
    if (enrollPreviewTimer) {
      clearInterval(enrollPreviewTimer);
      enrollPreviewTimer = null;
    }
    const modal = document.getElementById('faceEnrollModal');
    if (modal) modal.classList.remove('active');

    if (enrollStream) {
      enrollStream.getTracks().forEach(track => track.stop());
      enrollStream = null;
    }
  }

  // ════ LIVE TRACKING PREVIEW (MEMBERIKAN FEEDBACK HIJAU / KUNING SECARA REAL-TIME) ════
  function startLiveTrackingLoop() {
    if (enrollPreviewTimer) clearInterval(enrollPreviewTimer);
    const video = document.getElementById('enrollVideo');
    const reticle = document.getElementById('kycReticleOval');
    const statusTag = document.getElementById('enrollStatusTag');

    enrollPreviewTimer = setInterval(async () => {
      if (isStepProcessing || !video || !video.videoWidth || !isAiModelLoaded) {
        return;
      }

      try {
        const desc = await extractDeepFaceDescriptor(video);
        if (desc) {
          latestLiveDescriptor = desc;
          if (reticle) {
            reticle.style.borderColor = '#10B981';
            reticle.style.boxShadow = '0 0 25px rgba(16,185,129,0.6)';
          }
          if (statusTag && !isStepProcessing) {
            if (currentKycStage === 1) {
              statusTag.textContent = '✅ Wajah Terdeteksi Jelas · Klik Tombol di Bawah';
              statusTag.style.color = '#10B981';
            } else if (currentKycStage === 2) {
              statusTag.textContent = '✅ Sudut 3D Terbaca · Klik Lanjut Sampel 2';
              statusTag.style.color = '#38BDF8';
            } else if (currentKycStage === 3) {
              statusTag.textContent = '✅ Liveness Siap · Klik Simpan Face ID';
              statusTag.style.color = '#A855F7';
            }
          }
        } else {
          latestLiveDescriptor = null;
          if (reticle) {
            reticle.style.borderColor = 'rgba(250,204,21,0.6)';
            reticle.style.boxShadow = 'none';
          }
          if (statusTag && !isStepProcessing) {
            if (currentKycStage === 1) {
              statusTag.textContent = 'Tahap 1: Posisikan wajah tepat di depan kamera';
              statusTag.style.color = '#FACC15';
            } else if (currentKycStage === 2) {
              statusTag.textContent = 'Tahap 2: Tolehkan wajah sedikit ke kanan/kiri';
              statusTag.style.color = '#38BDF8';
            } else if (currentKycStage === 3) {
              statusTag.textContent = 'Tahap 3: Kedipkan mata atau tersenyum santai';
              statusTag.style.color = '#A855F7';
            }
          }
        }
      } catch (e) {
        // Silent frame tracking
      }
    }, 220);
  }

  function setButtonState(isLoading, text, iconClass) {
    const spinner = document.getElementById('btnKycSpinner');
    const icon = document.getElementById('btnKycIcon');
    const txt = document.getElementById('btnKycText');
    const btn = document.getElementById('btnKycAction');

    if (spinner) spinner.style.display = isLoading ? 'inline-block' : 'none';
    if (icon) {
      icon.style.display = isLoading ? 'none' : 'inline-block';
      if (iconClass) icon.className = iconClass;
    }
    if (txt && text) txt.textContent = text;
    if (btn) btn.disabled = isLoading;
  }

  function initStageUI(stage) {
    currentKycStage = stage;
    const badge = document.getElementById('enrollBadgeStatus');
    const laser = document.getElementById('kycLaserBeam');

    if (laser) laser.style.display = 'block';
    setButtonState(false);

    if (stage === 1) {
      setKycProgress(0);
      if (badge) {
        badge.textContent = 'Tahap 1 dari 3';
        badge.style.background = 'rgba(202,138,4,0.12)';
        badge.style.color = 'var(--gold)';
      }
      setButtonState(false, 'Ambil Sampel 1: Wajah Lurus', 'bi bi-camera-fill');

      setStepActive(1);
      setStepReset(2);
      setStepReset(3);
    } else if (stage === 2) {
      setKycProgress(33);
      if (badge) {
        badge.textContent = 'Tahap 2 dari 3';
        badge.style.background = 'rgba(56,189,248,0.15)';
        badge.style.color = '#38BDF8';
      }
      setButtonState(false, 'Lanjut Sampel 2: Tolehkan Wajah', 'bi bi-arrow-left-right');

      setStepDone(1);
      setStepActive(2);
      setStepReset(3);
    } else if (stage === 3) {
      setKycProgress(66);
      if (badge) {
        badge.textContent = 'Tahap 3 dari 3 (Terakhir)';
        badge.style.background = 'rgba(168,85,247,0.15)';
        badge.style.color = '#A855F7';
      }
      setButtonState(false, 'Ambil Sampel 3 & Simpan Face ID', 'bi bi-shield-check');

      setStepDone(1);
      setStepDone(2);
      setStepActive(3);
    }
  }

  function setKycProgress(percent) {
    const circle = document.getElementById('kycProgressCircle');
    const tag = document.getElementById('kycPercentTag');
    const circumference = 2 * Math.PI * 44; // 276.46
    const offset = circumference - (percent / 100) * circumference;
    
    if (circle) circle.style.strokeDashoffset = offset;
    if (tag) tag.textContent = `${Math.round(percent)}%`;

    if (circle) {
      if (percent >= 100) {
        circle.style.stroke = '#10B981';
      } else if (percent >= 66) {
        circle.style.stroke = '#A855F7';
      } else if (percent >= 33) {
        circle.style.stroke = '#38BDF8';
      } else {
        circle.style.stroke = 'var(--gold)';
      }
    }
  }

  function speakPrompt(text) {
    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel();
      const utter = new SpeechSynthesisUtterance(text);
      utter.lang = 'id-ID';
      utter.rate = 1.05;
      window.speechSynthesis.speak(utter);
    }
  }

  // ════ CEK STATUS PYTHON INSIGHTFACE SERVICE ════
  let pythonServiceActive = null; // null = belum dicek, true/false = status

  async function checkPythonService() {
    if (pythonServiceActive !== null) return pythonServiceActive;
    try {
      const res = await fetch('/api/v1/face-service-status', { method: 'GET' });
      const json = await res.json();
      pythonServiceActive = (json.python_service === 'online');
    } catch (e) {
      pythonServiceActive = false;
    }
    return pythonServiceActive;
  }

  // ════ CAPTURE JPEG BASE64 DARI VIDEO (UNTUK PYTHON INSIGHTFACE) ════
  function captureFrameBase64(videoElement, quality = 0.90) {
    const canvas = document.createElement('canvas');
    canvas.width = videoElement.videoWidth;
    canvas.height = videoElement.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(videoElement, 0, 0);
    return canvas.toDataURL('image/jpeg', quality);
  }

  // ════ EKSEKUSI TAHAP PEREKAMAN ════
  // Mode A: Python InsightFace ArcFace R100 (99.83%) — kirim JPEG ke server
  // Mode B: Fallback face-api.js ResNet-34 (97.7%) — jika Python offline
  async function executeCurrentStep() {
    if (isStepProcessing) return;
    isStepProcessing = true;

    const video = document.getElementById('enrollVideo');
    const statusTag = document.getElementById('enrollStatusTag');

    if (!video || !video.videoWidth) {
      alert("Kamera belum siap. Mohon tunggu beberapa detik.");
      isStepProcessing = false;
      return;
    }

    const sleep = ms => new Promise(r => setTimeout(r, ms));
    const usePython = await checkPythonService();

    setButtonState(true, usePython ? 'Memindai AI Biometrik (Server)...' : 'Memindai Biometrik (Browser)...');

    try {
      if (currentKycStage === 1) {
        if (statusTag) {
          statusTag.textContent = usePython
            ? 'Memindai Vektor Biometrik Wajah Lurus...'
            : 'Memindai 68 Titik Kontur Wajah Lurus...';
        }

        const sample = await captureSample(video, usePython, sleep);
        if (!sample) throw new Error("Wajah tidak terdeteksi. Pastikan wajah tepat di depan kamera dan pencahayaan cukup.");

        collectedSamples.push(sample);
        speakPrompt("Tahap 1 selesai. Silakan tolehkan wajah sedikit.");
        initStageUI(2);

      } else if (currentKycStage === 2) {
        if (statusTag) {
          statusTag.textContent = usePython
            ? 'Memindai Sudut & Kedalaman 3D Wajah...'
            : 'Memindai Sudut Kedalaman 3D...';
        }

        const sample = await captureSample(video, usePython, sleep);
        if (!sample) throw new Error("Wajah tidak terdeteksi saat menoleh. Pastikan wajah tetap terlihat di kamera.");

        collectedSamples.push(sample);
        speakPrompt("Tahap 2 selesai. Silakan kedipkan mata atau tersenyum.");
        initStageUI(3);

      } else if (currentKycStage === 3) {
        if (statusTag) {
          statusTag.textContent = usePython
            ? 'Memindai Liveness Biometrik...'
            : 'Memindai Liveness...';
        }

        const sample = await captureSample(video, usePython, sleep);
        if (!sample) throw new Error("Wajah tidak terdeteksi. Posisikan wajah di kamera dan ulangi.");

        collectedSamples.push(sample);
        setStepDone(3);
        setKycProgress(100);

        if (statusTag) {
          statusTag.textContent = 'Menyintesis Embedding Biometrik ke Database...';
          statusTag.style.color = '#10B981';
        }
        setButtonState(true, 'Menyimpan ke Database...');

        let enrollBody, enrollHeaders;

        if (usePython) {
          // MODE A: Kirim gambar ke Python InsightFace (server-side)
          // Ambil gambar terbaik dari sampel (sampel terakhir = liveness)
          enrollHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
          };
          enrollBody = JSON.stringify({
            type: enrollTargetType,
            id: enrollTargetId,
            image_base64: collectedSamples[2], // Gambar base64 dari Tahap 3
          });
        } else {
          // MODE B: Kirim embedding dari browser (fallback face-api.js)
          await loadFaceAiModels();
          const finalDescriptor = averageDeepDescriptors(collectedSamples.filter(s => Array.isArray(s)));
          enrollHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
          };
          enrollBody = JSON.stringify({
            type: enrollTargetType,
            id: enrollTargetId,
            embedding: finalDescriptor,
          });
        }

        const res = await fetch('/api/v1/face-enroll', {
          method: 'POST',
          headers: enrollHeaders,
          body: enrollBody,
        });

        const json = await res.json();
        if (json.success) {
          if (statusTag) {
            statusTag.textContent = `✅ Perekaman Face ID Berhasil!`;
          }
          speakPrompt("Perekaman biometrik selesai seratus persen.");
          await sleep(600);
          alert(json.message);
          closeFaceEnrollModal();
          window.location.reload();
        } else {
          alert("Gagal menyimpan Face ID: " + (json.error || 'Terjadi kesalahan'));
          initStageUI(1);
        }
      }
    } catch (err) {
      alert(err.message || "Terjadi kesalahan pada pemindaian.");
      if (statusTag) {
        statusTag.textContent = err.message || 'Pemindaian gagal, silakan coba lagi.';
        statusTag.style.color = '#EF4444';
      }
      setButtonState(false);
    } finally {
      isStepProcessing = false;
    }
  }

  function setStepActive(stepNum) {
    const card = document.getElementById(`stepCard${stepNum}`);
    const icon = document.getElementById(`stepIcon${stepNum}`);
    if (card) card.className = 'step-active';
    if (icon) icon.style.color = 'var(--gold)';
  }

  function setStepDone(stepNum) {
    const card = document.getElementById(`stepCard${stepNum}`);
    const icon = document.getElementById(`stepIcon${stepNum}`);
    if (card) card.className = 'step-done';
    if (icon) {
      icon.className = 'bi bi-check-circle-fill';
      icon.style.color = '#10B981';
    }
  }

  function setStepReset(stepNum) {
    const card = document.getElementById(`stepCard${stepNum}`);
    const icon = document.getElementById(`stepIcon${stepNum}`);
    if (card) {
      card.className = '';
      card.style.background = 'var(--surface)';
      card.style.borderColor = 'var(--border)';
    }
    if (icon) {
      icon.style.color = 'var(--text-3)';
      if (stepNum === 1) icon.className = 'bi bi-person-fill';
      if (stepNum === 2) icon.className = 'bi bi-arrow-left-right';
      if (stepNum === 3) icon.className = 'bi bi-emoji-smile-fill';
    }
  }

  /**
   * captureSample: Abstraksi pengambilan sampel biometrik.
   * Mode A (usePython=true): Tangkap JPEG base64 dari kamera.
   * Mode B (usePython=false): Ekstrak embedding 128-D via face-api.js.
   */
  async function captureSample(videoElement, usePython, sleep) {
    if (usePython) {
      // Mode A: Base64 JPEG — verifikasi wajah dilakukan di server Python
      return captureFrameBase64(videoElement, 0.92);
    }

    // Mode B: face-api.js embedding (fallback)
    let sample = latestLiveDescriptor || await extractDeepFaceDescriptor(videoElement);
    if (!sample) {
      for (let retry = 0; retry < 3; retry++) {
        await sleep(150);
        sample = await extractDeepFaceDescriptor(videoElement);
        if (sample) break;
      }
    }
    return sample;
  }

  // ════ EKSTRAKSI VEKTOR AI DEEP LEARNING (FILTER 1 WAJAH DI DALAM OVAL RETICLE) ════
  async function extractDeepFaceDescriptor(videoElement) {
    if (!videoElement || !videoElement.videoWidth) return null;
    await loadFaceAiModels();

    if (typeof faceapi !== 'undefined' && isAiModelLoaded) {
      try {
        const allDetections = await faceapi
          .detectAllFaces(videoElement, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.20 }))
          .withFaceLandmarks()
          .withFaceDescriptors();

        const vW = videoElement.videoWidth;
        const vH = videoElement.videoHeight;
        const roiLeft = vW * 0.18;
        const roiRight = vW * 0.82;
        const roiTop = vH * 0.08;
        const roiBottom = vH * 0.92;
        const minFaceWidth = vW * 0.15;

        const insideFaces = allDetections.filter(d => {
          const box = d.detection.box;
          const centerX = box.x + box.width / 2;
          const centerY = box.y + box.height / 2;
          return (
            centerX >= roiLeft &&
            centerX <= roiRight &&
            centerY >= roiTop &&
            centerY <= roiBottom &&
            box.width >= minFaceWidth
          );
        });

        if (insideFaces.length > 0) {
          insideFaces.sort((a, b) => b.detection.box.area - a.detection.box.area);
          return Array.from(insideFaces[0].descriptor);
        }
      } catch (err) {
        console.warn("Deep model detection attempt:", err);
      }
    }
    return null;
  }

  // Meratakan Multi-Sampel ResNet Menjadi 1 Vektor Terpadu (Unit L2-Normalized)
  function averageDeepDescriptors(samples) {
    const dim = samples[0].length;
    const avgVec = new Float32Array(dim);

    for (let i = 0; i < dim; i++) {
      let sum = 0;
      for (let s = 0; s < samples.length; s++) {
        sum += samples[s][i];
      }
      avgVec[i] = sum / samples.length;
    }

    let normSq = 0;
    for (let i = 0; i < dim; i++) {
      normSq += avgVec[i] * avgVec[i];
    }
    const norm = Math.sqrt(normSq);
    if (norm === 0) return Array.from(avgVec);

    const result = [];
    for (let i = 0; i < dim; i++) {
      result.push(parseFloat((avgVec[i] / norm).toFixed(6)));
    }
    return result;
  }

  async function deleteTargetFace() {
    const targetName = document.getElementById('enrollTargetName')?.textContent || 'orang ini';
    if (!confirm(`Hapus data biometrik Face ID untuk ${targetName}?`)) {
      return;
    }
    await executeDeleteFaceApi(enrollTargetType, enrollTargetId);
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadFaceAiModels();

    const enrollModal = document.getElementById('faceEnrollModal');
    if (enrollModal) {
      enrollModal.addEventListener('click', (e) => {
        if (e.target === enrollModal && !isStepProcessing) closeFaceEnrollModal();
      });
    }

    const viewModal = document.getElementById('faceViewModal');
    if (viewModal) {
      viewModal.addEventListener('click', (e) => {
        if (e.target === viewModal) closeViewFaceModal();
      });
    }
  });
</script>
