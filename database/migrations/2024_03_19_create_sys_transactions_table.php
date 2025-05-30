<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sys_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 20)->unique();
            $table->foreignId('sender_user_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('sender_customer_id')->nullable()->constrained('sys_customers')->onDelete('restrict');
            $table->foreignId('sender_agent_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->integer('sender_region_id')->nullable();
            $table->foreignId('receiver_user_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('receiver_customer_id')->nullable()->constrained('sys_customers')->onDelete('restrict');
            $table->foreignId('receiver_agent_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->integer('receiver_region_id')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('commission', 12, 2)->nullable();
            $table->decimal('admin_fee', 12, 2)->nullable();
            $table->decimal('net_amount', 12, 2)->nullable();
            $table->decimal('final_delivered_amount', 12, 2)->nullable();
            $table->unsignedInteger('transaction_type_id')->nullable();
            $table->boolean('delivery_confirmation')->default(false);
            $table->string('delivery_proof', 255)->nullable();
            $table->text('delivery_notes')->nullable();
            $table->integer('delivered_by')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'rejected', 'delivered'])->default('pending');
            $table->enum('type', ['internal', 'external'])->default('internal');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->integer('region_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_transactions');
    }
};
