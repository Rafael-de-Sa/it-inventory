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
        Schema::create('equipment_attributes', function (Blueprint $table) {
            $table->id()->autoIncrement();

            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');

            $table->string('value', 45);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['equipment_id', 'attribute_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_attributes');
    }
};
