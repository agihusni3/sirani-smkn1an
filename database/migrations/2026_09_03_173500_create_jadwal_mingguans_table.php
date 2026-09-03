<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_mingguans', function (Blueprint $table) {
            $table->id();
            $table->string('hari', 20)->unique(); // Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu
            $table->time('jam_masuk_toleransi')->default('07:15:00');
            $table->time('jam_pulang_mulai')->default('15:30:00');
            $table->time('jam_tutup_gerbang')->default('17:00:00');
            $table->boolean('is_aktif')->default(true);
            $table->string('keterangan')->nullable();
            $table->string('diubah_oleh')->nullable();
            $table->timestamps();
        });

        // Isi data awal standar SMKN 1 Air Naningan
        $defaultJadwals = [
            [
                'hari' => 'Senin',
                'jam_masuk_toleransi' => '07:15:00',
                'jam_pulang_mulai' => '15:30:00',
                'jam_tutup_gerbang' => '17:00:00',
                'is_aktif' => true,
                'keterangan' => 'Jadwal Reguler & Upacara Bendera',
                'diubah_oleh' => 'Sistem Awal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hari' => 'Selasa',
                'jam_masuk_toleransi' => '07:15:00',
                'jam_pulang_mulai' => '15:30:00',
                'jam_tutup_gerbang' => '17:00:00',
                'is_aktif' => true,
                'keterangan' => 'Jadwal Reguler KBM',
                'diubah_oleh' => 'Sistem Awal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hari' => 'Rabu',
                'jam_masuk_toleransi' => '07:15:00',
                'jam_pulang_mulai' => '15:30:00',
                'jam_tutup_gerbang' => '17:00:00',
                'is_aktif' => true,
                'keterangan' => 'Jadwal Reguler KBM',
                'diubah_oleh' => 'Sistem Awal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hari' => 'Kamis',
                'jam_masuk_toleransi' => '07:15:00',
                'jam_pulang_mulai' => '15:30:00',
                'jam_tutup_gerbang' => '17:00:00',
                'is_aktif' => true,
                'keterangan' => 'Jadwal Reguler KBM',
                'diubah_oleh' => 'Sistem Awal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hari' => 'Jumat',
                'jam_masuk_toleransi' => '07:15:00',
                'jam_pulang_mulai' => '11:30:00',
                'jam_tutup_gerbang' => '17:00:00',
                'is_aktif' => true,
                'keterangan' => 'Jadwal Khusus Hari Jumat (Pulang Awal)',
                'diubah_oleh' => 'Sistem Awal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('jadwal_mingguans')->insert($defaultJadwals);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_mingguans');
    }
};
