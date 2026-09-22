<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkademikAsesmenOnline extends Model {
    protected $table = 'akademik_asesmen_onlines';
    protected $fillable = [
        'distribusi_id', 'judul', 'deskripsi', 'tujuan_pembelajaran', 'jenis', 'semester',
        'durasi_menit', 'dibuka_pada', 'ditutup_pada', 'acak_soal', 'acak_opsi',
        'tampilkan_nilai', 'tampilkan_pembahasan', 'is_active', 'passing_grade',
        'token_ujian', 'anti_cheat_mode', 'wajib_fullscreen', 'blokir_copy_paste',
        'max_toleransi_keluar', 'status_validasi', 'catatan_validasi', 'divalidasi_oleh',
        'divalidasi_pada', 'target_tipe', 'target_siswa_ids'
    ];

    protected $casts = [
        'dibuka_pada' => 'datetime',
        'ditutup_pada' => 'datetime',
        'divalidasi_pada' => 'datetime',
        'acak_soal' => 'boolean',
        'acak_opsi' => 'boolean',
        'tampilkan_nilai' => 'boolean',
        'tampilkan_pembahasan' => 'boolean',
        'is_active' => 'boolean',
        'anti_cheat_mode' => 'boolean',
        'wajib_fullscreen' => 'boolean',
        'blokir_copy_paste' => 'boolean',
        'max_toleransi_keluar' => 'integer',
        'passing_grade' => 'integer',
        'target_siswa_ids' => 'array',
    ];

    public function distribusi(): BelongsTo { return $this->belongsTo(AkademikDistribusiMengajar::class, 'distribusi_id'); }
    public function soals(): HasMany { return $this->hasMany(AkademikAsesmenSoal::class, 'asesmen_id')->orderBy('nomor'); }
    public function hasils(): HasMany { return $this->hasMany(AkademikAsesmenHasil::class, 'asesmen_id'); }
    public function validator(): BelongsTo { return $this->belongsTo(User::class, 'divalidasi_oleh'); }

    public function getTotalSoalAttribute(): int { return $this->soals()->count(); }
    public function getTotalPesertaAttribute(): int { return $this->hasils()->where('is_selesai', true)->count(); }
    public function isOpen(): bool { $now = now(); return $this->is_active && (!$this->dibuka_pada || $now->gte($this->dibuka_pada)) && (!$this->ditutup_pada || $now->lte($this->ditutup_pada)); }
    public function getJenisLabelAttribute(): string {
        return match($this->jenis) {
            'kuis' => 'Kuis Harian',
            'ulangan_harian' => 'Ulangan Harian (UH)',
            'pts' => 'PTS (Tengah Semester)',
            'pas' => 'PAS (Akhir Semester)',
            'tugas' => 'Tugas Daring',
            default => ucwords(str_replace('_', ' ', $this->jenis))
        };
    }

    /**
     * Audit otomatis kelayakan teknis dan mutu butir soal
     */
    public function cekKelayakanSoal(): array
    {
        $soals = $this->soals;
        $errors = [];
        $warnings = [];
        $totalBobot = 0;

        if ($soals->isEmpty()) {
            $errors[] = 'Paket asesmen belum memiliki butir soal (minimal 1 soal).';
            return [
                'is_valid' => false,
                'total_soal' => 0,
                'total_bobot' => 0,
                'errors' => $errors,
                'warnings' => $warnings,
            ];
        }

        foreach ($soals as $idx => $s) {
            $no = $s->nomor ?? ($idx + 1);
            $totalBobot += (int) $s->bobot;

            // 1. Teks pertanyaan
            if (empty(trim(strip_tags($s->pertanyaan)))) {
                $errors[] = "Soal no. {$no}: Teks pertanyaan masih kosong.";
            }

            // 2. Kunci Jawaban
            if (empty($s->kunci_jawaban)) {
                $errors[] = "Soal no. {$no}: Kunci jawaban belum ditentukan.";
            }

            // 3. Pilihan Ganda Checks
            if ($s->tipe === 'pilihan_ganda') {
                if (empty(trim($s->opsi_a ?? '')) || empty(trim($s->opsi_b ?? ''))) {
                    $errors[] = "Soal no. {$no}: Minimal harus mengisi pilihan jawaban A dan B.";
                }

                $kunci = strtoupper(trim($s->kunci_jawaban));
                $kunciField = 'opsi_' . strtolower($kunci);
                if (empty(trim($s->{$kunciField} ?? ''))) {
                    $errors[] = "Soal no. {$no}: Kunci jawaban ({$kunci}) dipilih, tetapi teks pilihan {$kunci} masih kosong!";
                }

                // Cek opsi duplikat
                $opsiNonEmpty = array_filter([
                    'A' => trim($s->opsi_a ?? ''),
                    'B' => trim($s->opsi_b ?? ''),
                    'C' => trim($s->opsi_c ?? ''),
                    'D' => trim($s->opsi_d ?? ''),
                    'E' => trim($s->opsi_e ?? ''),
                ]);
                if (count($opsiNonEmpty) !== count(array_unique($opsiNonEmpty))) {
                    $warnings[] = "Soal no. {$no}: Terdapat opsi jawaban dengan teks yang sama persis (duplikat).";
                }

                if (count($opsiNonEmpty) < 4) {
                    $warnings[] = "Soal no. {$no}: Pilihan jawaban hanya sampai " . array_key_last($opsiNonEmpty) . " (Standar SMK biasanya A sampai D/E).";
                }
            }
        }

        return [
            'is_valid' => count($errors) === 0,
            'total_soal' => $soals->count(),
            'total_bobot' => $totalBobot,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    public static function buatToken(): string {
        return strtoupper(\Illuminate\Support\Str::random(6));
    }
}
