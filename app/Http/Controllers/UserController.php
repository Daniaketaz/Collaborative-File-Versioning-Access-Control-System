<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function Register(RegisterRequest $request){

        $request->validated();

        $result = $this->authService->register(
            $request->name,
            $request->user_Name,
            $request->password
        );

        return response()->json([
            'message' => 'Registration successful',
            'user' => $result['user'],
            'token' => $result['token']
        ], 201);
    }

    public function Login(LoginRequest $request){
        $request->validated();
        $user = $this->authService->login($request->user_Name,$request->password);
        if($user){
            return response()->json(['message'=>'login successfully ' ,
                                    'data'=>$user]);
        }
        else{
        return response()->json(['message' => 'Invalid credentials'], 401);}

    }

    public function Logout(){
        $logout =$this->authService->Logout();
        if($logout)  return response()->json('logged out');
        else  return response()->json('something went wrong ');
    }
}
