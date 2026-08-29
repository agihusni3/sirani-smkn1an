<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Services\FaceScanService;
use App\Services\InsightFaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class FaceScanController extends Controller
{
    protected FaceScanService $scanService;
    protected InsightFaceService $insightFace;

    public function __construct(FaceScanService $scanService, InsightFaceService $insightFace)
    {
        $this->scanService  = $scanService;
        $this->insightFace  = $insightFace;
    }

    /**
     * Mengambil seluruh deskriptor/vektor wajah aktif untuk Smart Gate Kiosk.
     * Digunakan sebagai referensi matching di Python service.
     */
    public function getDescriptors(): JsonResponse
    {
        $siswas = Siswa::whereNotNull('face_embedding')
            ->whereIn('status', ['aktif', 'pkl'])
            ->with(['siswaRombels' => function ($q) {
                $q->where('status_keanggotaan', 'aktif')->with('rombel');
            }])
            ->get()
            ->map(function ($s) {
                $embedding = is_array($s->face_embedding) ? $s->face_embedding : json_decode($s->face_embedding, true);
                $rombelNama = $s->siswaRombels->first()?->rombel?->nama_rombel ?? 'Siswa';
                return [
                    'id'                  => $s->id,
                    'type'                => 'siswa',
                    'nama'                => $s->nama,
                    'identitas'           => 'NIS: ' . $s->nis,
                    'rombel_atau_jabatan' => $rombelNama,
                    'foto_url'            => $s->foto_url,
                    'embedding'           => $embedding,
                ];
            })
            ->filter(fn($item) => !empty($item['embedding']));

        $gurus = Guru::whereNotNull('face_embedding')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($g) {
                $embedding = is_array($g->face_embedding) ? $g->face_embedding : json_decode($g->face_embedding, true);
                return [
                    'id'                  => $g->id,
                    'type'                => 'guru',
                    'nama'                => $g->nama,
                    'identitas'           => $g->nip ? 'NIP: ' . $g->nip : $g->label_kepegawaian,
                    'rombel_atau_jabatan' => $g->jabatan ?? 'Guru',
                    'foto_url'            => $g->foto_url,
                    'embedding'           => $embedding,
                ];
            })
            ->filter(fn($item) => !empty($item['embedding']));

        $allDescriptors = $siswas->concat($gurus)->values();

        // Deteksi engine yang sedang digunakan
        $pythonActive = $this->insightFace->isHealthy();

        return response()->json([
            'success'        => true,
            'count'          => $allDescriptors->count(),
            'engine'         => $pythonActive ? 'insightface_arcface_r100' : 'faceapi_resnet34',
            'embedding_dim'  => $pythonActive ? 512 : 128,
            'data'           => $allDescriptors,
        ]);
    }

    /**
     * Smart Gate Kiosk — Verifikasi & Catat Absensi
     *
     * Mode A (InsightFace aktif): Terima image_base64 → Python verify → catat absensi
     * Mode B (Fallback): Terima type+id dari JS kiosk → catat langsung
     */
    public function handleFaceScan(Request $request): JsonResponse
    {
        try {
            // Mode A: InsightFace Python Server-side Verification
            if ($request->has('image_base64') && $this->insightFace->isHealthy()) {
                $request->validate([
                    'image_base64' => 'required|string',
                    'candidates'   => 'required|array|min:1',
                ]);

                $verifyResult = $this->insightFace->verify(
                    $request->input('image_base64'),
                    $request->input('candidates'),
                    threshold: 0.40
                );

                if (!$verifyResult['success']) {
                    return response()->json([
                        'success' => false,
                        'status'  => 'no_match',
                        'reason'  => $verifyResult['reason'] ?? 'no_match',
                        'message' => match($verifyResult['reason'] ?? '') {
                            'no_face'       => 'Wajah tidak terdeteksi di kamera.',
                            'no_match'      => 'Wajah tidak ditemukan di database.',
                            'borderline'    => 'Mendeteksi: ' . ($verifyResult['match']['nama'] ?? '') . ' (' . ($verifyResult['match']['match_pct'] ?? 0) . '% · Dekatkan wajah)',
                            'service_offline' => 'AI Service offline.',
                            default         => 'Tidak ditemukan kecocokan.',
                        },
                        'data'    => $verifyResult['match'] ?? null,
                    ]);
                }

                $match = $verifyResult['match'];
                $result = $this->scanService->scanPerson($match['type'], $match['id'], 'insightface_kiosk');

                return response()->json([
                    'success'     => ($result['status'] === 'success'),
                    'status'      => $result['status'],
                    'type'        => $result['type'],
                    'message'     => $result['message'],
                    'data'        => array_merge($result['data'], [
                        'match_pct'   => $match['match_pct'],
                        'similarity'  => $match['similarity'],
                        'engine'      => 'insightface_arcface_r100',
                        'elapsed_ms'  => $verifyResult['elapsed_ms'] ?? null,
                    ]),
                ], 200);
            }

            // Mode B: Fallback – JS kiosk sudah matching di browser (face-api.js)
            $request->validate([
                'type'       => 'required|in:siswa,guru',
                'id'         => 'required|integer',
                'confidence' => 'nullable|numeric',
            ]);

            $result = $this->scanService->scanPerson(
                $request->input('type'),
                (int)$request->input('id'),
                'face_kiosk'
            );

            return response()->json([
                'success' => ($result['status'] === 'success'),
                'status'  => $result['status'],
                'type'    => $result['type'],
                'message' => $result['message'],
                'data'    => $result['data'],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Pendaftaran / pembaruan biometrik wajah siswa atau guru.
     *
     * Mode A (InsightFace aktif): Terima image_base64 → Python ekstrak 512-D embedding
     * Mode B (Fallback): Terima embedding[] langsung dari browser (128-D face-api.js)
     */
    public function enrollFace(Request $request): JsonResponse
    {
        try {
            // Mode A: Server-side embedding via Python InsightFace
            if ($request->has('image_base64') && $this->insightFace->isHealthy()) {
                $request->validate([
                    'type'         => 'required|in:siswa,guru',
                    'id'           => 'required|integer',
                    'image_base64' => 'required|string',
                ]);

                $enrollResult = $this->insightFace->enroll($request->input('image_base64'));

                if (!$enrollResult['success']) {
                    return response()->json([
                        'success' => false,
                        'error'   => $enrollResult['error'] ?? 'Gagal mengekstrak embedding.',
                    ], 422);
                }

                $type      = $request->input('type');
                $id        = (int)$request->input('id');
                $embedding = $enrollResult['embedding'];

            } else {
                // Mode B: Fallback — embedding dari browser (face-api.js)
                $request->validate([
                    'type'      => 'required|in:siswa,guru',
                    'id'        => 'required|integer',
                    'embedding' => 'required|array|min:64',
                ]);

                $type      = $request->input('type');
                $id        = (int)$request->input('id');
                $embedding = $request->input('embedding');
            }

            $person = ($type === 'siswa') ? Siswa::findOrFail($id) : Guru::findOrFail($id);

            $person->update([
                'face_embedding'     => $embedding,
                'face_registered_at' => now(),
            ]);

            $operatorName = auth()->user()?->name ?? 'Administrator';
            \App\Models\AuditLog::catat(
                'enroll_face',
                $type,
                "Perekaman Face ID: {$person->nama} ({$type}) oleh {$operatorName}",
                null,
                [
                    'id'            => $person->id,
                    'nama'          => $person->nama,
                    'operator'      => $operatorName,
                    'engine'        => $request->has('image_base64') ? 'insightface_arcface_r100' : 'faceapi_resnet34',
                    'embedding_dim' => count($embedding),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Biometrik wajah {$person->nama} berhasil didaftarkan.",
                'data'    => [
                    'id'                 => $person->id,
                    'nama'               => $person->nama,
                    'embedding_dim'      => count($embedding),
                    'engine'             => $request->has('image_base64') ? 'InsightFace ArcFace R100 (99.83%)' : 'face-api.js ResNet-34',
                    'face_registered_at' => $person->face_registered_at?->format('d/m/Y H:i'),
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Hapus / reset biometrik wajah siswa atau guru.
     */
    public function deleteFace(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:siswa,guru',
            'id'   => 'required|integer',
        ]);

        $type   = $request->input('type');
        $id     = (int)$request->input('id');
        $person = ($type === 'siswa') ? Siswa::findOrFail($id) : Guru::findOrFail($id);

        $person->update([
            'face_embedding'     => null,
            'face_registered_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Face ID {$person->nama} berhasil dihapus.",
        ]);
    }

    /**
     * Status Python InsightFace Service (untuk diagnostik admin).
     */
    public function serviceStatus(): JsonResponse
    {
        $healthy = $this->insightFace->isHealthy();
        return response()->json([
            'python_service' => $healthy ? 'online' : 'offline',
            'engine'         => $healthy ? 'InsightFace ArcFace R100 (99.83%)' : 'Fallback face-api.js ResNet-34',
            'url'            => config('services.insightface.url'),
        ]);
    }
}
