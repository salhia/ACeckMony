<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sys_account_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->constrained('sys_accounts')->onDelete('restrict');
            $table->enum('type', ['debit', 'credit'])->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('balance_after', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('related_transaction_id')->nullable()->constrained('sys_transactions')->onDelete('restrict');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_account_movements');
    }
};
