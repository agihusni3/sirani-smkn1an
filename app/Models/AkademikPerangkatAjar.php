<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AkademikPerangkatAjar extends Model
{
    use HasFactory;

    protected $table = 'akademik_perangkat_ajars';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'distribusi_id',
        'tahun_ajaran_id',
        'semester',
        'tingkat',
        'fase',
        'status',
        'catatan_supervisi',
        'catatan_guru',
        'capaian_pembelajaran',
        'rasional_tujuan',
        'elemen_cp',
        'disahkan_oleh',
        'tanggal_pengesahan',
        'qr_token_pengesahan',
        'rpe_pekan_efektif',
        'rpe_pekan_cadangan',
        'file_kaldik_rpe',
        'file_cover_pengesahan',
    ];

    protected $casts = [
        'semester' => 'integer',
        'rpe_pekan_efektif' => 'integer',
        'rpe_pekan_cadangan' => 'integer',
        'tanggal_pengesahan' => 'date',
        'elemen_cp' => 'array',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(AkademikMataPelajaran::class, 'mata_pelajaran_id');
    }

    public function distribusiMengajar(): BelongsTo
    {
        return $this->belongsTo(AkademikDistribusiMengajar::class, 'distribusi_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disahkan_oleh');
    }

    public function atpItems(): HasMany
    {
        return $this->hasMany(AkademikAtpItem::class, 'perangkat_id')->orderBy('urutan');
    }

    public function modulAjars(): HasMany
    {
        return $this->hasMany(AkademikModulAjar::class, 'perangkat_id')->orderBy('pertemuan_ke_mulai');
    }

    public function kktpItems(): HasMany
    {
        return $this->hasMany(AkademikKktpItem::class, 'perangkat_id');
    }

    /**
     * Hitung persentase kelengkapan komponen perangkat pembelajaran
     */
    public function getKelengkapanAttribute(): array
    {
        $hasCp = !empty($this->capaian_pembelajaran) || !empty($this->elemen_cp) || !empty($this->mataPelajaran?->deskripsi_cp);
        $hasAtp = $this->atpItems()->count() > 0;
        $hasModul = $this->modulAjars()->count() > 0;
        $hasKktp = $this->kktpItems()->count() > 0;
        $hasRpe = ($this->rpe_pekan_efektif > 0);

        $totalPoin = 0;
        if ($hasCp) $totalPoin += 20;
        if ($hasAtp) $totalPoin += 25;
        if ($hasRpe) $totalPoin += 15;
        if ($hasModul) $totalPoin += 25;
        if ($hasKktp) $totalPoin += 15;

        return [
            'persen' => $totalPoin,
            'has_cp' => $hasCp,
            'has_atp' => $hasAtp,
            'has_rpe' => $hasRpe,
            'has_modul' => $hasModul,
            'has_kktp' => $hasKktp,
            'is_lengkap' => $totalPoin >= 100,
        ];
    }

    /**
     * Ambil Capaian Pembelajaran aktif (prioritaskan inputan guru)
     */
    public function getResolvedCpAttribute(): string
    {
        if (!empty($this->capaian_pembelajaran)) {
            return $this->capaian_pembelajaran;
        }

        if ($this->fase === 'E' && !empty($this->mataPelajaran?->capaian_pembelajaran_fase_e)) {
            return $this->mataPelajaran->capaian_pembelajaran_fase_e;
        }

        if ($this->fase === 'F' && !empty($this->mataPelajaran?->capaian_pembelajaran_fase_f)) {
            return $this->mataPelajaran->capaian_pembelajaran_fase_f;
        }

        return $this->mataPelajaran?->deskripsi_cp ?? '';
    }

    /**
     * Ambil Elemen CP aktif (prioritaskan inputan guru)
     */
    public function getResolvedElemenCpAttribute(): array
    {
        if (!empty($this->elemen_cp) && is_array($this->elemen_cp)) {
            return $this->elemen_cp;
        }

        $masterElem = $this->mataPelajaran?->elemen_cp;
        if (is_string($masterElem)) {
            return json_decode($masterElem, true) ?? [];
        }

        return is_array($masterElem) ? $masterElem : [];
    }

    /**
     * Badge status warna dan label
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'disahkan' => [
                'label' => 'Disahkan Kepala Sekolah',
                'badge' => 'ak-badge-success',
                'icon' => 'bi-check-circle-fill',
                'bg' => '#ecfdf5',
                'color' => '#065f46',
                'border' => '#a7f3d0',
            ],
            'diajukan' => [
                'label' => 'Menunggu Supervisi',
                'badge' => 'ak-badge-warning',
                'icon' => 'bi-hourglass-split',
                'bg' => '#fffbeb',
                'color' => '#92400e',
                'border' => '#fde68a',
            ],
            'perlu_revisi' => [
                'label' => 'Perlu Revisi',
                'badge' => 'ak-badge-danger',
                'icon' => 'bi-exclamation-circle-fill',
                'bg' => '#fef2f2',
                'color' => '#b91c1c',
                'border' => '#fecaca',
            ],
            default => [
                'label' => 'Draft Guru',
                'badge' => 'ak-badge-secondary',
                'icon' => 'bi-pencil',
                'bg' => '#f1f5f9',
                'color' => '#475569',
                'border' => '#cbd5e1',
            ],
        };
    }

    /**
     * Generate QR code token unik jika belum ada
     */
    public function generateQrToken(): string
    {
        if (empty($this->qr_token_pengesahan)) {
            $token = 'PERANGKAT-' . strtoupper(Str::random(12)) . '-' . $this->id;
            $this->update(['qr_token_pengesahan' => $token]);
            return $token;
        }
        return $this->qr_token_pengesahan;
    }
}
