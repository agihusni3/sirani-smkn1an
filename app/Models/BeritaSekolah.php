<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeritaSekolah extends Model
{
    use HasFactory;

    protected $table = 'berita_sekolahs';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'ringkasan',
        'konten',
        'gambar_sampul',
        'penulis',
        'dilihat',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->judul) . '-' . Str::random(5);
            }
            if (empty($berita->published_at) && $berita->is_published) {
                $berita->published_at = now();
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at')->orderBy('published_at', 'desc');
    }

    public function getTanggalPublikasiAttribute()
    {
        return $this->published_at ?? $this->created_at;
    }

    public function getViewsAttribute()
    {
        return $this->dilihat ?? 0;
    }

    public function getAuthorNameAttribute()
    {
        return $this->penulis ?? 'Humas SMKN 1 Air Naningan';
    }

    public function getUrlGambarSampulAttribute(): ?string
    {
        if (empty($this->gambar_sampul)) {
            return null;
        }
        if (str_starts_with($this->gambar_sampul, 'http')) {
            return $this->gambar_sampul;
        }
        if (str_contains($this->gambar_sampul, 'images/web/')) {
            return asset('images/web/' . basename($this->gambar_sampul));
        }
        return asset('storage/' . $this->gambar_sampul);
    }
}
