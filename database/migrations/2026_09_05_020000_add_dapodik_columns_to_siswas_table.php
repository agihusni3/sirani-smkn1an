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
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->after('nisn');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('nama');
            $table->string('tempat_lahir')->nullable()->after('jenis_kelamin');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama')->nullable()->after('tanggal_lahir');
            $table->text('alamat')->nullable()->after('agama');
            $table->string('nama_ayah')->nullable()->after('alamat');
            $table->string('nama_ibu')->nullable()->after('nama_ayah');
            $table->string('asal_sekolah')->nullable()->after('nama_ortu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'nik',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'agama',
                'alamat',
                'nama_ayah',
                'nama_ibu',
                'asal_sekolah',
            ]);
        });
    }
};
