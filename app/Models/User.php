<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_id',
        'location',
        'nickname',
        'tutorial',
        'question',
        'answer',
    ];

    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'avatar_id' => 'integer',
        'tutorial'=> 'integer'
    ];

    public function AnonymousLinks () {
        return $this->hasOne(Anonymous::class, 'user_id', 'id');
    }

    public function rooms () {
        return $this->hasMany(rooms::class, 'user_id', 'id');
    }

    public function invite () {
        return $this->hasMany(Invite::class, 'user_id', 'id');
    }


}
