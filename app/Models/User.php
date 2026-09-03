<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'guru_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function guru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function isAdmin(): bool
    {
        return ($this->role === 'admin' || $this->role === null) 
            && !$this->isKepalaSekolah() 
            && !$this->isWakaKesiswaan() 
            && !$this->isWakaKurikulum()
            && !$this->isGuruBk() 
            && !$this->isWaliKelas() 
            && !$this->isStafTu()
            && ($this->guru_id === null || $this->role === 'admin');
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah' || ($this->guru && $this->guru->jabatan === 'Kepala Sekolah');
    }

    public function isWakaKesiswaan(): bool
    {
        return $this->role === 'waka_kesiswaan' || ($this->guru && (str_contains(strtolower($this->guru->jabatan ?? ''), 'waka kesiswaan') || str_contains(strtolower($this->guru->jabatan ?? ''), 'wakil kepala sekolah bidang kesiswaan')));
    }

    public function isWakaKurikulum(): bool
    {
        return $this->role === 'waka_kurikulum' || ($this->guru && (str_contains(strtolower($this->guru->jabatan ?? ''), 'waka kurikulum') || str_contains(strtolower($this->guru->jabatan ?? ''), 'wakil kepala sekolah bidang kurikulum')));
    }

    public function isGuruBk(): bool
    {
        // Kepala Sekolah, Waka Kesiswaan & Waka Kurikulum tidak boleh terdeteksi sebagai Guru BK
        if ($this->isKepalaSekolah() || $this->isWakaKesiswaan() || $this->isWakaKurikulum()) {
            return false;
        }
        return $this->role === 'guru_bk' || ($this->guru && (str_contains(strtolower($this->guru->jabatan ?? ''), 'bimbingan konseling') || str_contains(strtolower($this->guru->jabatan ?? ''), 'bk')));
    }

    public function isWaliKelas(): bool
    {
        // Kepala Sekolah, Waka Kesiswaan & Waka Kurikulum tidak boleh terdeteksi sebagai Wali Kelas
        if ($this->isKepalaSekolah() || $this->isWakaKesiswaan() || $this->isWakaKurikulum()) {
            return false;
        }

        if ($this->role === 'wali_kelas') {
            return true;
        }

        if ($this->guru) {
            if ($this->guru->jabatan === 'Wali Kelas') {
                return true;
            }
            if ($this->guru->rombels()->exists()) {
                return true;
            }
        }

        return false;
    }

    public function isStafTu(): bool
    {
        return $this->role === 'staf_tu' || ($this->guru && (str_contains(strtolower($this->guru->jabatan ?? ''), 'tata usaha') || str_contains(strtolower($this->guru->jabatan ?? ''), 'tu') || str_contains(strtolower($this->guru->jabatan ?? ''), 'administrasi')));
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru' || ($this->guru_id !== null 
            && !$this->isAdmin() 
            && !$this->isKepalaSekolah() 
            && !$this->isWakaKesiswaan() 
            && !$this->isWakaKurikulum() 
            && !$this->isGuruBk() 
            && !$this->isWaliKelas() 
            && !$this->isStafTu() 
            && !$this->isGuruPiket());
    }

    public function isGuruPiket(): bool
    {
        // Hanya Kepala Sekolah eksekutif yang tidak bertugas piket gerbang harian
        if ($this->isKepalaSekolah()) {
            return false;
        }

        if ($this->role === 'guru_piket') {
            return true;
        }

        if ($this->guru) {
            return \App\Models\JadwalPiket::where('guru_id', $this->guru->id)->exists();
        }

        return false;
    }

    public function isPiketHariIni(): bool
    {
        if ($this->isKepalaSekolah()) {
            return false;
        }

        if ($this->role === 'guru_piket') {
            return true;
        }

        if ($this->guru && \App\Models\JadwalPiket::isGuruPiketHariIni($this->guru->id)) {
            return true;
        }

        return false;
    }

    public function getWaliRombelIds(): array
    {
        if (!$this->isWaliKelas()) {
            return [];
        }

        if ($this->guru) {
            $ids = $this->guru->rombels()->pluck('id')->toArray();
            if (!empty($ids)) {
                return $ids;
            }
        }

        if (str_contains($this->email, 'walikelas')) {
            $rombel = Rombel::all()->first(function ($r) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $r->nama_rombel));
                return str_contains($this->email, $slug);
            });
            if ($rombel) {
                return [$rombel->id];
            }
        }

        return [];
    }

    public function getWaliRombel(): ?Rombel
    {
        $ids = $this->getWaliRombelIds();
        return !empty($ids) ? Rombel::find($ids[0]) : null;
    }

    public function getRoleDisplayNameAttribute(): string
    {
        if ($this->isAdmin()) return 'Administrator Sistem';
        if ($this->isKepalaSekolah()) return 'Kepala Sekolah';
        if ($this->isWakaKesiswaan()) return 'Waka Kesiswaan';
        if ($this->isWakaKurikulum()) return 'Waka Kurikulum';
        if ($this->isGuruBk()) return 'Guru Bimbingan Konseling (BK)';
        if ($this->isWaliKelas()) return 'Wali Kelas';
        if ($this->isGuruPiket()) return 'Guru Piket';
        if ($this->isStafTu()) return 'Staf Tata Usaha (TU)';
        return 'Guru / Tenaga Pendidik';
    }
}
