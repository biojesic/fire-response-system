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
        Schema::table('users', function (Blueprint $table) {
        $table->text('rejection_reason')->nullable()->after('userStatus');
        $table->boolean('reapply_allowed')->default(true)->after('rejection_reason');
        $table->unsignedInteger('reapplication_count')->default(0)->after('reapply_allowed');
        $table->timestamp('last_rejection_at')->nullable()->after('reapplication_count');

        DB::statement("ALTER TABLE users MODIFY COLUMN userStatus ENUM('Active', 'Inactive', 'Unverified', 'Rejected')");
        
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'reapply_allowed', 'reapplication_count']);
        });
    }
};
