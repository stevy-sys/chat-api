<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\AuthService;

use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public $authService ;

    public function __construct(){
        $this->authService = new AuthService();
    }

    public function login(Request $request){
        $email = User::where('email',$request->input('email'))->first();
        $error = [] ;
        if (!isset($email)) {
            $error[] = "email n'existe pas";
            return $this->sendError('erreur email',$error,401);
        }
    
        if (Auth::attempt($request->all())) {
            $user = Auth::user();
            return  $this->finalResponse($user);
        }
        $error[] = "mot de passe invalide";
        return $this->sendError('erreur password',$error,401);
    }

    public function register(Request $request) {
        $error = $this->authService->validateRegister($request);
        if (count($error) > 0) {
            return $this->sendError('erreur inscription',$error,401);
        }
        $user = $this->authService->createUser($request->name,$request->email,$request->password);
        return  $this->finalResponse($user);
    }

    private function finalResponse($user) {
        $token = $this->authService->createToken($user);
        return $this->sendResponse(compact('user','token'),'user connected'); 
    }

    public function notAuth() {
        return response()->json(['message' => 'unauthorized'], 401);
    }
}
