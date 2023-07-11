<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService 
{
    public function validateRegister($request) : Array
    {
        $error = [] ;
        if (!isset($request->email)) {
            $error['email'][] = 'email invalide ';
        }
        $user = User::where('email',$request->email)->first();
        if (isset($user)) {
            $error['email'][] = 'email deja pris ';
        }
        if (!isset($request->password)) {
            $error['password'][] = 'password invalide ';
        }
        if (!isset($request->name)) {
            $error['name'][] = 'name invalide ';
        }
        if (!isset($request->confirm_password)) {
            $error['confirm_password'][] = 'confirm_password invalide ';
        }
        if (isset($request->confirm_password) && isset($request->password) && ($request->confirm_password !== $request->password)) {
            $error['error'][] = 'password not match ';
        }
        return $error ; 
    }

    
    public function createToken($user) : String {
        $token = $user->createToken('token-name')->plainTextToken;
        return $token ;
    }

    public function createUser($name,$email,$password) : User {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password) ,
        ]);
        return $user ;
    }
}
