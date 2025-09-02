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

            $table->date('purchase_date')->nullable();
            $table->double('purchase_value')->nullable();
            $table->foreignId('equipment_type_id')->constrained('equipment_types')->onDelete('restrict');
            $table->enum('state', ['in_use', 'defective', 'discarded', 'available'])->default('available');
            $table->boolean('active')->default(true);
            $table->text('description');
            $table->string('patrimony')->unique()->nullable();
            $table->string('serial_number')->unique()->nullable();

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
