<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService{

    public function Login($UserName,$Password){
        if (Auth::attempt(['user_Name' => $UserName, 'password' => $Password])) {
            return  auth()->user()->createToken('token')->accessToken;
        }
        else {
           return null;
        }


    }

    public function Register($Name , $UserName,$Password){
        $user = new User([
            'name' => $Name,
            'user_Name' => $UserName,
            'password' => Hash::make($Password),
        ]);

        $token = $user->createToken('token')->accessToken;
            $user->save();
        return ['user' => $user, 'token' => $token];
    }

    public function Logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        return true;
    }

}


