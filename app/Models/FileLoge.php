<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileLoge extends Model
{
    use HasFactory;
    protected $table = 'file_loges';

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'file_id',
        'user_id',
        'group_id',
        'action',
        'details',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
