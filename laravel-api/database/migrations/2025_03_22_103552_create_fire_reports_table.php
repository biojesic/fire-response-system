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
        Schema::create('fire_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullable();
            $table->foreignId('fireStationId')->constrained('fire_stations')->onDelete('set null');
            $table->text('location');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('landmark')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_info')->nullable(); // Optional: Name/Phone for anonymous reports
            $table->enum('status', ['Pending', 'Responding', 'Resolved', 'False Alarm'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fire_reports');
    }
};
