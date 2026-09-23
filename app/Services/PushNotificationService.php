<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    protected static ?array $vapidKeys = null;

    /**
     * Dapatkan atau hasilkan VAPID Keypair otomatis (P-256 RFC 8292)
     */
    public static function getVapidKeys(): array
    {
        if (self::$vapidKeys !== null) {
            return self::$vapidKeys;
        }

        $vapidPath = storage_path('app/vapid.json');
        if (File::exists($vapidPath)) {
            $data = json_decode(File::get($vapidPath), true);
            if (!empty($data['publicKey']) && !empty($data['privateKey']) && strlen($data['publicKey']) > 50) {
                self::$vapidKeys = $data;
                return self::$vapidKeys;
            }
        }

        // Generate VAPID keypair RFC-compliant
        try {
            $keys = VAPID::createVapidKeys();
            self::$vapidKeys = [
                'publicKey'  => $keys['publicKey'],
                'privateKey' => $keys['privateKey'],
            ];

            File::ensureDirectoryExists(dirname($vapidPath));
            File::put($vapidPath, json_encode(self::$vapidKeys, JSON_PRETTY_PRINT));
            return self::$vapidKeys;
        } catch (\Throwable $e) {
            Log::error('Gagal generate VAPID keys: ' . $e->getMessage());
            return [
                'publicKey'  => '',
                'privateKey' => '',
            ];
        }
    }

    /**
     * Kirim notifikasi kondisi presensi ke orang tua siswa tertentu berdasarkan NISN
     */
    public static function sendToSiswa(string $nisn, string $title, string $body, ?string $url = null, ?string $icon = null): int
    {
        if (empty($nisn)) return 0;

        // 1. Ambil langganan perangkat aktif yang terikat dengan NISN ini
        $subscriptions = PushSubscription::where('nisn', $nisn)
            ->where('is_active', true)
            ->get();

        // 2. Fallback untuk pengujian/demo: jika belum ada perangkat terdaftar dengan NISN ini,
        // cari perangkat aktif yang belum terikat NISN tertentu (nisn is null)
        if ($subscriptions->isEmpty()) {
            $subscriptions = PushSubscription::whereNull('nisn')
                ->where('is_active', true)
                ->get();
        }

        if ($subscriptions->isEmpty()) {
            Log::info("Push sendToSiswa: Tidak ada perangkat aktif untuk NISN {$nisn}.");
            return 0;
        }

        $payload = [
            'title'     => $title,
            'body'      => $body,
            'url'       => $url ?: '/presensi-siswa/' . urlencode($nisn),
            'icon'      => $icon ?: '/icons/icon-192.png',
            'badge'     => '/icons/icon-192.png',
            'vibrate'   => [500, 200, 500, 200, 500],
            'sound'     => 'chime',
            'tag'       => 'sirani-siswa-' . $nisn . '-' . time(),
            'nisn'      => $nisn,
            'timestamp' => now()->timestamp,
        ];

        $sentCount = 0;
        foreach ($subscriptions as $sub) {
            if (self::dispatchPush($sub, $payload)) {
                $sentCount++;
            }
        }

        Log::info("Push sendToSiswa berhasil dikirim ke {$sentCount} perangkat untuk NISN: {$nisn}");
        return $sentCount;
    }

    /**
     * Broadcast notifikasi uji coba / demo langsung ke SEMUA perangkat wali murid yang aktif
     */
    public static function broadcastDemoWaliMurid(string $title, string $body, ?string $url = null): array
    {
        $subscriptions = PushSubscription::where('is_active', true)->get();
        $totalDevices = $subscriptions->count();

        if ($totalDevices === 0) {
            return [
                'success' => false,
                'total'   => 0,
                'sent'    => 0,
                'failed'  => 0,
                'message' => 'Saat ini belum ada perangkat HP wali murid yang terdaftar di portal. Minta wali murid membuka portal di HP dan mengizinkan notifikasi.',
            ];
        }

        $payload = [
            'title'     => $title,
            'body'      => $body,
            'url'       => $url ?: '/cek-presensi',
            'icon'      => '/icons/icon-192.png',
            'badge'     => '/icons/icon-192.png',
            'vibrate'   => [300, 100, 300, 100, 400],
            'sound'     => 'chime',
            'tag'       => 'sirani-demo-' . time(),
            'timestamp' => now()->timestamp,
        ];

        $sentCount = 0;
        $failCount = 0;

        foreach ($subscriptions as $sub) {
            if (self::dispatchPush($sub, $payload)) {
                $sentCount++;
            } else {
                $failCount++;
            }
        }

        return [
            'success' => $sentCount > 0,
            'total'   => $totalDevices,
            'sent'    => $sentCount,
            'failed'  => $failCount,
            'message' => "Notifikasi demo berhasil terkirim ke {$sentCount} dari {$totalDevices} perangkat HP wali murid!",
        ];
    }

    /**
     * Kirim siaran pengumuman sekolah ke seluruh atau target perangkat orang tua
     */
    public static function broadcastPengumuman(
        string $title,
        string $body,
        ?string $url = null,
        ?string $image = null,
        ?array $targetNisns = null
    ): int {
        $query = PushSubscription::where('is_active', true);

        if (!empty($targetNisns)) {
            $query->where(function ($q) use ($targetNisns) {
                $q->whereIn('nisn', $targetNisns)
                  ->orWhereNull('nisn');
            });
        }

        $subscriptions = $query->get();
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $payload = [
            'title'     => '📢 ' . $title,
            'body'      => $body,
            'url'       => $url ?: '/cek-presensi#pengumuman',
            'icon'      => '/icons/icon-192.png',
            'badge'     => '/icons/icon-192.png',
            'timestamp' => now()->timestamp,
        ];

        if (!empty($image)) {
            $payload['image'] = $image;
        }

        $sentCount = 0;
        foreach ($subscriptions as $sub) {
            if (self::dispatchPush($sub, $payload)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Kirim payload terenkripsi ke browser/Android menggunakan WebPush library
     */
    public static function dispatchPush(PushSubscription $sub, array $payload): bool
    {
        try {
            $vapidKeys = self::getVapidKeys();
            if (empty($vapidKeys['publicKey']) || empty($vapidKeys['privateKey'])) {
                Log::warning('Push notification: VAPID keys belum dikonfigurasi.');
                return false;
            }

            $auth = [
                'VAPID' => [
                    'subject'    => 'mailto:admin@smkn1airnaningan.sch.id',
                    'publicKey'  => $vapidKeys['publicKey'],
                    'privateKey' => $vapidKeys['privateKey'],
                ],
            ];

            $webPush = new WebPush($auth);
            $webPush->setDefaultOptions([
                'TTL'     => 86400,
                'urgency' => 'high',
                'timeout' => 8,
            ]);

            // Buat objek langganan WebPush
            $subscription = Subscription::create([
                'endpoint'        => $sub->endpoint,
                'publicKey'       => $sub->p256dh_key,
                'authToken'       => $sub->auth_token,
                'contentEncoding' => 'aes128gcm',
            ]);

            $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $report = $webPush->sendOneNotification($subscription, $jsonPayload, [
                'TTL'     => 86400,
                'urgency' => 'high',
            ]);

            if ($report->isSuccess()) {
                Log::info("Push berhasil dikirim ke endpoint: " . substr($sub->endpoint, 0, 45) . "...");
                return true;
            }

            // Jika endpoint expired / diblokir pengguna
            if ($report->isSubscriptionExpired()) {
                Log::info("Endpoint push kedaluwarsa, menonaktifkan langganan ID: {$sub->id}");
                $sub->update(['is_active' => false]);
            } else {
                Log::warning("Gagal kirim push: " . $report->getReason());
            }

            return false;
        } catch (\Throwable $e) {
            Log::warning('Exception dispatchPush: ' . $e->getMessage());
            return false;
        }
    }
}
