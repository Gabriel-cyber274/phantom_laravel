<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anonymous extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        
    ];

    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function messages() {
        return $this->belongsToMany(AnonymousMessage::class, 'anon_message', 'anon_id', 'message_id');
    }

}
