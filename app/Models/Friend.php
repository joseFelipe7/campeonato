<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $table = 'friends';

    protected $primaryKey = 'id';
   
    protected $fillable = ['id_player_send',  'id_player_recived', 'accept'];
    
    
    public function playerSend(){
        return $this->belongsTo(Player::class, 'id_player_send');
    }

    public function playerRecived(){
        return $this->belongsTo(Player::class, 'id_player_recived');
    }
}
