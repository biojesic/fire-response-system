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
        $table->timestamp('responders_arrived_at')->nullable();
        $table->decimal('response_time', 8, 2)->nullable()->comment('in minutes')->after('responders_arrived_at');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fire_reports', function (Blueprint $table) {
            //
        });
    }
};
