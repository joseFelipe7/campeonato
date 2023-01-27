<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//jwt config
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Player extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'players';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'email', 'password'];


    public function championship(){
        return $this->hasMany(Championship::class);
    }
    
    public function friendSend(){
        return $this->hasMany(Friend::class, 'id_player_send');
    }

    public function friendRecived(){
        return $this->hasMany(Friend::class, 'id_player_recived');
    }

    public function getJWTIdentifier(){
        return $this->getKey();
    }

    public function getJWTCustomClaims(){
        return [];
    }
}
