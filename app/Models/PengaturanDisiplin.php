<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanDisiplin extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_disiplins';

    protected $fillable = [
        'bobot_terlambat',
        'bobot_alpha',
        'bobot_bolos',
        'toleransi_terlambat_piket',
        'ambang_tahap_1_wali',
        'ambang_tahap_2_bk',
        'ambang_tahap_3_wakasis',
        'ambang_tahap_4_kepsek',
        'reward_streak_hari',
        'reward_streak_poin',
    ];

    protected $casts = [
        'bobot_terlambat'          => 'integer',
        'bobot_alpha'              => 'integer',
        'bobot_bolos'              => 'integer',
        'toleransi_terlambat_piket' => 'integer',
        'ambang_tahap_1_wali'      => 'integer',
        'ambang_tahap_2_bk'        => 'integer',
        'ambang_tahap_3_wakasis'   => 'integer',
        'ambang_tahap_4_kepsek'    => 'integer',
        'reward_streak_hari'       => 'integer',
        'reward_streak_poin'       => 'integer',
    ];

    /**
     * Dapatkan konfigurasi aktif (Singleton).
     */
    public static function getPengaturan(): self
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'bobot_terlambat'          => 3,
                'bobot_alpha'              => 10,
                'bobot_bolos'              => 15,
                'toleransi_terlambat_piket' => 2,
                'ambang_tahap_1_wali'      => 10,
                'ambang_tahap_2_bk'        => 30,
                'ambang_tahap_3_wakasis'   => 50,
                'ambang_tahap_4_kepsek'    => 75,
                'reward_streak_hari'       => 14,
                'reward_streak_poin'       => 5,
            ]);

            // Seed default reward catalog if empty
            KatalogReward::seedDefault();
        }
        return $setting;
    }
}
