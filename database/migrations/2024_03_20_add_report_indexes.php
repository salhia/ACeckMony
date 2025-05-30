<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sys_transactions', function (Blueprint $table) {
            $table->index(['created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['send_region_id', 'created_at']);
            $table->index(['user_id', 'send_region_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['parent_agent_id']);
            $table->index(['region_id']);
        });
    }

    public function down()
    {
        Schema::table('sys_transactions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['send_region_id', 'created_at']);
            $table->dropIndex(['user_id', 'send_region_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['parent_agent_id']);
            $table->dropIndex(['region_id']);
        });
    }
};
