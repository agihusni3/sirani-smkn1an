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
     * Uji coba pengiriman push nyata dari server ke perangkat (dengan delay agar bisa dites saat HP dikunci)
     */
    public function testBackground(Request $request)
    {
        $endpoint = trim($request->input('endpoint') ?: '');
        $nisn = trim($request->input('nisn') ?: '');
        $delay = min(15, max(0, (int) $request->input('delay', 5)));

        $sub = null;
        if ($endpoint) {
            $sub = PushSubscription::where('endpoint', $endpoint)->where('is_active', true)->first();
        }
        if (!$sub && $nisn) {
            $sub = PushSubscription::where('nisn', $nisn)->where('is_active', true)->latest()->first();
        }

        if (!$sub) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Perangkat ini belum terdaftar di database server. Pastikan Anda menekan tombol "Izinkan di HP Ini" atau muat ulang portal saat izin aktif.',
            ], 404);
        }

        if ($delay > 0) {
            sleep($delay);
        }

        $payload = [
            'title'     => '🔔 SIRANI — Tes Notifikasi Background',
            'body'      => 'Hebat! Notifikasi latar belakang berhasil diterima di HP Anda saat aplikasi tertutup (seperti WhatsApp).',
            'url'       => '/cek-presensi',
            'icon'      => '/icons/icon-192.png',
            'badge'     => '/icons/icon-192.png',
            'vibrate'   => [300, 100, 300, 100, 400],
            'timestamp' => now()->timestamp,
        ];

        $success = PushNotificationService::dispatchPush($sub, $payload);

        return response()->json([
            'status'  => $success ? 'success' : 'error',
            'message' => $success ? 'Push notifikasi latar belakang berhasil dikirim ke HP Anda!' : 'Gagal mengirim push ke browser/FCM.',
        ]);
    }

    /**
     * Dapatkan jumlah perangkat HP wali murid yang aktif terhubung
     */
    public function getSubscribersCount()
    {
        $activeCount = PushSubscription::where('is_active', true)->count();
        $totalCount  = PushSubscription::count();

        return response()->json([
            'status'       => 'success',
            'active_count' => $activeCount,
            'total_count'  => $totalCount,
        ]);
    }

    /**
     * Broadcast uji coba / demo ke SELURUH wali murid
     */
    public function broadcastDemo(Request $request)
    {
        $title = trim($request->input('title') ?: '🔔 [DEMO SIRANI] Uji Coba Notifikasi Wali Murid');
        $body  = trim($request->input('body') ?: 'Halo Bapak/Ibu Wali Murid! Notifikasi kehadiran & kedisiplinan siswa SMKN 1 Air Naningan berhasil aktif di HP Anda. Terima kasih.');
        $url   = trim($request->input('url') ?: '/cek-presensi');

        $result = PushNotificationService::broadcastDemoWaliMurid($title, $body, $url);

        return response()->json([
            'status'  => $result['success'] ? 'success' : ($result['total'] === 0 ? 'warning' : 'error'),
            'message' => $result['message'],
            'data'    => $result,
        ]);
    }
}
