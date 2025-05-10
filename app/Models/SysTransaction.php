<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysTransaction extends Model
{
    use HasFactory;
    protected $table = 'sys_transactions';
    protected $fillable = [
        'transaction_code', 'sender_user_id', 'sender_customer_id', 'sender_agent_id',
        'receiver_user_id', 'receiver_customer_id', 'receiver_agent_id',
        'amount', 'commission', 'admin_fee', 'net_amount', 'final_delivered_amount',
        'transaction_type_id', 'delivery_confirmation', 'delivery_proof',
        'delivery_notes', 'notes', 'status', 'type', 'created_by'
    ];

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    public function senderCustomer()
    {
        return $this->belongsTo(SysCustomer::class, 'sender_customer_id');
    }

    public function receiverCustomer()
    {
        return $this->belongsTo(SysCustomer::class, 'receiver_customer_id');
    }

    public function transactionType()
    {
        return $this->belongsTo(SysTransactionType::class, 'transaction_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function agent()
{
    return $this->belongsTo(User::class, 'sender_agent_id');
}




public function state()
{
    return $this->belongsTo(SysRegion::class, 'region_id', 'id');
}

public function region()
{
    return $this->belongsTo(SysRegion::class, 'region_id', 'id');
}

public function deliveredByUser()
{
    return $this->belongsTo(User::class, 'delivered_by');
}
}
