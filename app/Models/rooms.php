<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rooms extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_name',
        'creator_id',
        'creator_avatar',
        'block',
        'report',
        'reveal',
        'links'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function messages () {
        return $this->belongsToMany(RoomMessage::class, 'room_chats', 'room_id', 'chat_id');
    }

}

