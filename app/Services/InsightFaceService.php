<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * InsightFaceService
 *
 * Wrapper komunikasi HTTP ke Python FastAPI InsightFace ArcFace R100
 * yang berjalan di http://127.0.0.1:8001
 *
 * Akurasi: 99.83% (LFW Benchmark)
 * Embedding: 512-D L2-Normalized ArcFace Vectors
 */
class InsightFaceService
{
    protected string $baseUrl;
    protected int $timeoutEnroll = 15;
    protected int $timeoutVerify = 8;

    public function __construct()
    {
        $this->baseUrl = config('services.insightface.url', 'http://127.0.0.1:8001');
    }

    /**
     * Cek apakah Python service aktif dan model sudah dimuat.
     */
    public function isHealthy(): bool
    {
        try {
            $res = Http::timeout(3)->get("{$this->baseUrl}/health");
            return $res->ok() && ($res->json('model_loaded') === true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Ekstrak 512-D ArcFace embedding dari gambar wajah (base64).
     * Dipanggil saat perekaman Face ID (KYC Enrollment).
     *
     * @param  string $imageBase64  JPEG/PNG gambar dalam format base64
     * @return array{ success: bool, embedding: float[]|null, det_score: float, elapsed_ms: float, error?: string }
     */
    public function enroll(string $imageBase64): array
    {
        try {
            $res = Http::timeout($this->timeoutEnroll)
                ->post("{$this->baseUrl}/enroll", [
                    'image_base64' => $imageBase64,
                ]);

            if ($res->ok()) {
                return [
                    'success'       => true,
                    'embedding'     => $res->json('embedding'),
                    'embedding_dim' => $res->json('embedding_dim'),
                    'det_score'     => $res->json('det_score'),
                    'elapsed_ms'    => $res->json('elapsed_ms'),
                ];
            }

            $detail = $res->json('detail') ?? 'Gagal mengekstrak embedding.';
            Log::warning("InsightFace enroll error [{$res->status()}]: {$detail}");
            return ['success' => false, 'error' => $detail];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("InsightFace service tidak dapat dijangkau: " . $e->getMessage());
            return ['success' => false, 'error' => 'Python Face Service tidak aktif. Hubungi administrator.'];
        } catch (\Throwable $e) {
            Log::error("InsightFace enroll exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verifikasi wajah dari gambar terhadap daftar kandidat terdaftar.
     * Dipanggil oleh Smart Gate Kiosk setiap frame kamera.
     *
     * @param  string  $imageBase64   Frame kamera dalam base64
     * @param  array   $candidates    Array kandidat: [{ id, type, nama, embedding }]
     * @param  float   $threshold     Cosine similarity minimum (default 0.40)
     * @return array{ success: bool, reason: string, match?: array, elapsed_ms: float }
     */
    public function verify(string $imageBase64, array $candidates, float $threshold = 0.40): array
    {
        try {
            $res = Http::timeout($this->timeoutVerify)
                ->post("{$this->baseUrl}/verify", [
                    'image_base64' => $imageBase64,
                    'candidates'   => $candidates,
                    'threshold'    => $threshold,
                ]);

            if ($res->ok()) {
                return $res->json();
            }

            return ['success' => false, 'reason' => 'service_error', 'match' => null];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning("InsightFace verify: service tidak aktif");
            return ['success' => false, 'reason' => 'service_offline', 'match' => null];
        } catch (\Throwable $e) {
            Log::error("InsightFace verify exception: " . $e->getMessage());
            return ['success' => false, 'reason' => 'exception', 'match' => null];
        }
    }
}
