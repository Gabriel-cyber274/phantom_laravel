<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnonymousMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'anonymous_id',
        'message',
        'review',
        'hint',
        'hint_text',
    ];

    
    protected $casts = [
        'anonymous_id' => 'integer',
    ];


    public function anonymous() {
        return $this->belongsToMany(AnonymousMessage::class, 'anon_message', 'message_id', 'anon_id');
    }
}
