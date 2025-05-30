<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sys_agent_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('transaction_id')->nullable()->constrained('sys_transactions')->onDelete('restrict');
            $table->decimal('earned_amount', 12, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_agent_earnings');
    }
};
