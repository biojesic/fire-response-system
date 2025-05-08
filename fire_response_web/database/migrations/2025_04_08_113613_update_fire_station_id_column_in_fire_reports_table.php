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
        Schema::table('fire_reports', function (Blueprint $table) {
            // $table->unsignedBigInteger('fireStationId')->nullable(false)->change();
            // $table->foreign('fireStationId')->references('id')->on('fire_stations')->onDelete('set null');
            $table->foreignId('fireStationId')->nullable()->constrained('fire_station')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fire_reports', function (Blueprint $table) {
            // $table->unsignedBigInteger('fireStationId')->nullable(false)->change();
            // $table->dropForeign(['fireStationId']);
            $table->dropForeign(['fireStationId']);
            $table->dropColumn('fireStationId');
        });
    }
};
