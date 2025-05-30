<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'commission_rate' => 'decimal:2'
    ];

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'phone',
        'address',
        'photo',
        'role',
        'status',
        'commission_rate',
        'region_id',
        'parent_agent_id'
    ];

    public function getpermissionGroups()
    {
        $permission_group = DB::table('permissions')->select('group_name')->groupBy('group_name')->get();
        return $permission_group;
    }

    public function getPermissionByGroupName($group_name)
    {

        $permissions =  DB::table('permissions')->select('name', 'id')->where('group_name', $group_name)->get();
        return $permissions;
    }

    public function roleHasPermissions($role, $permissions)
    {
        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {  // Fixed the method call
                $hasPermission = false;
                break;  // Exit the loop once a permission is not found
            }
        }
        return $hasPermission;
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(SysRegion::class, 'region_id');
    }

    public function parentAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_agent_id');
    }

    public function subAgents()
    {
        return $this->hasMany(User::class, 'parent_agent_id');
    }

    public function sentTransactions()
    {
        return $this->hasMany(SysTransaction::class, 'sender_user_id');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(SysTransaction::class, 'receiver_user_id');
    }

    public function agentTransactions()
    {
        return $this->hasMany(SysTransaction::class, 'sender_agent_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

}
