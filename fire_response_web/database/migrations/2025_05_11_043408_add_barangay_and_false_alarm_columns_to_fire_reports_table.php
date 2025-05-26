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
            // $table->unsignedBigInteger('barangay_id')->nullable()->after('fireStationId');
            // $table->unsignedBigInteger('marked_as_false_alarm_by')->nullable()->after('barangay_id');
            //  $table->timestamp('marked_as_false_alarm_at')->nullable()->after('marked_as_false_alarm_by');

            // $table->foreign('barangay_id')->references('id')->on('barangays')->nullOnDelete();
            // $table->foreign('marked_as_false_alarm_by')->references('id')->on('users')->nullOnDelete();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fire_reports', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropForeign(['marked_as_false_alarm_by']);
            $table->dropColumn(['barangay_id', 'marked_as_false_alarm_by']);
        });
    }
};
