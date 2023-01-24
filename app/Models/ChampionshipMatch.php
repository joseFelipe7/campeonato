<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampionshipMatch extends Model
{
    use HasFactory;
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'championship_matchs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_championship', 'id_player_a', 'id_player_b', 'id_player_win', 'group', 'round', 'points' ];

    public function championship()
    {
        return $this->belongsTo(Championship::class, 'id_championship');
    }
    
}