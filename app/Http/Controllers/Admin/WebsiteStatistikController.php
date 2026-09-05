<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteVisitor;
use Illuminate\Http\Request;

class WebsiteStatistikController extends Controller
{
    /**
     * Tampilkan halaman dasbor grafik & analitik pengunjung website
     * Hanya dapat diakses oleh Administrator.
     */
    public function index(Request $request)
    {
        $days = (int) $request->get('days', 30);
        if (!in_array($days, [7, 14, 30, 60, 90])) {
            $days = 30;
        }

        $summary = WebsiteVisitor::getSummaryStats();
        $trend = WebsiteVisitor::getDailyTrend($days);
        $devices = WebsiteVisitor::getDeviceDistribution($days);
        $topPages = WebsiteVisitor::getTopPages(10, $days);

        return view('web.admin.statistik.index', compact(
            'summary',
            'trend',
            'devices',
            'topPages',
            'days'
        ));
    }
}
