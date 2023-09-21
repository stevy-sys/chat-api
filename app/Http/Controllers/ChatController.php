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
