<?php
namespace App\Service;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;

class ChatService
{

    public function getAllUser() {
        return [
            'user' => User::with('profile')->get()
        ];
    }

    public function createConversation($name = null,$type=null) {
        $conversation = Conversation::create([
            'name' => isset($name) ? $name : 'sera' ,
            'type' => isset($type) ? $type : 'prive',
        ]);
        return $conversation ;
    }

    public function createMembre(array $membres,$conversation) {
        $new = $conversation->membres()->createMany($membres);
        return $new ;
    }

    public function createMessage($conversation,$message,$user) {
        $message = $conversation->messages()->create([
            'message' => $message,
            'user_id' => $user->id,
        ]);
        return $message ;
    }

    public function verifConversationPrive($user,$auth) {
        $conversation = Conversation::where('type','prive')->whereHas('membres',function ($q) use($user) {
            $q->where('user_id',$user->id);
        })->whereHas('membres',function ($q) use($auth) {
            $q->where('user_id',$auth->id);
        })->first();
        if (isset($conversation)) 
            return $conversation ;
        return null ;
    }

    public function listConversation($auth)  {
        $conversationPrive = Conversation::where('type','prive')->whereHas('membres',function ($q) use($auth) {
            $q->where('user_id',$auth->id);
        })->with(['lastMessage','talked.user','membres'])->orderByDesc(function ($query) {
            $query->select('created_at')
                ->from('messages')
                ->whereColumn('conversation_id', 'conversations.id')
                ->orderBy('created_at', 'desc')
                ->limit(1);
        })->get();

        $conversationGroupe = Conversation::where('type','groupe')->whereHas('membres',function ($q) use($auth) {
            $q->where('user_id',$auth->id);
        })->with('lastMessage','membres')->get();
        
        return compact('conversationPrive','conversationGroupe') ;
    }

    public function listMessage($conversation)  {
        $messages = Message::with('user')->where('conversation_id',$conversation->id)->get();
        return $messages ;
    }

    public function deleteMessage($message_id) {
        Message::find($message_id)->delete();
        return true ;
    }
}
