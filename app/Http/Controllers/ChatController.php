<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\NewMessage;
use App\Models\Conversation;
use App\Service\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public $chatService ;

    public function __construct(){
        $this->chatService = new ChatService();
    }


    /**
     * @OA\Get(
     *      path="/api/all-user",
     *      operationId="allUsers",
     *      tags={"User"},
     *      summary="Recupere tout les user",
     *      description="Recupere tout les users",
     *      @OA\Response(
     *          response=200,
     *          description="Recupere tout user",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function allUsers() {
        $users = $this->chatService->getAllUser();
        return $this->sendResponse(true,$users,'All Users');
    }



    /**
     * @OA\Get(
     *      path="/api/all-conversation",
     *      operationId="allConversation",
     *      tags={"Chat"},
     *      summary="Recupere tout les conversations",
     *      description="Recupere tout les conversations",
     *      @OA\Response(
     *          response=200,
     *          description="Recupere tout conversations",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function allConversation() {
        $conversation = $this->chatService->listConversation(Auth::user());
        return $this->sendResponse(true,$conversation,'Conversation');
    }


    /**
     * @OA\Get(
     *      path="/api/all-discussion/{idConversation}",
     *      operationId="allDiscussion",
     *      tags={"Chat"},
     *      summary="Obtenir tout les message",
     *      description="Retourne tout les message a partir une conversation",
     *      @OA\Parameter(
     *          name="idConversation",
     *          in="path",
     *          required=true,
     *          description="ID du conversation",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Liste des messages d une conversation",
     *          @OA\JsonContent(
     *              type="object"
     *          )
     *      )
     * )
     */
    public function allDiscussion(Conversation $idConversation) {
        $conversation = $this->chatService->listMessage($idConversation);
        return $this->sendResponse(true,$conversation,'Conversation');
    }





    /**
     * @OA\Post(
     *      path="/api/send-message",
     *      operationId="createMessage",
     *      tags={"Chat"},
     *      summary="Envoyer un message",
     *      description="Envoyer un message",
     *      @OA\RequestBody(
     *          description="Données du utilisatuer à envoyer",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="conversation_id",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="mon message"
     *              ),
     *              @OA\Property(
     *                  property="type",
     *                  type="string",
     *                  example="tapez 'prive' ou 'groupe'"
     *              ),
     *              @OA\Property(
     *                  property="user_id",
     *                  type="array",
     *                  @OA\Items(type="integer"),
     *                  example="[1,2,3]"
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="sera"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Envoyer un message",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
    public function createMessage(Request $request) {
        $message = null ;
        $conversation = null;
        if (!isset($request->conversation_id)) {
            if (($request->type == 'prive')) {
                $user = User::find($request->user_id[0]);
                $conversation = $this->chatService->verifConversationPrive($user,Auth::user());
                if (!isset($conversation)) {
                    $conversation = $this->chatService->createConversation($request->name,$request->type);
                    $membres[] = ['user_id' => Auth::id()] ;
                    foreach ($request->user_id as $user_id) {
                        $membres[] = ['user_id' => $user_id];
                    }
                    $membres = $this->chatService->createMembre($membres,$conversation);
                }
            }else{
                $conversation = $this->chatService->createConversation($request->name,$request->type);
                $membres[] = ['user_id' => Auth::id()] ;
                foreach ($request->user_id as $user_id) {
                    $membres[] = ['user_id' => $user_id];
                }
                $membres = $this->chatService->createMembre($membres,$conversation);
            }
            $message = $this->chatService->createMessage($conversation,$request->message,Auth::user());
        }else{
            $conversation = Conversation::find($request->conversation_id);
            $message = $this->chatService->createMessage($conversation,$request->message,Auth::user());
        }
        $message = $message->load(['user','conversation.talked.user','conversation.membres','conversation.lastMessage']);
        $conversation = $conversation->load(['talked.user','membres.user','lastMessage']);
        if ($conversation->type == 'prive') {
            NewMessage::dispatch($message,$conversation,$conversation->talked->user);
        }else{
            foreach ($conversation->membres as $membre) {
                if ($membre->user_id != Auth::id()) {
                    NewMessage::dispatch($message,$conversation,$membre->user);
                }
            }
        }
        return $this->sendResponse(true,$message,'Message creer');
    }
}
