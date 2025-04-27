<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SysCustomer extends Model
{
    use HasFactory;
    protected $table = 'sys_customers';
    protected $fillable = ['name', 'phone', 'identity_number', 'identity_type', 'registered_by'];

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}