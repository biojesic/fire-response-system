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
        Schema::create('firefighter_personal_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firefighter_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('personal_equipment')->onDelete('cascade'); // Foreign key to personal_equipment table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firefighter_personal_equipment');
    }
};
