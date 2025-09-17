<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
        protected $fillable = ['group_name','admin_id'];

    public function user_groups()
    {
        return $this->hasMany(User_Group::class,'group_id');
    }
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    public function joinRequest(){
        return $this->hasMany(JoinGroup::class,'group_id');
    }
}
