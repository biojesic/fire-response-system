<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::table('barangays', function (Blueprint $table) {
            $table->enum('brgy_status', ['active', 'inactive', 'unverified', 'rejected', 'banned'])->default('unverified');
            $table->foreignId('lgu_id')->nullable()->constrained('cities_and_municipalities')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('barangays', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['lgu_id']);

            $table->dropColumn([
                'brgy_status',
                'lgu_id',
                'approved_by',
                'approved_at',
                'rejected_by',
                'rejected_at',
            ]);
        });
    }
};
