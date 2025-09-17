<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoge extends Model
{
    use HasFactory;
    protected $table = 'user_loges';
    protected $fillable = [
        'user_id',
        'group_id',
        'file_id',
        'action',
        'details',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
