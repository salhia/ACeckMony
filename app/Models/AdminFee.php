<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'percentage',
        'status',
        'description',
        'transaction_id',
        'paid_amount',
        'payment_notes',
        'payment_date',
        'trnsferamount',
 ];
    /**
     * علاقة مع المستخدم (الوكيل)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع المعاملة
     */
    public function transaction()
    {
        return $this->belongsTo(SysTransaction::class, 'transaction_id');
    }
}
