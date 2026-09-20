<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AkademikJadwalWaktu extends Model
{
    protected $table = 'akademik_jadwal_waktus';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'hari',
        'jam_ke',    // 0-12 untuk slot jam KBM; -1 = istirahat 1, -2 = istirahat 2 (jika tipe=istirahat)
        'pukul',
        'keterangan',
        'tipe',      // 'jam' | 'istirahat' | 'khusus'
        'label',     // Nama tampilan baris, misal "Istirahat Pertama", "Sholat Dzuhur"
        'urutan',    // Urutan baris dalam 1 hari
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Jadwal Waktu Standar Default SMKN 1 Air Naningan
     * Format baru: array of rows dengan tipe, label, urutan
     */
    public static function defaultSchedule(): array
    {
        return [
            'SENIN' => [
                0  => '07.15 - 08.15',
                1  => '08.15 - 08.50',
                2  => '08.50 - 09.25',
                3  => '09.25 - 10.00',
                // Istirahat 1: 10.00 - 10.20
                4  => '10.20 - 10.55',
                5  => '10.55 - 11.30',
                6  => '11.30 - 12.05',
                7  => '12.05 - 12.40',
                // Istirahat 2: 12.40 - 13.10
                8  => '13.10 - 13.45',
                9  => '13.45 - 14.20',
                10 => '14.20 - 14.55',
                11 => '14.55 - 15.30',
            ],
            'SELASA' => [
                0  => '07.15 - 07.30',
                1  => '07.30 - 08.05',
                2  => '08.05 - 08.40',
                3  => '08.40 - 09.15',
                4  => '09.15 - 09.50',
                // Istirahat 1: 09.50 - 10.05
                5  => '10.05 - 10.40',
                6  => '10.40 - 11.15',
                7  => '11.15 - 11.50',
                // Istirahat 2: 11.50 - 12.25
                8  => '12.25 - 13.00',
                9  => '13.00 - 13.35',
                10 => '13.35 - 14.10',
                11 => '14.10 - 14.45',
            ],
            'RABU' => [
                0  => '07.15 - 07.30',
                1  => '07.30 - 08.05',
                2  => '08.05 - 08.40',
                3  => '08.40 - 09.15',
                4  => '09.15 - 09.50',
                5  => '10.05 - 10.40',
                6  => '10.40 - 11.15',
                7  => '11.15 - 11.50',
                8  => '12.55 - 13.30',
                9  => '13.30 - 14.05',
                10 => '14.05 - 14.40',
                11 => '14.40 - 15.15',
            ],
            'KAMIS' => [
                0  => '07.15 - 07.30',
                1  => '07.30 - 08.05',
                2  => '08.05 - 08.40',
                3  => '08.40 - 09.15',
                4  => '09.15 - 09.50',
                5  => '10.05 - 10.40',
                6  => '10.40 - 11.15',
                7  => '11.15 - 11.50',
                8  => '12.55 - 13.30',
                9  => '13.30 - 14.05',
                10 => '14.05 - 14.40',
                11 => '14.40 - 15.15',
            ],
            'JUMAT' => [
                0 => '07.15 - 08.00',
                1 => '08.00 - 08.35',
                2 => '08.35 - 09.05',
                3 => '09.05 - 09.40',
                // Istirahat 1: 09.40 - 10.00
                4 => '10.00 - 10.35',
                5 => '10.35 - 11.10',
            ],
        ];
    }

    /**
     * Jadwal lengkap termasuk baris istirahat (untuk tab Atur Pukul)
     * Returns array of rows per hari dengan tipe masing-masing
     */
    public static function defaultScheduleFull(): array
    {
        return [
            'SENIN' => [
                ['tipe' => 'khusus',    'jam_ke' => 0,   'urutan' => 0,  'label' => 'Upacara Bendera',  'pukul' => '07.15 - 08.15'],
                ['tipe' => 'jam',       'jam_ke' => 1,   'urutan' => 1,  'label' => 'Jam 1',            'pukul' => '08.15 - 08.50'],
                ['tipe' => 'jam',       'jam_ke' => 2,   'urutan' => 2,  'label' => 'Jam 2',            'pukul' => '08.50 - 09.25'],
                ['tipe' => 'jam',       'jam_ke' => 3,   'urutan' => 3,  'label' => 'Jam 3',            'pukul' => '09.25 - 10.00'],
                ['tipe' => 'istirahat', 'jam_ke' => -1,  'urutan' => 4,  'label' => 'Istirahat Pertama','pukul' => '10.00 - 10.20'],
                ['tipe' => 'jam',       'jam_ke' => 4,   'urutan' => 5,  'label' => 'Jam 4',            'pukul' => '10.20 - 10.55'],
                ['tipe' => 'jam',       'jam_ke' => 5,   'urutan' => 6,  'label' => 'Jam 5',            'pukul' => '10.55 - 11.30'],
                ['tipe' => 'jam',       'jam_ke' => 6,   'urutan' => 7,  'label' => 'Jam 6',            'pukul' => '11.30 - 12.05'],
                ['tipe' => 'jam',       'jam_ke' => 7,   'urutan' => 8,  'label' => 'Jam 7',            'pukul' => '12.05 - 12.40'],
                ['tipe' => 'istirahat', 'jam_ke' => -2,  'urutan' => 9,  'label' => 'Istirahat Kedua',  'pukul' => '12.40 - 13.10'],
                ['tipe' => 'jam',       'jam_ke' => 8,   'urutan' => 10, 'label' => 'Jam 8',            'pukul' => '13.10 - 13.45'],
                ['tipe' => 'jam',       'jam_ke' => 9,   'urutan' => 11, 'label' => 'Jam 9',            'pukul' => '13.45 - 14.20'],
                ['tipe' => 'jam',       'jam_ke' => 10,  'urutan' => 12, 'label' => 'Jam 10',           'pukul' => '14.20 - 14.55'],
                ['tipe' => 'jam',       'jam_ke' => 11,  'urutan' => 13, 'label' => 'Jam 11',           'pukul' => '14.55 - 15.30'],
            ],
            'SELASA' => [
                ['tipe' => 'khusus',    'jam_ke' => 0,   'urutan' => 0,  'label' => 'Apel Pagi',        'pukul' => '07.15 - 07.30'],
                ['tipe' => 'jam',       'jam_ke' => 1,   'urutan' => 1,  'label' => 'Jam 1',            'pukul' => '07.30 - 08.05'],
                ['tipe' => 'jam',       'jam_ke' => 2,   'urutan' => 2,  'label' => 'Jam 2',            'pukul' => '08.05 - 08.40'],
                ['tipe' => 'jam',       'jam_ke' => 3,   'urutan' => 3,  'label' => 'Jam 3',            'pukul' => '08.40 - 09.15'],
                ['tipe' => 'jam',       'jam_ke' => 4,   'urutan' => 4,  'label' => 'Jam 4',            'pukul' => '09.15 - 09.50'],
                ['tipe' => 'istirahat', 'jam_ke' => -1,  'urutan' => 5,  'label' => 'Istirahat Pertama','pukul' => '09.50 - 10.05'],
                ['tipe' => 'jam',       'jam_ke' => 5,   'urutan' => 6,  'label' => 'Jam 5',            'pukul' => '10.05 - 10.40'],
                ['tipe' => 'jam',       'jam_ke' => 6,   'urutan' => 7,  'label' => 'Jam 6',            'pukul' => '10.40 - 11.15'],
                ['tipe' => 'jam',       'jam_ke' => 7,   'urutan' => 8,  'label' => 'Jam 7',            'pukul' => '11.15 - 11.50'],
                ['tipe' => 'istirahat', 'jam_ke' => -2,  'urutan' => 9,  'label' => 'Istirahat Kedua',  'pukul' => '11.50 - 12.25'],
                ['tipe' => 'jam',       'jam_ke' => 8,   'urutan' => 10, 'label' => 'Jam 8',            'pukul' => '12.25 - 13.00'],
                ['tipe' => 'jam',       'jam_ke' => 9,   'urutan' => 11, 'label' => 'Jam 9',            'pukul' => '13.00 - 13.35'],
                ['tipe' => 'jam',       'jam_ke' => 10,  'urutan' => 12, 'label' => 'Jam 10',           'pukul' => '13.35 - 14.10'],
                ['tipe' => 'jam',       'jam_ke' => 11,  'urutan' => 13, 'label' => 'Jam 11',           'pukul' => '14.10 - 14.45'],
            ],
            'RABU' => [
                ['tipe' => 'khusus',    'jam_ke' => 0,   'urutan' => 0,  'label' => 'Apel Pagi',        'pukul' => '07.15 - 07.30'],
                ['tipe' => 'jam',       'jam_ke' => 1,   'urutan' => 1,  'label' => 'Jam 1',            'pukul' => '07.30 - 08.05'],
                ['tipe' => 'jam',       'jam_ke' => 2,   'urutan' => 2,  'label' => 'Jam 2',            'pukul' => '08.05 - 08.40'],
                ['tipe' => 'jam',       'jam_ke' => 3,   'urutan' => 3,  'label' => 'Jam 3',            'pukul' => '08.40 - 09.15'],
                ['tipe' => 'jam',       'jam_ke' => 4,   'urutan' => 4,  'label' => 'Jam 4',            'pukul' => '09.15 - 09.50'],
                ['tipe' => 'istirahat', 'jam_ke' => -1,  'urutan' => 5,  'label' => 'Istirahat Pertama','pukul' => '09.50 - 10.05'],
                ['tipe' => 'jam',       'jam_ke' => 5,   'urutan' => 6,  'label' => 'Jam 5',            'pukul' => '10.05 - 10.40'],
                ['tipe' => 'jam',       'jam_ke' => 6,   'urutan' => 7,  'label' => 'Jam 6',            'pukul' => '10.40 - 11.15'],
                ['tipe' => 'jam',       'jam_ke' => 7,   'urutan' => 8,  'label' => 'Jam 7',            'pukul' => '11.15 - 11.50'],
                ['tipe' => 'istirahat', 'jam_ke' => -2,  'urutan' => 9,  'label' => 'Istirahat Kedua',  'pukul' => '11.50 - 12.25'],
                ['tipe' => 'jam',       'jam_ke' => 8,   'urutan' => 10, 'label' => 'Jam 8',            'pukul' => '12.55 - 13.30'],
                ['tipe' => 'jam',       'jam_ke' => 9,   'urutan' => 11, 'label' => 'Jam 9',            'pukul' => '13.30 - 14.05'],
                ['tipe' => 'jam',       'jam_ke' => 10,  'urutan' => 12, 'label' => 'Jam 10',           'pukul' => '14.05 - 14.40'],
                ['tipe' => 'jam',       'jam_ke' => 11,  'urutan' => 13, 'label' => 'Jam 11',           'pukul' => '14.40 - 15.15'],
            ],
            'KAMIS' => [
                ['tipe' => 'khusus',    'jam_ke' => 0,   'urutan' => 0,  'label' => 'Apel Pagi',        'pukul' => '07.15 - 07.30'],
                ['tipe' => 'jam',       'jam_ke' => 1,   'urutan' => 1,  'label' => 'Jam 1',            'pukul' => '07.30 - 08.05'],
                ['tipe' => 'jam',       'jam_ke' => 2,   'urutan' => 2,  'label' => 'Jam 2',            'pukul' => '08.05 - 08.40'],
                ['tipe' => 'jam',       'jam_ke' => 3,   'urutan' => 3,  'label' => 'Jam 3',            'pukul' => '08.40 - 09.15'],
                ['tipe' => 'jam',       'jam_ke' => 4,   'urutan' => 4,  'label' => 'Jam 4',            'pukul' => '09.15 - 09.50'],
                ['tipe' => 'istirahat', 'jam_ke' => -1,  'urutan' => 5,  'label' => 'Istirahat Pertama','pukul' => '09.50 - 10.05'],
                ['tipe' => 'jam',       'jam_ke' => 5,   'urutan' => 6,  'label' => 'Jam 5',            'pukul' => '10.05 - 10.40'],
                ['tipe' => 'jam',       'jam_ke' => 6,   'urutan' => 7,  'label' => 'Jam 6',            'pukul' => '10.40 - 11.15'],
                ['tipe' => 'jam',       'jam_ke' => 7,   'urutan' => 8,  'label' => 'Jam 7',            'pukul' => '11.15 - 11.50'],
                ['tipe' => 'istirahat', 'jam_ke' => -2,  'urutan' => 9,  'label' => 'Istirahat Kedua',  'pukul' => '11.50 - 12.25'],
                ['tipe' => 'jam',       'jam_ke' => 8,   'urutan' => 10, 'label' => 'Jam 8',            'pukul' => '12.55 - 13.30'],
                ['tipe' => 'jam',       'jam_ke' => 9,   'urutan' => 11, 'label' => 'Jam 9',            'pukul' => '13.30 - 14.05'],
                ['tipe' => 'jam',       'jam_ke' => 10,  'urutan' => 12, 'label' => 'Jam 10',           'pukul' => '14.05 - 14.40'],
                ['tipe' => 'jam',       'jam_ke' => 11,  'urutan' => 13, 'label' => 'Jam 11',           'pukul' => '14.40 - 15.15'],
            ],
            'JUMAT' => [
                ['tipe' => 'khusus',    'jam_ke' => 0,   'urutan' => 0,  'label' => 'Lampung Mengaji',  'pukul' => '07.15 - 08.00'],
                ['tipe' => 'jam',       'jam_ke' => 1,   'urutan' => 1,  'label' => 'Jam 1',            'pukul' => '08.00 - 08.35'],
                ['tipe' => 'jam',       'jam_ke' => 2,   'urutan' => 2,  'label' => 'Jam 2',            'pukul' => '08.35 - 09.05'],
                ['tipe' => 'jam',       'jam_ke' => 3,   'urutan' => 3,  'label' => 'Jam 3',            'pukul' => '09.05 - 09.40'],
                ['tipe' => 'istirahat', 'jam_ke' => -1,  'urutan' => 4,  'label' => 'Istirahat',        'pukul' => '09.40 - 10.00'],
                ['tipe' => 'jam',       'jam_ke' => 4,   'urutan' => 5,  'label' => 'Jam 4',            'pukul' => '10.00 - 10.35'],
                ['tipe' => 'jam',       'jam_ke' => 5,   'urutan' => 6,  'label' => 'Jam 5',            'pukul' => '10.35 - 11.10'],
            ],
        ];
    }
}
