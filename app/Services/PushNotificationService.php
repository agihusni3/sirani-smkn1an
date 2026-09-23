<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    protected static ?array $vapidKeys = null;

    /**
     * Dapatkan atau hasilkan VAPID Keypair otomatis (P-256)
     */
    public static function getVapidKeys(): array
    {
        if (self::$vapidKeys !== null) {
            return self::$vapidKeys;
        }

        $vapidPath = storage_path('app/vapid.json');
        if (File::exists($vapidPath)) {
            $data = json_decode(File::get($vapidPath), true);
            if (!empty($data['publicKey']) && !empty($data['privateKey'])) {
                self::$vapidKeys = $data;
                return self::$vapidKeys;
            }
        }

        // Generate VAPID keypair baru jika belum ada
        $config = [
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ];
        $res = openssl_pkey_new($config);
        if ($res) {
            openssl_pkey_export($res, $privatePem);
            $details = openssl_pkey_get_details($res);
            $ec = $details['ec'];
            
            // Format uncompressed public key (0x04 + x + y)
            $rawPublicKey = "\x04" . $ec['x'] . $ec['y'];
            $publicKey = self::base64UrlEncode($rawPublicKey);
            $privateKey = self::base64UrlEncode($ec['d']);

            self::$vapidKeys = [
                'publicKey'  => $publicKey,
                'privateKey' => $privateKey,
                'pem'        => $privatePem,
            ];

            // Simpan ke storage/app/vapid.json
            File::ensureDirectoryExists(dirname($vapidPath));
            File::put($vapidPath, json_encode(self::$vapidKeys, JSON_PRETTY_PRINT));

            return self::$vapidKeys;
        }

        // Fallback default jika openssl CLI berbeda
        $defaultKeys = [
            'publicKey'  => 'BCv5qD3_SIRANI_SMKN1AN_PUBLIC_KEY_' . bin2hex(random_bytes(16)),
            'privateKey' => bin2hex(random_bytes(32)),
        ];
        self::$vapidKeys = $defaultKeys;
        return self::$vapidKeys;
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
            'url'       => $url ?: '/monitoring-absen?keyword=' . urlencode($nisn),
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
     * Kirim siaran pengumuman sekolah ke seluruh perangkat orang tua yang terpasang aplikasi
     */
    public static function broadcastPengumuman(string $title, string $body, ?string $url = null, ?string $icon = null): int
    {
        $subscriptions = PushSubscription::where('is_active', true)->get();
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $payload = [
            'title'     => '📢 ' . $title,
            'body'      => $body,
            'url'       => $url ?: '/pengumuman',
            'icon'      => $icon ?: '/icons/icon-192.png',
            'badge'     => '/icons/icon-192.png',
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
     * Kirim payload ke endpoint push browser/Android via HTTP POST
     */
    protected static function dispatchPush(PushSubscription $sub, array $payload): bool
    {
        try {
            $endpoint = $sub->endpoint;
            $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $headers = [
                'TTL: 86400',
                'Urgency: high',
                'Content-Type: application/json',
            ];

            // Jika endpoint FCM Google lama
            if (str_contains($endpoint, 'fcm.googleapis.com/fcm/send')) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Jika endpoint sudah tidak valid / aplikasi di-uninstall
            if ($httpCode === 404 || $httpCode === 410) {
                $sub->update(['is_active' => false]);
                return false;
            }

            return ($httpCode >= 200 && $httpCode < 300);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim push notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper URL-safe Base64
     */
    protected static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
