<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;

        protected $fillable=['user_id','file_id' ,'time_check_in','time_check_out'];
    public function user(){
        return $this->hasOne(User::class,'user_id');

    }

    public function file(){
        return $this->hasOne(File::class,'file_id');

    }
}
