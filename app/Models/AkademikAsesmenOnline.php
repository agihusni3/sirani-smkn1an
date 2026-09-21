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
        'max_toleransi_keluar'
    ];

    protected $casts = [
        'dibuka_pada' => 'datetime',
        'ditutup_pada' => 'datetime',
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
    ];

    public function distribusi(): BelongsTo { return $this->belongsTo(AkademikDistribusiMengajar::class, 'distribusi_id'); }
    public function soals(): HasMany { return $this->hasMany(AkademikAsesmenSoal::class, 'asesmen_id')->orderBy('nomor'); }
    public function hasils(): HasMany { return $this->hasMany(AkademikAsesmenHasil::class, 'asesmen_id'); }

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

    public static function buatToken(): string {
        return strtoupper(\Illuminate\Support\Str::random(6));
    }
}
