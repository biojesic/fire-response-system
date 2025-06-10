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
        Schema::table('fire_station', function (Blueprint $table) {
        $table->unsignedBigInteger('city_municipality_id')->nullable();
        $table->foreign('city_municipality_id')
              ->references('id')->on('cities_and_municipalities')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fire_station', function (Blueprint $table) {
            $table->dropForeign(['city_municipality_id']);
            $table->dropColumn('city_municipality_id');
        });
    }
};
