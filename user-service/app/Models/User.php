<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Wajib untuk JWT — mengembalikan primary key user
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Custom claims yang ikut dalam payload token
    public function getJWTCustomClaims()
    {
        return [
            'name'  => $this->name,
            'email' => $this->email,
        ];
    }
}
