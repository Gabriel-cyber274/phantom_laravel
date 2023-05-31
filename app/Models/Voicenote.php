<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voicenote extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'name',
        'file_path'
    ];

    public function message () {
        return $this->belongsTo(RoomMessage::class, 'message_id', 'id');
    }
}
