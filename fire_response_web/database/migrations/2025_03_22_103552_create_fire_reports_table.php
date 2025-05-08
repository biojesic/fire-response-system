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
            $table->foreignId('reported_by')->nullable()->constrained('users');
            $table->foreignId('fireStationId')->nullable()->constrained('fire_stations')->onDelete('set null');
            $table->text('location');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('landmark')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_info')->nullable(); // Optional: Name/Phone for anonymous reports
            $table->enum('status', ['Pending', 'Responding', 'Fire Out', 'False Alarm'])->default('Pending');
            $table->foreignId('marked_as_contained_by_id')->nullable()->constrained('firefighters')->onDelete('set null');
            $table->timestamp('marked_as_contained_at')->nullable();
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
