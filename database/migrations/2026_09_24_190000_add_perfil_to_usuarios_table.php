<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perfil de acesso (App\Enums\Perfil). Os usuários existentes ficam como TIC, que tem acesso completo,
     * mantendo o comportamento anterior.
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('perfil', 20)->default('tic')->after('senha');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('perfil');
        });
    }
};
