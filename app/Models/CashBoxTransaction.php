<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBoxTransaction extends Model
{
    use HasFactory;

    protected $table = 'sys_cash_box_transactions';

    protected $fillable = [
        'user_id',
        'type',
        'date',
        'amount',
        'description',
        'reference_id',
        'reference_type',
        'status',
        'notes',
        'transaction_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime'
    ];

    // علاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
