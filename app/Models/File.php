<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_group_id',
        'name',
        'file_suffix',
        'free',
        'accepted'
    ];

    public function user_group(){
        return $this->belongsTo(User_Group::class,'user_group_id');

    }

    public function checks(){
        return $this->hasOne(Check::class,'check_id');

    }


}
