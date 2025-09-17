<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_id',
        'user_id',
        'group_id',
        'action',
        'details',
        'action_time'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function file()
    {
        return $this->belongsTo(File::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
