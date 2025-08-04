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
        Schema::create('movements', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('restrict');

            $table->string('observation', 200)->nullable();
            $table->string('responsibility_term', 200)->nullable();

            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
