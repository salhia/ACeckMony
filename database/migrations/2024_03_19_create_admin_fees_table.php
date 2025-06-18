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
        Schema::create('sys_admin_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('sys_transactions')->onDelete('set null');
            $table->decimal('trnsferamount', 12, 2)->default(0.00);
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->text('payment_notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_fees');
    }
};
