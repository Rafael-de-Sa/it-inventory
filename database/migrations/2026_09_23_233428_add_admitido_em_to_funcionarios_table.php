<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nullable no banco porque os funcionários já cadastrados não têm essa data;
     * a obrigatoriedade fica na validação do cadastro e da edição.
     */
    public function up(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->date('admitido_em')->nullable()->after('matricula');
        });
    }

    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropColumn('admitido_em');
        });
    }
};
