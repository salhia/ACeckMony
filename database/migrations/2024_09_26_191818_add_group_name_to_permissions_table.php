<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('group_name')->nullable();  // Adding the group_name column
        });
    }

    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('group_name');  // Dropping the group_name column if rolled back
        });
    }
};
