<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sys_agent_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('fixed_commission', 8, 2)->default(0.00);
            $table->decimal('admin_fee_fixed', 8, 2)->default(0.00);
            $table->decimal('min_amount', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sys_agent_commissions');
    }
};
