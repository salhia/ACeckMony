<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysAgentEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'transaction_id',
        'earned_amount'
    ];

    protected $casts = [
        'earned_amount' => 'decimal:2'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function transaction()
    {
        return $this->belongsTo(SysTransaction::class, 'transaction_id');
    }
}