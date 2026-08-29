<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * Tampilkan daftar audit log dengan filter.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['modul', 'aksi', 'user_id', 'dari', 'sampai', 'cari']);

        $logs = AuditLog::with('user')
            ->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString();

        $modulOptions = AuditLog::select('modul')
            ->distinct()
            ->orderBy('modul')
            ->pluck('modul');

        $aksiOptions = [
            'create', 'update', 'delete', 'transisi', 'koreksi', 'scan', 'login', 'logout',
        ];

        $users = User::orderBy('name')->get();

        return view('audit.index', compact('logs', 'filters', 'modulOptions', 'aksiOptions', 'users'));
    }

    /**
     * Tampilkan detail satu log (beserta diff data lama vs baru).
     */
    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);

        return view('audit.show', compact('log'));
    }
}
