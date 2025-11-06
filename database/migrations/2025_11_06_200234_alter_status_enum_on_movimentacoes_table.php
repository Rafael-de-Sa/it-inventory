<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE movimentacoes SET status = 'concluida' WHERE status = 'finalizada'");

        DB::statement("
            ALTER TABLE movimentacoes
            MODIFY COLUMN status ENUM('pendente', 'cancelada', 'concluida', 'encerrada')
            NOT NULL DEFAULT 'pendente'
        ");
    }

    public function down(): void
    {
        DB::statement("UPDATE movimentacoes SET status = 'finalizada' WHERE status = 'concluida'");

        DB::statement("
            ALTER TABLE movimentacoes
            MODIFY COLUMN status ENUM('pendente', 'cancelada', 'finalizada')
            NOT NULL DEFAULT 'pendente'
        ");
    }
};
