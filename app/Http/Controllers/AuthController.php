<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\AuthService;

use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use App\Events\NewUserJoinedPresence;

class AuthController extends Controller
{
    public $authService ;

    public function __construct(){
        $this->authService = new AuthService();
    }

    public function login(Request $request){
        try {
            $email = User::where('email',$request->input('email'))->first();
            $error = [] ;
            if (!isset($email)) {
                $error[] = "email n'existe pas";
                

                // Renvoyez la réponse
                $response =  response()->json([
                    'status' => 'error'
                ],200);
                $response->header("Access-Control-Allow-Origin", "*");
                $response->header("Access-Control-Allow-Credentials", "true");
                $response->header("Access-Control-Max-Age", "600"); // Cache pendant 10 minutes
                $response->header("Access-Control-Allow-Methods", "POST, GET, OPTIONS, DELETE, PUT"); // Assurez-vous de supprimer ceux que vous ne souhaitez pas prendre en charge
                $response->header("Access-Control-Allow-Headers", "Content-Type, Accept, Authorization, X-Requested-With, Application"); // Vous pouvez inclure les en-têtes que vous voulez prendre en charge
                $response->header("Referer-Policy", "*"); // Vous pouvez inclure les en-têtes que vous voulez prendre en charge
                return $response;
            }
        
            if (Auth::attempt($request->all())) {
                $user = Auth::user();
                event(new NewUserJoinedPresence('connected',$user));
                return  $this->finalResponse($user);
            }
            $error[] = "mot de passe invalide";
            return $this->sendResponse(false,$error,'erreur password');
            
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(),[],500);
        }
    }

    public function register(Request $request) {
        $error = $this->authService->validateRegister($request);
        if (count($error) > 0) {
            return $this->sendResponse(false,$error,'erreur inscription');
        }
        $user = $this->authService->createUser($request->name,$request->email,$request->password);
        return  $this->finalResponse($user);
    }

    private function finalResponse($user) {
        $token = $this->authService->createToken($user);
        return $this->sendResponse(true,compact('user','token'),'user connected'); 
    }

    public function notAuth() {
        return response()->json(['message' => 'unauthorized'], 401);
    }
}
