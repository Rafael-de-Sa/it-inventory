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
        Schema::create('employees', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');

            $table->string('first_name', 20);
            $table->string('last_name', 50);

            //Não deixei como unique pois posso contratar um colaborador novamente e 
            // ele terá outra matricula, mas CPF será o mesmo
            $table->string('cpf', 11);

            $table->string('registration', 8)->unique()->nullable();
            $table->date('dismissal_date')->nullable();
            $table->boolean('active')->default(true);
            $table->json('phones')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
