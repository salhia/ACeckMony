<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add indexes for sys_transactions table
        Schema::table('sys_transactions', function (Blueprint $table) {
            // Composite indexes for common queries
            $table->index(['user_id', 'created_at', 'send_region_id']);
            $table->index(['send_region_id', 'created_at']);

            // Individual indexes for frequently filtered columns
            $table->index('created_at');
            $table->index('amount');
            $table->index('commission');
        });

        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->index(['parent_agent_id', 'region_id']);
            $table->index('region_id');
        });
    }

    public function down()
    {
        Schema::table('sys_transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at', 'send_region_id']);
            $table->dropIndex(['send_region_id', 'created_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['amount']);
            $table->dropIndex(['commission']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['parent_agent_id', 'region_id']);
            $table->dropIndex(['region_id']);
        });
    }
};
