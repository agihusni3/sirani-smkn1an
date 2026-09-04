<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WebsiteBanner extends Model
{
    use HasFactory;

    protected $table = 'website_banners';

    protected $fillable = [
        'posisi',
        'posisi_teks',
        'judul',
        'subjudul',
        'badge_text',
        'gambar',
        'tag_overlay',
        'tombol_teks_1',
        'tombol_url_1',
        'tombol_teks_2',
        'tombol_url_2',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan'    => 'integer',
    ];

    /**
     * Scope untuk banner aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk banner posisi hero_home.
     */
    public function scopeHero($query)
    {
        return $query->where('posisi', 'hero_home');
    }

    /**
     * Accessor URL Gambar dengan fallback ke hero bawaan.
     */
    public function getGambarUrlAttribute(): string
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return Storage::disk('public')->url($this->gambar);
        }
        if ($this->urutan % 2 === 0 && file_exists(public_path('images/web/jurusan_rpl.jpg'))) {
            return asset('images/web/jurusan_rpl.jpg');
        }
        return asset('images/web/hero_kampus.jpg');
    }
}
