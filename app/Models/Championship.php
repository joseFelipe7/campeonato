<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{
    use HasFactory;

    protected $table = 'championships';

    protected $primaryKey = 'id';

    protected $fillable = ['id_player_host',  'id_type_championship', 'name', 'round_current', 'round_total'];
    
    
    public function playerHost(){
        return $this->belongsTo(Player::class, 'id_player_host');
    }

    public function championshipPlayer(){
        return $this->hasMany(ChampionshipPlayer::class, 'id_championship');
    }

    public function championshipMatch(){
        return $this->hasMany(ChampionshipMatch::class, 'id_championship');
    }
}
