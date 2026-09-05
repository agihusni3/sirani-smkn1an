<?php

namespace App\Http\Middleware;

use App\Models\WebsiteVisitor;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackWebsiteVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya track request GET dengan status 200 OK
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $this->recordVisitor($request);
        }

        return $response;
    }

    /**
     * Catat kunjungan ke database
     */
    protected function recordVisitor(Request $request): void
    {
        try {
            // Jangan track AJAX, prefetch, atau asset statis
            if ($request->ajax() || $request->header('Sec-Purpose') === 'prefetch') {
                return;
            }

            $path = '/' . ltrim($request->path(), '/');

            // Abaikan rute admin dan dashboard internal
            if (
                $request->is('admin*') ||
                $request->is('portal*') ||
                $request->is('dashboard*') ||
                $request->is('hub*') ||
                $request->is('smart-gate*') ||
                $request->is('login*') ||
                $request->is('logout*') ||
                $request->is('api*') ||
                $request->is('up')
            ) {
                return;
            }

            $userAgent = (string) $request->header('User-Agent', '');

            // Abaikan bot mesin pencari / web crawler otomatis
            if (preg_match('/(bot|crawl|spider|slurp|facebookexternalhit|whatsapp|googlebot|bingbot)/i', $userAgent)) {
                return;
            }

            // Anonymized IP hash
            $ip = $request->ip() ?? '127.0.0.1';
            $ipHash = hash('sha256', $ip . '_' . config('app.key'));

            // Deteksi jenis perangkat
            $deviceType = 'desktop';
            if (preg_match('/(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle)/i', $userAgent)) {
                $deviceType = 'tablet';
            } elseif (preg_match('/(mobile|iphone|ipod|android|blackberry|iemobile|opera mini)/i', $userAgent)) {
                $deviceType = 'mobile';
            }

            // Deteksi browser sederhana
            $browser = 'Lainnya';
            if (preg_match('/edg/i', $userAgent)) {
                $browser = 'Edge';
            } elseif (preg_match('/chrome|crios/i', $userAgent)) {
                $browser = 'Chrome';
            } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
                $browser = 'Firefox';
            } elseif (preg_match('/safari/i', $userAgent)) {
                $browser = 'Safari';
            } elseif (preg_match('/opr\//i', $userAgent)) {
                $browser = 'Opera';
            }

            $referer = $request->header('referer');
            $cleanReferer = $referer ? substr(filter_var($referer, FILTER_SANITIZE_URL), 0, 255) : null;

            WebsiteVisitor::create([
                'ip_hash'      => $ipHash,
                'url'          => substr($path, 0, 255),
                'route_name'   => $request->route()?->getName(),
                'device_type'  => $deviceType,
                'browser'      => $browser,
                'referer'      => $cleanReferer,
                'visited_date' => Carbon::today()->toDateString(),
            ]);
        } catch (\Throwable $e) {
            // Silently catch error agar tidak pernah menggagalkan response publik
        }
    }
}
