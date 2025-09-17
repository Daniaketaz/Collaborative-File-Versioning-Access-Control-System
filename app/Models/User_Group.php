<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Group extends Model
{
    use HasFactory;

    protected $fillable =['user_id','group_id'];
    protected $table = 'user_groups';

    public function user(){
        return $this->belongsTo(User::class,'user_id');

    }

    public function group(){
        return $this->belongsTo(Group::class,'group_id');

    }

    public function files(){
        return $this->hasMany(File::class,'user_group_id');

    }
}
