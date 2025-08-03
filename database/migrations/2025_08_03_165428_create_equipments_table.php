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
        Schema::create('equipments', function (Blueprint $table) {
            $table->id()->autoIncrement();

            $table->date('acquisition_date');
            $table->double('acquisition_value');
            $table->foreignId('equipment_type_id')->constrained('equipment_types');
            $table->enum('status', ['uso', 'defeituoso', 'descartado', 'estoque']);
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
