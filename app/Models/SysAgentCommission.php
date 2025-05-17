<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysAgentCommission extends Model
{
    use HasFactory;

    protected $table = 'sys_agent_commetion';

    protected $fillable = [
        'agent_id',
        'commission_rate',
        'fixed_commission',
        'admin_fee_fixed',
        'min_amount',
        'is_active'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'fixed_commission' => 'decimal:2',
        'admin_fee_fixed' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}