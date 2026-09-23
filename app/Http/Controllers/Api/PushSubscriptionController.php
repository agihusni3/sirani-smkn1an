<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Dapatkan Public Key VAPID untuk inisialisasi di frontend browser/APK
     */
    public function getPublicKey()
    {
        $keys = PushNotificationService::getVapidKeys();
        return response()->json([
            'status'    => 'success',
            'publicKey' => $keys['publicKey'] ?? '',
        ]);
    }

    /**
     * Daftarkan atau perbarui langganan notifikasi perangkat
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'nisn'     => 'nullable|string|max:30',
        ]);

        $endpoint = trim($request->input('endpoint'));
        $nisn = trim($request->input('nisn') ?: '');
        $p256dh = $request->input('p256dh') ?: $request->input('keys.p256dh');
        $auth = $request->input('auth') ?: $request->input('keys.auth');
        $userAgent = $request->header('User-Agent') ?: 'Android APK';

        // Simpan atau update
        $sub = PushSubscription::updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'nisn'        => $nisn ?: null,
                'p256dh_key'  => $p256dh,
                'auth_token'  => $auth,
                'user_agent'  => substr($userAgent, 0, 255),
                'device_type' => str_contains(strtolower($userAgent), 'android') ? 'android' : 'browser',
                'is_active'   => true,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Perangkat berhasil didaftarkan untuk notifikasi!',
            'data'    => [
                'id'   => $sub->id,
                'nisn' => $sub->nisn,
            ],
        ]);
    }

    /**
     * Hapus pendaftaran perangkat saat pengguna mematikan notifikasi
     */
    public function unsubscribe(Request $request)
    {
        $endpoint = trim($request->input('endpoint') ?: '');
        if ($endpoint) {
            PushSubscription::where('endpoint', $endpoint)->update(['is_active' => false]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Langganan notifikasi dinonaktifkan.',
        ]);
    }

    /**
     * Uji kirim push notification ke NISN tertentu (untuk pengujian instan)
     */
    public function sendTest(Request $request)
    {
        $nisn = trim($request->input('nisn') ?: '0106605523');

        $subs = PushSubscription::where('nisn', $nisn)->where('is_active', true)->get();
        if ($subs->isEmpty()) {
            return response()->json([
                'status'      => 'warning',
                'message'     => "Belum ada HP/browser yang mengaktifkan notifikasi untuk NISN {$nisn}. Silakan klik 'Aktifkan Notifikasi' di HP/browser terlebih dahulu.",
                'subscribers' => 0,
                'sent'        => 0,
            ]);
        }

        $sent = PushNotificationService::sendToSiswa(
            $nisn,
            "🧪 Uji Notifikasi SIRANI (NISN: {$nisn})",
            "Halo! Notifikasi presensi & kedisiplinan SIRANI SMKN 1 Air Naningan aktif dan terhubung sempurna di HP Anda.",
            "/presensi-siswa/{$nisn}"
        );

        return response()->json([
            'status'      => 'success',
            'message'     => "Notifikasi uji coba berhasil dikirim ke {$sent} perangkat aktif!",
            'subscribers' => $subs->count(),
            'sent'        => $sent,
        ]);
    }
}
