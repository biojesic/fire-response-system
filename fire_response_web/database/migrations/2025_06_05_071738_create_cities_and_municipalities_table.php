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
        Schema::create('cities_and_municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['City', 'Municipality']);
            $table->string('province')->default('Cavite');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities_and_municipalities');
    }
};
