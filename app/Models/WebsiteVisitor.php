<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WebsiteVisitor extends Model
{
    protected $fillable = [
        'ip_hash',
        'url',
        'route_name',
        'device_type',
        'browser',
        'referer',
        'visited_date',
    ];

    protected $casts = [
        'visited_date' => 'date',
    ];

    /**
     * Scope untuk kunjungan hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visited_date', Carbon::today()->toDateString());
    }

    /**
     * Scope untuk rentang tanggal
     */
    public function scopeRange($query, $startDate, $endDate)
    {
        return $query->whereDate('visited_date', '>=', $startDate)
            ->whereDate('visited_date', '<=', $endDate);
    }

    /**
     * Ringkasan Metrik Utama
     */
    public static function getSummaryStats(): array
    {
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();

        $todayViews = self::whereDate('visited_date', $today)->count();
        $todayUnique = self::whereDate('visited_date', $today)->distinct('ip_hash')->count('ip_hash');

        $monthViews = self::whereDate('visited_date', '>=', $startOfMonth)->count();
        $monthUnique = self::whereDate('visited_date', '>=', $startOfMonth)->distinct('ip_hash')->count('ip_hash');

        $totalViews = self::count();
        $totalUnique = self::distinct('ip_hash')->count('ip_hash');

        return [
            'today_views'   => $todayViews,
            'today_unique'  => $todayUnique,
            'month_views'   => $monthViews,
            'month_unique'  => $monthUnique,
            'total_views'   => $totalViews,
            'total_unique'  => $totalUnique,
        ];
    }

    /**
     * Data Tren Harian untuk Chart.js (Line Chart)
     */
    public static function getDailyTrend(int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days - 1)->toDateString();
        $endDate = Carbon::today()->toDateString();

        // Ambil data agregasi dari DB
        $records = self::whereDate('visited_date', '>=', $startDate)
            ->whereDate('visited_date', '<=', $endDate)
            ->select(
                DB::raw('DATE(visited_date) as visited_day'),
                DB::raw('COUNT(*) as total_views'),
                DB::raw('COUNT(DISTINCT ip_hash) as unique_visitors')
            )
            ->groupBy('visited_day')
            ->orderBy('visited_day', 'asc')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->visited_day)->toDateString();
            });

        // Bentuk label lengkap berurutan per hari (termasuk hari yang 0 kunjungan)
        $labels = [];
        $viewsData = [];
        $uniqueData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->toDateString();
            $label = $date->translatedFormat('d M');

            $labels[] = $label;
            if (isset($records[$dateStr])) {
                $viewsData[] = (int) $records[$dateStr]->total_views;
                $uniqueData[] = (int) $records[$dateStr]->unique_visitors;
            } else {
                $viewsData[] = 0;
                $uniqueData[] = 0;
            }
        }

        return [
            'labels'     => $labels,
            'views'      => $viewsData,
            'uniques'    => $uniqueData,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ];
    }

    /**
     * Distribusi Perangkat (Donut Chart)
     */
    public static function getDeviceDistribution(int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days - 1)->toDateString();

        $rows = self::whereDate('visited_date', '>=', $startDate)
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        $mobile = $rows['mobile'] ?? 0;
        $desktop = $rows['desktop'] ?? 0;
        $tablet = $rows['tablet'] ?? 0;

        $total = $mobile + $desktop + $tablet;

        return [
            'mobile'      => $mobile,
            'desktop'     => $desktop,
            'tablet'      => $tablet,
            'total'       => $total,
            'mobile_pct'  => $total > 0 ? round(($mobile / $total) * 100, 1) : 0,
            'desktop_pct' => $total > 0 ? round(($desktop / $total) * 100, 1) : 0,
            'tablet_pct'  => $total > 0 ? round(($tablet / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Halaman Paling Sering Dikunjungi (Top Visited Pages)
     */
    public static function getTopPages(int $limit = 10, int $days = 30): array
    {
        $startDate = Carbon::today()->subDays($days - 1)->toDateString();

        return self::whereDate('visited_date', '>=', $startDate)
            ->select('url', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT ip_hash) as uniques'))
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
