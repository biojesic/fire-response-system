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
        Schema::table('firefighters', function (Blueprint $table) {
            $table->unsignedBigInteger('rank_id')->nullable();
            $table->foreign('rank_id')->references('id')->on('firefighter_ranks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firefighters', function (Blueprint $table) {
            $table->dropForeign(['rank_id']);
            $table->dropColumn('rank_id');
        });
    }
};
