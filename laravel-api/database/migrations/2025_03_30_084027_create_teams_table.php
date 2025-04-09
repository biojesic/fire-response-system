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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('teamName')->unique();
            $table->foreignId('fireStationId')->constrained('fire_station')->cascadeOnDelete();
            $table->foreignId('assignedFireIncident')->nullable()->constrained('fire_reports')->nullOnDelete();
            $table->string('firetruckName')->nullable(); // Firetruck assigned
            $table->json('truckEquipmentList')->nullable(); // List of truck equipment (e.g., ["Hose", "Ladder"])
            $table->enum('status', ['standby', 'on_response'])->default('standby');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
