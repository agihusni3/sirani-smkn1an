<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan relasi surat keluar ke notifikasi_ortus
        if (Schema::hasTable('notifikasi_ortus') && !Schema::hasColumn('notifikasi_ortus', 'surat_keluar_id')) {
            Schema::table('notifikasi_ortus', function (Blueprint $table) {
                $table->foreignId('surat_keluar_id')->nullable()->constrained('surat_keluars')->nullOnDelete();
            });
        }

        // 2. Tambahkan relasi SK dan surat keluar ke kasus_disiplins
        if (Schema::hasTable('kasus_disiplins')) {
            Schema::table('kasus_disiplins', function (Blueprint $table) {
                if (!Schema::hasColumn('kasus_disiplins', 'buku_sk_id')) {
                    $table->foreignId('buku_sk_id')->nullable()->constrained('buku_sk_kepseks')->nullOnDelete();
                }
                if (!Schema::hasColumn('kasus_disiplins', 'surat_keluar_id')) {
                    $table->foreignId('surat_keluar_id')->nullable()->constrained('surat_keluars')->nullOnDelete();
                }
            });
        }

        // 3. Tambahkan metadata sumber modul, kategori surat, dan link cetak pada surat_keluars
        if (Schema::hasTable('surat_keluars')) {
            Schema::table('surat_keluars', function (Blueprint $table) {
                if (!Schema::hasColumn('surat_keluars', 'sumber_modul')) {
                    $table->string('sumber_modul', 50)->default('situan_tu');
                }
                if (!Schema::hasColumn('surat_keluars', 'kategori_surat')) {
                    $table->string('kategori_surat', 100)->nullable();
                }
                if (!Schema::hasColumn('surat_keluars', 'link_cetak')) {
                    $table->string('link_cetak', 255)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifikasi_ortus') && Schema::hasColumn('notifikasi_ortus', 'surat_keluar_id')) {
            Schema::table('notifikasi_ortus', function (Blueprint $table) {
                $table->dropForeign(['surat_keluar_id']);
                $table->dropColumn('surat_keluar_id');
            });
        }

        if (Schema::hasTable('kasus_disiplins')) {
            Schema::table('kasus_disiplins', function (Blueprint $table) {
                if (Schema::hasColumn('kasus_disiplins', 'buku_sk_id')) {
                    $table->dropForeign(['buku_sk_id']);
                    $table->dropColumn('buku_sk_id');
                }
                if (Schema::hasColumn('kasus_disiplins', 'surat_keluar_id')) {
                    $table->dropForeign(['surat_keluar_id']);
                    $table->dropColumn('surat_keluar_id');
                }
            });
        }

        if (Schema::hasTable('surat_keluars')) {
            Schema::table('surat_keluars', function (Blueprint $table) {
                if (Schema::hasColumn('surat_keluars', 'link_cetak')) {
                    $table->dropColumn('link_cetak');
                }
                if (Schema::hasColumn('surat_keluars', 'kategori_surat')) {
                    $table->dropColumn('kategori_surat');
                }
                if (Schema::hasColumn('surat_keluars', 'sumber_modul')) {
                    $table->dropColumn('sumber_modul');
                }
            });
        }
    }
};
