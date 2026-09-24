<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OBSERVACAO_RECONSTRUIDO = 'Registro reconstruído a partir dos dados existentes.';

    /**
     * Os eventos reconstruídos na criação da tabela eram marcados por um texto no início da
     * observação. A marcação passa para uma coluna própria e a observação fica só com o texto real.
     */
    public function up(): void
    {
        Schema::table('equipamento_historicos', function (Blueprint $table) {
            $table->boolean('reconstruido')->default(false)->after('observacao');
        });

        DB::table('equipamento_historicos')
            ->where('observacao', 'like', self::OBSERVACAO_RECONSTRUIDO . '%')
            ->orderBy('id')
            ->each(function ($evento) {
                $observacao = trim(mb_substr($evento->observacao, mb_strlen(self::OBSERVACAO_RECONSTRUIDO)));

                DB::table('equipamento_historicos')->where('id', $evento->id)->update([
                    'reconstruido' => true,
                    'observacao' => $observacao !== '' ? $observacao : null,
                ]);
            });
    }

    public function down(): void
    {
        DB::table('equipamento_historicos')
            ->where('reconstruido', true)
            ->orderBy('id')
            ->each(function ($evento) {
                DB::table('equipamento_historicos')->where('id', $evento->id)->update([
                    'observacao' => trim(self::OBSERVACAO_RECONSTRUIDO . ' ' . ($evento->observacao ?? '')),
                ]);
            });

        Schema::table('equipamento_historicos', function (Blueprint $table) {
            $table->dropColumn('reconstruido');
        });
    }
};
