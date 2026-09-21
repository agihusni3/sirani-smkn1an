<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkademikMataPelajaran extends Model {
    protected $table = 'akademik_mata_pelajarans';
    protected $fillable = ['tahun_ajaran_id','jurusan_id','kode_mapel','nama_mapel','jenis','fase','tingkat','jumlah_jam_per_minggu','deskripsi_cp','is_active','resource_key','singkatan_mapel'];
    protected $casts = ['is_active' => 'boolean'];

    /**
     * Daftar resource terbatas yang tersedia di sekolah.
     * Tambahkan resource baru di sini jika ada lab/ruangan baru.
     */
    const RESOURCES = [
        'LAB_KOMPUTER' => 'Lab Komputer',
        'LAB_APHP'     => 'Lab APHP',
        'BENGKEL_TSM'  => 'Bengkel TSM',
    ];

    public function tahunAjaran(): BelongsTo { return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id'); }
    public function jurusan(): BelongsTo { return $this->belongsTo(Jurusan::class, 'jurusan_id'); }
    public function distribusiMengajars(): HasMany { return $this->hasMany(AkademikDistribusiMengajar::class, 'mata_pelajaran_id'); }
    public function gurus() {
        return $this->belongsToMany(Guru::class, 'akademik_distribusi_mengajars', 'mata_pelajaran_id', 'guru_id')->distinct();
    }

    public function getJenisLabelAttribute(): string {
        return match($this->jenis) {
            'umum' => 'Umum',
            'kejuruan' => 'Kejuruan',
            'pilihan' => 'Mapel Pilihan',
            'p5bk' => 'P5BK',
            'pkl' => 'PKL',
            default => ucfirst($this->jenis),
        };
    }
    public function getFaseLabelAttribute(): string {
        $fase = strtoupper(trim($this->fase ?? ''));
        if ($fase === 'E' && !in_array('XI', $this->tingkat_array) && !in_array('XII', $this->tingkat_array)) {
            return 'Fase E (Kelas X)';
        }
        if ($fase === 'F' && !in_array('X', $this->tingkat_array)) {
            return 'Fase F (Kelas XI–XII)';
        }
        if (str_contains($fase, 'E') && str_contains($fase, 'F')) {
            return 'Fase E & F (Kelas X–XII)';
        }
        if (in_array('X', $this->tingkat_array) && (in_array('XI', $this->tingkat_array) || in_array('XII', $this->tingkat_array))) {
            return 'Fase E & F (Kelas X–XII)';
        }
        return in_array('X', $this->tingkat_array) ? 'Fase E (Kelas X)' : 'Fase F (Kelas XI–XII)';
    }

    /** Label resource yang dibutuhkan mapel ini */
    public function getResourceLabelAttribute(): string {
        if (!$this->resource_key) return '—';
        return self::RESOURCES[$this->resource_key] ?? $this->resource_key;
    }

    public function getTingkatArrayAttribute(): array {
        if (empty($this->tingkat)) return ['X', 'XI', 'XII'];
        return array_map('trim', explode(',', $this->tingkat));
    }

    public function getTingkatLabelAttribute(): string {
        $arr = $this->tingkat_array;
        if (count($arr) === 3) return 'Kelas X, XI, XII (Semua)';
        return 'Kelas ' . implode(', ', $arr);
    }
}
