<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi_ortus', function (Blueprint $table) {
            $table->string('foto_diskusi')->nullable()->after('pesan');
            $table->string('dokumen_pendukung')->nullable()->after('foto_diskusi');
            $table->text('catatan_hasil_diskusi')->nullable()->after('dokumen_pendukung');
            $table->timestamp('waktu_diskusi')->nullable()->after('catatan_hasil_diskusi');
            $table->string('nama_wali_hadir')->nullable()->after('waktu_diskusi');
            $table->string('status_pembinaan', 50)->default('menunggu_kehadiran')->after('nama_wali_hadir');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi_ortus', function (Blueprint $table) {
            $table->dropColumn([
                'foto_diskusi',
                'dokumen_pendukung',
                'catatan_hasil_diskusi',
                'waktu_diskusi',
                'nama_wali_hadir',
                'status_pembinaan',
            ]);
        });
    }
};
