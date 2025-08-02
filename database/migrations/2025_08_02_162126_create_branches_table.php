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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employer_id')->constrained('employers')->onDelete('cascade');

            $table->string('trade_name', 50);
            $table->string('street', 150);
            $table->string('number', 8);
            $table->string('complement', 50)->nullable();
            $table->string('neighborhood', 50)->nullable();
            $table->string('city', 30);
            $table->string('state', 2);
            $table->string('zipcode', 8);
            $table->string('email', 60)->nullable();
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
        Schema::dropIfExists('branches');
    }
};
