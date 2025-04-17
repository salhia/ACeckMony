<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function property(){
        return $this->belongsTo(Property::class,'property_id','id');
    }

    public function agent(){
        return $this->belongsTo(User::class,'agent_id','id');
    }
}
