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
            $table->foreignId('position_id')->nullable()->constrained('firefighter_positions')->onDelete('set null');
            // $table->json('personalEquipment')->nullable();
            $table->foreign('rank_id')->nullable()->constrained('firefighter_ranks')->onDelete('set null');
            $table->enum('status', ['Standby', 'On response', 'Off duty'])->default('off duty');
            $table->time('shift_start')->nullable();
            $table->time('shift_end')->nullable();
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
