<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampionshipPlayer extends Model
{
    use HasFactory;

    protected $table = 'championship_players';

    protected $primaryKey = 'id';

    protected $fillable = ['id_championship', 'id_player', 'points', 'defeats', 'ppm' ];


    public function championship(){
        return $this->belongsTo(Championship::class, 'id_championship');
    }
    
}
