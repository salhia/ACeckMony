<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
