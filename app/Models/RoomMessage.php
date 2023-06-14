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

      
    protected $casts = [
        'user_id' => 'integer',
        'sender_id' => 'integer',
        'room_id' => 'integer',
        'reply_id' => 'integer',
        'message_id' => 'integer',
        'seen' => 'integer',
    ];

    public function rooms () {
        return $this->belongsToMany(rooms::class, 'room_chats', 'chat_id', 'room_id');
    }

    public function voicenote () {
        return $this->hasOne(Voicenote::class, 'message_id', 'id');
    }

}
