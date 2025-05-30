<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sys_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->string('title', 255)->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->enum('type', ['sms', 'email', 'in_app', 'transfer', 'delivery', 'commission', 'system'])->default('in_app');
            $table->foreignId('related_transaction_id')->nullable()->constrained('sys_transactions')->onDelete('restrict');
            $table->foreignId('recipient_customer_id')->nullable()->constrained('sys_customers')->onDelete('restrict');
            $table->timestamp('sent_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_notifications');
    }
};
