<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SysRegion extends Model
{
    use HasFactory;
    protected $table = 'sys_regions';
    protected $fillable = ['name'];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class, 'region_id');
    }
}

class SysAgentDistributor extends Model
{
    use HasFactory;
    protected $table = 'sys_agent_distributors';
    protected $fillable = ['agent_id', 'distributor_id', 'assigned_at'];
    public $timestamps = false;

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    public function transactions()
    {
        return $this->hasMany(SysTransaction::class, 'agent_id');
    }
}



class SysAccount extends Model
{
    use HasFactory;
    protected $table = 'sys_accounts';
    protected $fillable = ['user_id', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movements()
    {
        return $this->hasMany(SysAccountMovement::class, 'account_id');
    }
}

class SysTransactionType extends Model
{
    use HasFactory;
    protected $table = 'sys_transaction_types';
    protected $fillable = ['name', 'description', 'is_active'];

    public function transactions()
    {
        return $this->hasMany(SysTransaction::class, 'transaction_type_id');
    }
}



class SysAgentCommission extends Model
{
    use HasFactory;
    protected $table = 'sys_agent_commissions';
    protected $fillable = [
        'agent_id', 'commission_rate', 'fixed_commission',
        'admin_fee_fixed', 'min_amount', 'is_active'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}

class SysAgentEarning extends Model
{
    use HasFactory;
    protected $table = 'sys_agent_earnings';
    protected $fillable = ['agent_id', 'transaction_id', 'earned_amount'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function transaction()
    {
        return $this->belongsTo(SysTransaction::class, 'transaction_id');
    }
}

class SysAccountMovement extends Model
{
    use HasFactory;
    protected $table = 'sys_account_movements';
    protected $fillable = ['account_id', 'type', 'amount', 'balance_after', 'description', 'related_transaction_id'];

    public function account()
    {
        return $this->belongsTo(SysAccount::class, 'account_id');
    }

    public function transaction()
    {
        return $this->belongsTo(SysTransaction::class, 'related_transaction_id');
    }
}

class SysNotification extends Model
{
    use HasFactory;
    protected $table = 'sys_notifications';
    protected $fillable = [
        'user_id', 'title', 'message', 'is_read', 'type', 'related_transaction_id', 'recipient_customer_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(SysTransaction::class, 'related_transaction_id');
    }

    public function recipient()
    {
        return $this->belongsTo(SysCustomer::class, 'recipient_customer_id');
    }
}

class SysActivityLog extends Model
{
    use HasFactory;
    protected $table = 'sys_activity_logs';
    protected $fillable = ['user_id', 'action', 'description', 'ip_address', 'user_agent'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
