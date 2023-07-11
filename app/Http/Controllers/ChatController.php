<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Service\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public $chatService ;

    public function __construct(){
        $this->chatService = new ChatService();
    }

    public function allUsers() {
        $users = $this->chatService->getAllUser();
        return $this->sendResponse(true,$users,'All Users');
    }

    public function allConversation() {
        $conversation = $this->chatService->listConversation(Auth::user());
        return $this->sendResponse(true,$conversation,'Conversation');
    }

    public function allDiscussion(Conversation $idConversation) {
        $conversation = $this->chatService->listMessage($idConversation);
        return $this->sendResponse(true,$conversation,'Conversation');
    }

    public function createMessage(Request $request) {
        $message = null ;
        if (!isset($request->conversation_id)) {
            $conversation = $this->chatService->createConversation($request->name,$request->type);
            $membres[] = ['user_id' => Auth::id()] ;
            foreach ($request->user_id as $user_id) {
                $membres[] = ['user_id' => $user_id];
            }
            $membres = $this->chatService->createMembre($membres,$conversation);
            $message = $this->chatService->createMessage($conversation,$request->message,Auth::user());
        }else{
            $conversation = Conversation::find($request->conversation_id);
            $message = $this->chatService->createMessage($conversation,$request->message,Auth::user());
        }
        $message = $message->load('user');
        return $this->sendResponse(true,$message,'Message creer');
    }
}
