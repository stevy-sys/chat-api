<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    public function membres() {
        return $this->hasMany(Membre::class,'conversation_id');
    }

    public function lastMessage() {
        return $this->hasOne(Message::class,'conversation_id')->latest();
    }

    public function talked() {
        return $this->hasOne(Membre::class,'conversation_id')->where('user_id','<>',Auth::id());
    }

    public function messages() {
        return $this->hasMany(Message::class,'conversation_id');
    }
}
