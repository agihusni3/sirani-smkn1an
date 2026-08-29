<?php

namespace App\Services;

use App\Models\NotifikasiOrtu;
use App\Models\PengaturanNotifikasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppNotificationService
{
    /**
     * Kirim notifikasi WhatsApp yang telah diverifikasi ke nomor tujuan.
     */
    public function kirim(NotifikasiOrtu $notifikasi): array
    {
        $setting = PengaturanNotifikasi::getPengaturan();

        // 1. Cek nomor tujuan
        $noWa = NotifikasiOrtu::formatNomorWa($notifikasi->no_tujuan);
        if (!$noWa || strlen($noWa) < 9) {
            $notifikasi->update([
                'status'        => 'gagal',
                'catatan_error' => 'Nomor WhatsApp tidak valid atau kosong: ' . ($notifikasi->no_tujuan ?? '(kosong)'),
            ]);

            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tujuan tidak valid.',
            ];
        }

        // 2. Jika sistem dalam Mode Simulasi / Gateway Nonaktif (Safe Mode)
        if (!$setting->is_active || $setting->wa_provider === 'simulasi' || empty($setting->wa_api_token)) {
            $notifikasi->update([
                'status'        => 'terkirim',
                'waktu_kirim'   => now(),
                'catatan_error' => '[MODE SIMULASI] Pesan berhasil diverifikasi dan disimulasikan terkirim ke ' . $noWa,
            ]);

            Log::info("[SIRANI WA SIMULASI] Pesan ke {$noWa}: {$notifikasi->judul}\n{$notifikasi->pesan}");

            return [
                'success' => true,
                'mode'    => 'simulasi',
                'message' => 'Pesan berhasil disimulasikan terkirim ke ' . $noWa,
            ];
        }

        // 3. Pengiriman Live via Gateway Provider (Fonnte / Wablas / Generic API)
        try {
            if ($setting->wa_provider === 'fonnte') {
                $endpoint = $setting->wa_endpoint_url ?: 'https://api.fonnte.com/send';
                $response = Http::withHeaders([
                    'Authorization' => $setting->wa_api_token,
                ])->connectTimeout(2)->timeout(3)->post($endpoint, [
                    'target'  => $noWa,
                    'message' => $notifikasi->pesan,
                ]);

                if ($response->successful()) {
                    $notifikasi->update([
                        'status'        => 'terkirim',
                        'waktu_kirim'   => now(),
                        'catatan_error' => 'Terkirim via Fonnte: ' . $response->body(),
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Pesan WhatsApp berhasil dikirim ke orang tua.',
                    ];
                }

                $notifikasi->update([
                    'status'        => 'gagal',
                    'catatan_error' => 'Gagal Fonnte (' . $response->status() . '): ' . $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Gateway Fonnte merespons error: ' . $response->body(),
                ];
            }

            if ($setting->wa_provider === 'wablas') {
                $endpoint = $setting->wa_endpoint_url ?: 'https://kudus.wablas.com/api/send-message';
                $response = Http::withHeaders([
                    'Authorization' => $setting->wa_api_token,
                ])->connectTimeout(2)->timeout(3)->post($endpoint, [
                    'phone'   => $noWa,
                    'message' => $notifikasi->pesan,
                ]);

                if ($response->successful()) {
                    $notifikasi->update([
                        'status'        => 'terkirim',
                        'waktu_kirim'   => now(),
                        'catatan_error' => 'Terkirim via Wablas: ' . $response->body(),
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Pesan WhatsApp berhasil dikirim ke orang tua.',
                    ];
                }

                $notifikasi->update([
                    'status'        => 'gagal',
                    'catatan_error' => 'Gagal Wablas (' . $response->status() . '): ' . $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Gateway Wablas merespons error.',
                ];
            }

            // Generic JSON API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $setting->wa_api_token,
            ])->connectTimeout(2)->timeout(3)->post($setting->wa_endpoint_url, [
                'to'      => $noWa,
                'message' => $notifikasi->pesan,
            ]);

            if ($response->successful()) {
                $notifikasi->update([
                    'status'      => 'terkirim',
                    'waktu_kirim' => now(),
                ]);

                return ['success' => true, 'message' => 'Pesan terkirim.'];
            }

            $notifikasi->update([
                'status'        => 'gagal',
                'catatan_error' => 'Error Generic API: ' . $response->body(),
            ]);

            return ['success' => false, 'message' => 'Gagal mengirim pesan.'];
        } catch (Throwable $e) {
            $notifikasi->update([
                'status'        => 'gagal',
                'catatan_error' => 'Exception: ' . $e->getMessage(),
            ]);

            Log::error("[SIRANI WA ERROR] Exception saat kirim WA: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kendala koneksi ke WhatsApp Gateway: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim pesan langsung (Direct Broadcast) tanpa objek NotifikasiOrtu.
     */
    public function kirimDirect(string $noTujuan, string $pesan, string $judul = 'PENGUMUMAN SEKOLAH'): array
    {
        $setting = PengaturanNotifikasi::getPengaturan();
        $noWa = NotifikasiOrtu::formatNomorWa($noTujuan);

        if (!$noWa || strlen($noWa) < 9) {
            return ['success' => false, 'message' => 'Nomor WhatsApp tidak valid.'];
        }

        if (!$setting->is_active || $setting->wa_provider === 'simulasi' || empty($setting->wa_api_token)) {
            Log::info("[SIRANI WA BROADCAST SIMULASI] Ke {$noWa}: {$judul}\n{$pesan}");
            return ['success' => true, 'mode' => 'simulasi', 'message' => 'Simulasi kirim berhasil.'];
        }

        try {
            if ($setting->wa_provider === 'fonnte') {
                $endpoint = $setting->wa_endpoint_url ?: 'https://api.fonnte.com/send';
                $response = Http::withHeaders([
                    'Authorization' => $setting->wa_api_token,
                ])->connectTimeout(2)->timeout(3)->post($endpoint, [
                    'target'  => $noWa,
                    'message' => $pesan,
                ]);

                return ['success' => $response->successful(), 'response' => $response->body()];
            }

            if ($setting->wa_provider === 'wablas') {
                $endpoint = $setting->wa_endpoint_url ?: 'https://kudus.wablas.com/api/send-message';
                $response = Http::withHeaders([
                    'Authorization' => $setting->wa_api_token,
                ])->connectTimeout(2)->timeout(3)->post($endpoint, [
                    'phone'   => $noWa,
                    'message' => $pesan,
                ]);

                return ['success' => $response->successful(), 'response' => $response->body()];
            }

            return ['success' => true, 'message' => 'Terkirim via default gateway.'];
        } catch (Throwable $e) {
            Log::error("[SIRANI WA BROADCAST ERROR] " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
