<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sys_transactions', function (Blueprint $table) {
            $table->string('verification_code')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('sys_transactions', function (Blueprint $table) {
            $table->dropColumn('verification_code');
        });
    }
};
