<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AuditLog;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'gurus';

    protected static function boot(): void
    {
        parent::boot();
        static::created(function ($m) {
            AuditLog::catat('create', 'guru', "Guru/pegawai baru ditambahkan: {$m->nama}", null, $m->only(['nip','nama','jabatan','status']), $m);
        });
        static::updated(function ($m) {
            if ($m->wasChanged()) {
                AuditLog::catat('update', 'guru', "Data guru diubah: {$m->nama}", $m->getOriginal(), $m->getChanges(), $m);
            }
        });
        static::deleted(function ($m) {
            AuditLog::catat('delete', 'guru', "Guru dihapus: {$m->nama}", $m->only(['nip','nama','jabatan','status']), null, $m);
        });
    }

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'jenis_kepegawaian',
        'hari_mengajar',
        'no_hp',
        'foto',
        'status',
    ];

    protected $casts = [
        'hari_mengajar' => 'array',
    ];

    protected $appends = ['foto_url', 'label_kepegawaian'];

    /**
     * Cek apakah guru bertaraf honorer / GTT.
     */
    public function isHonor(): bool
    {
        return in_array($this->jenis_kepegawaian, ['honor', 'gtt', 'honorer']);
    }

    /**
     * Label teks kepegawaian untuk tampilan badge.
     */
    public function getLabelKepegawaianAttribute(): string
    {
        return match($this->jenis_kepegawaian) {
            'pns'     => 'PNS',
            'pppk'    => 'PPPK',
            'honor'   => 'Guru Honor (GTT)',
            'tendik'  => 'Tenaga Kependidikan',
            default   => 'PNS',
        };
    }

    /**
     * Dapatkan daftar hari mengajar (default Senin-Jumat jika tidak dispesifikasikan).
     */
    public function getHariMengajarList(): array
    {
        if (!empty($this->hari_mengajar) && is_array($this->hari_mengajar)) {
            return $this->hari_mengajar;
        }

        // Default untuk PNS / Tendik atau jika belum diset: Senin s/d Jumat
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    }

    /**
     * Cek apakah guru wajib hadir pada hari tertentu (nama hari bahasa Indonesia atau string tanggal).
     */
    public function isWajibHadirHari(?string $hari = null): bool
    {
        if (empty($hari)) {
            $hari = \Carbon\Carbon::today()->locale('id')->isoFormat('dddd');
        }

        // Jika parameter berupa format tanggal (misal 2026-09-04), konversi ke nama hari bahasa Indonesia
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $hari)) {
            $hari = \Carbon\Carbon::parse($hari)->locale('id')->isoFormat('dddd');
        }

        $hariFormatted = ucfirst(strtolower(trim($hari)));

        // Hari libur akhir pekan
        if (in_array($hariFormatted, ['Sabtu', 'Minggu'])) {
            return false;
        }

        // Pegawai / Guru tetap (PNS, PPPK, Tendik) wajib hadir setiap hari kerja (Senin-Jumat)
        if (!$this->isHonor()) {
            return true;
        }

        // Guru honorer / GTT wajib hadir jika hari tersebut ada di jadwal mengajarnya
        $hariList = array_map(fn($h) => ucfirst(strtolower(trim($h))), $this->getHariMengajarList());
        return in_array($hariFormatted, $hariList);
    }

    /**
     * Cek apakah hari tertentu (atau hari ini) termasuk hari mengajar bagi guru ini.
     */
    public function isHariMengajar($tanggal = null): bool
    {
        return $this->isWajibHadirHari($tanggal ? \Carbon\Carbon::parse($tanggal)->toDateString() : null);
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }

        // Bersihkan gelar akademik depan dan belakang untuk inisial nama yang akurat
        $cleanName = preg_replace('/\b(Drs|Dra|Ir|Prof|Dr|H|Hj)\.\s*/i', '', $this->nama);
        $cleanName = preg_replace('/,.*$/', '', $cleanName); // Hapus gelar belakang seperti ', S.Pd', ', S.T.', ', M.Pd'
        $cleanName = trim($cleanName);

        $namaEncoded = urlencode($cleanName ?: $this->nama);
        return "https://ui-avatars.com/api/?name={$namaEncoded}&background=3B82F6&color=ffffff&bold=true&size=200";
    }

    public function absensis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Absensi::class, 'pemilik_id')->where('pemilik_type', 'guru');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'guru_id');
    }

    public function rombels(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rombel::class, 'wali_kelas_id');
    }

    public function kartuRfid(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KartuRfid::class, 'pemilik_id')->where('pemilik_type', 'guru')->where('status', 'aktif');
    }

    public function kartuRfids(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KartuRfid::class, 'pemilik_id')->where('pemilik_type', 'guru');
    }
}
