<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMessage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'sender_id',
        'message',
        'type',
        'room_id',
        'seen',
        'reply_id',
        'reply_message',
        'message_id'
    ];

    public function rooms () {
        return $this->belongsToMany(rooms::class, 'room_chats', 'chat_id', 'room_id');
    }

    public function voicenote () {
        return $this->hasOne(Voicenote::class, 'message_id', 'id');
    }

}
