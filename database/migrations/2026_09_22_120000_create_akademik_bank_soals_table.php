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
        if (!Schema::hasTable('akademik_bank_soals')) {
            Schema::create('akademik_bank_soals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mata_pelajaran_id')->nullable()->index();
                $table->unsignedBigInteger('guru_id')->nullable()->index();
                $table->string('tipe')->default('pilihan_ganda');
                $table->string('topik')->nullable();
                $table->text('pertanyaan');
                $table->string('gambar_url')->nullable();
                $table->text('opsi_a')->nullable();
                $table->text('opsi_b')->nullable();
                $table->text('opsi_c')->nullable();
                $table->text('opsi_d')->nullable();
                $table->text('opsi_e')->nullable();
                $table->string('kunci_jawaban', 10);
                $table->integer('bobot')->default(1);
                $table->text('pembahasan')->nullable();
                $table->timestamps();

                $table->foreign('mata_pelajaran_id')->references('id')->on('akademik_mata_pelajarans')->onDelete('cascade');
                $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('set null');
            });
        }

        // Backfill soal yang sudah pernah dibuat di asesmen ke dalam bank soal
        try {
            $existingSoals = DB::table('akademik_asesmen_soals')
                ->join('akademik_asesmen_onlines', 'akademik_asesmen_soals.asesmen_id', '=', 'akademik_asesmen_onlines.id')
                ->leftJoin('akademik_distribusi_mengajars', 'akademik_asesmen_onlines.distribusi_id', '=', 'akademik_distribusi_mengajars.id')
                ->select(
                    'akademik_distribusi_mengajars.mata_pelajaran_id',
                    'akademik_distribusi_mengajars.guru_id',
                    'akademik_asesmen_soals.tipe',
                    'akademik_asesmen_soals.pertanyaan',
                    'akademik_asesmen_soals.gambar_url',
                    'akademik_asesmen_soals.opsi_a',
                    'akademik_asesmen_soals.opsi_b',
                    'akademik_asesmen_soals.opsi_c',
                    'akademik_asesmen_soals.opsi_d',
                    'akademik_asesmen_soals.opsi_e',
                    'akademik_asesmen_soals.kunci_jawaban',
                    'akademik_asesmen_soals.bobot',
                    'akademik_asesmen_soals.pembahasan',
                    'akademik_asesmen_soals.created_at',
                    'akademik_asesmen_soals.updated_at'
                )
                ->get();

            foreach ($existingSoals as $s) {
                if (!empty($s->pertanyaan) && !empty($s->mata_pelajaran_id)) {
                    $exists = DB::table('akademik_bank_soals')
                        ->where('mata_pelajaran_id', $s->mata_pelajaran_id)
                        ->where('pertanyaan', $s->pertanyaan)
                        ->exists();

                    if (!$exists) {
                        DB::table('akademik_bank_soals')->insert([
                            'mata_pelajaran_id' => $s->mata_pelajaran_id,
                            'guru_id' => $s->guru_id,
                            'tipe' => $s->tipe ?? 'pilihan_ganda',
                            'topik' => null,
                            'pertanyaan' => $s->pertanyaan,
                            'gambar_url' => $s->gambar_url,
                            'opsi_a' => $s->opsi_a,
                            'opsi_b' => $s->opsi_b,
                            'opsi_c' => $s->opsi_c,
                            'opsi_d' => $s->opsi_d,
                            'opsi_e' => $s->opsi_e,
                            'kunci_jawaban' => $s->kunci_jawaban,
                            'bobot' => $s->bobot ?? 1,
                            'pembahasan' => $s->pembahasan,
                            'created_at' => $s->created_at ?? now(),
                            'updated_at' => $s->updated_at ?? now(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika migrasi dijalankan pada database kosong
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akademik_bank_soals');
    }
};
