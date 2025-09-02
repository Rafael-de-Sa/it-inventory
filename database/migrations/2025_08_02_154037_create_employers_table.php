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
        Schema::create('employers', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('trade_name', 50);
            $table->string('corporate_name', 50);
            $table->string('cnpj', 14)->unique();
            $table->string('street', 150);
            $table->string('number', 8);
            $table->string('complement', 50)->nullable();
            $table->string('neighborhood', 50)->nullable();
            $table->string('city', 30);
            $table->string('state', 2);
            $table->string('zipcode', 8);
            $table->string('website', 40)->nullable();
            $table->string('email', 60);
            $table->boolean('active')->default(true);
            $table->json('phones')->nullable();

            $table->string('logo_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};
