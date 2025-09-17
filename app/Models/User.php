<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_Name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function checks(){
        return $this->hasMany(Check::class,'user_id');
    }
    public function user_groups(){
        return $this->hasMany(User_Group::class,'user_id');
    }
    public function groupsAsAdmin(){
        return $this->hasMany(Group::class,'admin_id');
    }
    public function joinRequest(){
        return $this->hasMany(JoinGroup::class,'user_id');
    }
    public function isAdmin()
    {
        return $this->id === 1;
    }
}
