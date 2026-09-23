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

        $subscriptions = PushSubscription::where('nisn', $nisn)
            ->where('is_active', true)
            ->get();

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $payload = [
            'title'     => $title,
            'body'      => $body,
            'url'       => $url ?: '/presensi-siswa/' . urlencode($nisn),
            'icon'      => $icon ?: '/icons/icon-192.png',
            'badge'     => '/icons/icon-192.png',
            'nisn'      => $nisn,
            'timestamp' => now()->timestamp,
        ];

        $sentCount = 0;
        foreach ($subscriptions as $sub) {
            if (self::dispatchPush($sub, $payload)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
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
    protected static function dispatchPush(PushSubscription $sub, array $payload): bool
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
            $report = $webPush->sendOneNotification($subscription, $jsonPayload);

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
