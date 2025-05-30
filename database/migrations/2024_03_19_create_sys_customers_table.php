<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sys_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('identity_number', 50)->nullable();
            $table->enum('identity_type', ['id_card', 'passport', 'driver_license'])->default('id_card');
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_customers');
    }
};
