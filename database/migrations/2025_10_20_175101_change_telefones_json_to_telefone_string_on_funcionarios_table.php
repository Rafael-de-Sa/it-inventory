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
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->string('telefone', 11)->nullable()->after('matricula');
        });

        if (Schema::hasColumn('funcionarios', 'telefones')) {
            DB::table('funcionarios')
                ->select('id', 'telefones')
                ->orderBy('id')
                ->chunkById(500, function ($rows) {
                    foreach ($rows as $r) {
                        if (is_null($r->telefones)) continue;

                        $raw = $r->telefones;
                        $val = null;

                        $decoded = json_decode($raw, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            if (is_string($decoded)) {
                                $val = $decoded;
                            } elseif (is_array($decoded)) {
                                foreach ($decoded as $item) {
                                    if (!empty($item)) {
                                        $val = $item;
                                        break;
                                    }
                                }
                            }
                        } else {
                            $val = $raw;
                        }

                        $digits = preg_replace('/\D+/', '', (string) $val);
                        if ($digits !== '' && strlen($digits) >= 10 && strlen($digits) <= 11) {
                            DB::table('funcionarios')
                                ->where('id', $r->id)
                                ->update(['telefone' => $digits]);
                        }
                    }
                });

            Schema::table('funcionarios', function (Blueprint $table) {
                $table->dropColumn('telefones');
            });
        }
    }

    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->json('telefones')->nullable()->after('matricula');
        });

        DB::table('funcionarios')
            ->select('id', 'telefone')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $r) {
                    $json = $r->telefone ? json_encode([$r->telefone]) : null;
                    DB::table('funcionarios')
                        ->where('id', $r->id)
                        ->update(['telefones' => $json]);
                }
            });

        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropColumn('telefone');
        });
    }
};
