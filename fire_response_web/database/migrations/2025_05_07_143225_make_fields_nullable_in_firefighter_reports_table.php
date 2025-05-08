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
        Schema::table('firefighter_reports', function (Blueprint $table) {
            $table->string('reportType')->nullable()->change();
            $table->text('details')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firefighter_reports', function (Blueprint $table) {
            $table->string('reportType')->nullable(false)->change();
            $table->text('details')->nullable(false)->change();
        });
    }
};
