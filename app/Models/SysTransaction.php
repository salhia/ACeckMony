<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysTransaction extends Model
{
    use HasFactory;
    protected $table = 'sys_transactions';
    protected $fillable = [
        'transaction_code', 'sender_user_id', 'sender_customer_id', 'sender_agent_id','sender_region_id',
        'receiver_user_id', 'receiver_customer_id', 'receiver_agent_id',
        'amount', 'commission', 'admin_fee', 'net_amount', 'final_delivered_amount',
        'transaction_type_id', 'delivery_confirmation', 'delivery_proof',
        'delivery_notes', 'notes', 'status', 'type', 'created_by','region_id',
        'agent_id'
    ];

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    // Alias for senderUser to match the view's expectations
    public function user()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function senderCustomer()
    {
        return $this->belongsTo(SysCustomer::class, 'sender_customer_id');
    }

    public function receiverCustomer()
    {
        return $this->belongsTo(SysCustomer::class, 'receiver_customer_id');
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
        return $this->belongsTo(SysRegion::class, 'region_id');
    }

    public function deliveredByUser()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function senderAgent()
    {
        return $this->belongsTo(User::class, 'sender_agent_id');
    }

    public function senderRegion()
    {
        return $this->belongsTo(SysRegion::class, 'sender_region_id');
    }

    // Alias for senderRegion to match the view's expectations
    public function sendRegion()
    {
        return $this->belongsTo(SysRegion::class, 'sender_region_id');
    }

    public function receiverAgent()
    {
        return $this->belongsTo(User::class, 'receiver_agent_id');
    }
}
