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
        Schema::create('firefighters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('fireStationId')->constrained('fire_station')->cascadeOnDelete();
            $table->foreignId('teamId')->nullable()->constrained('teams')->nullOnDelete();
            $table->enum('position', ['Team Leader', 'Responder', 'Dispatcher', 'Admin']);
            $table->json('personalEquipment')->nullable(); // Personal equipment list (e.g., ["Helmet", "PPE"])
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firefighters');
    }
};
