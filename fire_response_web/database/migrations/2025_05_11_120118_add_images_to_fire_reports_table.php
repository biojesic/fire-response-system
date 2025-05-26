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
            Schema::table('fire_reports', function (Blueprint $table) {
            // Add columns for false_alarm_image and fire_report_image
            $table->string('false_alarm_image')->nullable(); // For false alarm proof image
            $table->string('fire_report_image')->nullable(); // For fire report image (optional)
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('fire_reports', function (Blueprint $table) {
            // Drop the columns if we rollback the migration
            $table->dropColumn('false_alarm_image');
            $table->dropColumn('fire_report_image');
        });
    }
};
