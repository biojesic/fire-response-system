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
            $table->foreignId('marked_as_contained_by_id')
            ->nullable()
            ->constrained('firefighters')
            ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fire_reports', function (Blueprint $table) {
            $table->dropForeign(['marked_as_contained_by_id']);
        });
    }
};
