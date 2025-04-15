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
        Schema::create('firefighter_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fireReportId')->constrained('fire_reports')->cascadeOnDelete(); // Foreign key to fire_reports table
            $table->foreignId('fireFighterId')->constrained('firefighters'); // Foreign key to firefighters table
            $table->string('reportType');
            $table->text('details');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firefighter_reports');
    }
};
