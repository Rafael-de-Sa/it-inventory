<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Logradouro" é o termo correto do endereço (rua, avenida, travessa...) e já era o rótulo exibido.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->renameColumn('rua', 'logradouro');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->renameColumn('logradouro', 'rua');
        });
    }
};
