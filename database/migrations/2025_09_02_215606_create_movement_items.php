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
        Schema::create('movement_items', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignId('movement_id')->constrained('movements');
            $table->foreignId('equipment_id')->constrained('equipments');
            $table->string('return_term', 200)->nullable();
            $table->text('note')->nullable();
            $table->enum('return_reason', ['maintenance', 'defect', 'breakage', 'return'])->default('return');

            $table->timestamp('returned_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movement_items');
    }
};
