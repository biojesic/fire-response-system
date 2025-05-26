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
        Schema::create('barangay_fire_aids', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
        $table->enum('status', ['On Response', 'Standby', 'Off Duty'])->default('Standby');
        $table->string('photo')->nullable();
        $table->string('barangay_id_path')->nullable();
        $table->string('barangay_certificate_path')->nullable();
        $table->enum('position', ['Admin', 'Fire Aid']);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay_fire_aids');
    }
};
