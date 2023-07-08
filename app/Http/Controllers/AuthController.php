<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validation des informations de connexion
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        $email = User::where('email',$request->input('email'))->first();
        $error = [] ;
        if (!isset($email)) {
            $error[] = "email n'existe pas";
            return $this->sendError('erreur email',$error,401);
        }
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token-name')->plainTextToken;

            return $this->sendResponse(compact('user','token'),'user connected');
        }
        $error[] = "mot de passe invalide";
        return $this->sendError('erreur password',$error,401);
    }

    public function register(Request $request) {
        
    }

    public function notAuth() {
        return response()->json(['message' => 'unauthorized'], 401);
    }
}
