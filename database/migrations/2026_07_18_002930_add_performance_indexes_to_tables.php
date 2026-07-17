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
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('date');
            $table->index('status');
            $table->index(['user_id', 'date'], 'attendances_user_date_index');
        });

        Schema::table('user_schedules', function (Blueprint $table) {
            $table->index(['user_id', 'schedule_date'], 'schedules_user_date_index');
        });

        Schema::table('overtimes', function (Blueprint $table) {
            $table->index('status');
            $table->index(['user_id', 'tanggal'], 'overtimes_user_tanggal_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['date']);
            $table->dropIndex(['status']);
            $table->dropIndex('attendances_user_date_index');
        });

        Schema::table('user_schedules', function (Blueprint $table) {
            $table->dropIndex('schedules_user_date_index');
        });

        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex('overtimes_user_tanggal_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });
    }
};
