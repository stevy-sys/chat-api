<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\AuthService;

use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use App\Events\NewUserJoinedPresence;

/**
 * @OA\Info(
 *     title="chat-api",
 *     version="1.0.0",
 *     description="api de chat entre utilisateur",
 *     @OA\Contact(
 *         email="stevyralambomanana@gmail.com",
 *         name="stevy"
 *     ),
 *     @OA\License(
 *         name="Licence de l'API",
 *         url="URL de la licence"
 *     )
 * )
 */
class AuthController extends Controller
{
    public $authService ;

    public function __construct(){
        $this->authService = new AuthService();
    }



    /**
     * @OA\Post(
     *      path="/api/connexion",
     *      operationId="login",
     *      tags={"Authentication"},
     *      summary="Connexion utilisateur",
     *      description="Retourne le donnee de user avec token authentification",
     *      @OA\RequestBody(
     *          description="Données du utilisatuer à envoyer",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  example="JohnDoe@gmail.com"
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *                  example="votre mot de passe"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User connecter",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     *      security={}
     * )
     */
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


    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="register",
     *      tags={"Authentication"},
     *      summary="inscription de utilisateur",
     *      description="Inscription de nouvel utilisateur",
     *      @OA\RequestBody(
     *          description="Données du utilisatuer à envoyer",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  example="JohnDoe@gmail.com"
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="JohnDoe"
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *                  example="votre mot de passe"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Connexion avec success",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     *      security={}
     * )
     */
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

     /**
     * @OA\Get(
     *      path="/api/deconnect",
     *      operationId="deconnect",
     *      tags={"Authentication"},
     *      summary="Recupere tout les amis",
     *      description="Recupere tout les amis que jai accepter",
     *      @OA\Response(
     *          response=200,
     *          description="Recupere tout les amis",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function deconnect() {
        event(new NewUserJoinedPresence('deconnected', Auth::user()));
        return response()->json([
            'message' => 'deconnecte'
        ]);
    }
}
