<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Bagian / role user
            $table->string('bagian')
                ->nullable()
                ->after('email');

            // Foto profil
            $table->string('foto')
                ->nullable()
                ->after('bagian');

            // Status akun
            $table->string('status')
                ->default('AKTIF')
                ->after('foto');

            // Waktu terakhir login
            $table->timestamp('last_login_at')
                ->nullable()
                ->after('status');

        });
    }

    /**
     * Batalkan migration.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'bagian',
                'foto',
                'status',
                'last_login_at',
            ]);

        });
    }
};