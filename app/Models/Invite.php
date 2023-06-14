<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invited_id'
    ];

        
    protected $casts = [
        'user_id' => 'integer',
        'invited_id' => 'integer',
    ];

}
