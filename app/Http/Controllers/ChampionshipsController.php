<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Championship;
use App\Models\ChampionshipPlayer;
use App\Models\ChampionshipMatch;
use App\Models\Player;


class ChampionshipsController extends Controller
{
    public function createChampionships(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'players' => 'required|array|min:2',
                'id_type_championship'=> 'required'

            ],[
                'name.required' => 'Name of championship required.',
            ]);

            if ($validator->fails()) {
                return getReturnErrorsValidator($validator); //helper function
            }

            if(log(count($request->players),2)%1 && count($request->players)>1){
                return response()->json(array("message"=>"select a valid amount of players", "errors"=>array("number of players not supported for a championship in this format must be a potential number of 2")) ,422);
            };
            $idPlayer = $request['player']['id'];

            $championship = Championship::create([
                'name' => $request->name, 
                'id_player_host' => $idPlayer,
                'id_type_championship'=> $request->id_type_championship, 
                'round_total' => log(count($request->players),2)
            ]);

            foreach($request->players as $player){
                $players[] = ChampionshipPlayer::create([
                    'id_championship' => $championship->id, 
                    'id_player' => $player,
                ]);
            }
            
            $championship->players=$players;

            return response()->json(array("message"=>"Created with success", "data"=>array("championship"=>$championship)), 201);
        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    public function createMatch(Request $request, $id){
        try {

            $idPlayer = $request['player']['id'];
           
            $championship =  $this->championshipBelongsPlayer($id, $idPlayer);
            if(!$championship){
                return response()->json(array("message"=>"permission denied for this championship", "errors"=>array("this championship does not belong to this user")), 401);
            }
            
            if(Championship::find($id)->championshipMatch->where('id_player_win', null)->count()){
                return response()->json(array("message"=>"unfinished matches", "errors"=>array("This round still has pending matches")), 401);
            }


            if($championship['round_current']+1 <= $championship['round_total'] ){
                Championship::where('id',$id)->update([
                    'round_current' => $championship['round_current']+1,
                ]);    
            }else{

                $finalMatchs = array_values(Championship::find($id)->championshipMatch->where('round',$championship->round_current)->sortBy("group")->toArray());

                $playerWin = Player::find($finalMatchs[0]['id_player_win']);
                
                return response()->json(array("message"=>"championship ended the winner was ".$playerWin->name, "data"=>array("player_win"=>$playerWin)), 201);
            }
            

            $playersMatch = array_values(Championship::find($id)->championshipMatch->where('round',$championship->round_current)->sortBy("group")->toArray());
            
            
            if(count($playersMatch) == 0){

                $players = Championship::find($id)->championshipPlayer;
                $arrayPlayers = $players->toArray();
                shuffle($arrayPlayers);

                for($i = 0 ; count($arrayPlayers) > $i ; $i+=2) {
                    ChampionshipMatch::create([
                        'id_championship' => $id,
                        'id_player_a' => $arrayPlayers[$i]['id_player'], 
                        'id_player_b' => $arrayPlayers[$i+1]['id_player'],
                        'group' => ($i/2)+1,
                        'round' => $championship['round_current']+1,
                        'points' => $arrayPlayers[$i]['ppm']
                    ]);
                }

                $currentMatch = Championship::find($id)->championshipMatch->where('round', $championship['round_current']+1);
                return response()->json(array("message"=>"Match round started with success", "data"=>array("matchs"=>$currentMatch)), 201);
            
            }
            if(count($playersMatch) >= 2){

                for($i = 0 ; count($playersMatch) > $i ; $i+=2) {
                    $player = ChampionshipPlayer::where('id_championship', $championship['id'])
                                                ->where('id_player', $playersMatch[$i]['id_player_win'])
                                                ->get()->first();
    
                    ChampionshipMatch::create([
                        'id_championship' => $id,
                        'id_player_a' => $playersMatch[$i]['id_player_win'], 
                        'id_player_b' => $playersMatch[$i+1]['id_player_win'],
                        'group' => ($i/2)+1, 
                        'round' => $championship['round_current']+1,
                        'points' => $player->ppm
                    ]);
                }

                $currentMatch = Championship::find($id)->championshipMatch->where('round', $championship['round_current']+1);
                return response()->json(array("message"=>"Match round started with success", "data"=>array("matchs"=>$currentMatch)), 201);

            }

        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    public function listCurrentMatchChampionship(Request $request, $id){

        $idPlayer = $request['player']['id'];

        $championship =  $this->championshipBelongsPlayer($id, $idPlayer);
        if(!$championship){
            return response()->json(array("message"=>"permission denied for this championship", "errors"=>array("this championship does not belong to this user")), 401);
        }

        $currentMatch = Championship::find($id)->championshipMatch->where('round', $championship->round_current);
        
        if(count($currentMatch) == 0){
            return response()->json(array("message"=>"no matches happening at the moment","data"=>["matchs"=>array_values($currentMatch->toArray())]), 200);//204
        }

        return response()->json(array("data"=>["matchs"=>array_values($currentMatch->toArray())]), 200);

        
    }
    public function endMatch(Request $request, $id, $idMatch){
        try {
             $validator = Validator::make($request->all(), [
                'id_player_win'=> 'required|integer',
            ],[
                'id_player_win.required' => 'id_player_win is required.',
                'id_player_win.integer' => 'must be integer value'
            ]);
            
            if ($validator->fails()) {
                return getReturnErrorsValidator($validator); //helper function
            }

            $idPlayer = $request['player']['id'];

            $championship =  $this->championshipBelongsPlayer($id, $idPlayer);
            if(!$championship){
                return response()->json(array("message"=>"permission denied for this championship", "errors"=>array("this championship does not belong to this user")), 401);
            }
            
            $championshipMatch = Championship::find($id)
                                            ->championshipMatch
                                            ->where('id', $idMatch)
                                            ;
            $championshipMatchData = array_values($championshipMatch->toArray());
            
            if(!$championshipMatchData){
                return response()->json(array("message"=>"permission denied for this match", "errors"=>array("this match does not belong to this user")), 401);
            }
            if($championshipMatchData[0]['id_player_win'] != null){
                return response()->json(array("message"=>"finished match", "errors"=>array("this game has already ended")), 200);//304
            }
            if($championshipMatchData[0]['id_player_a'] != $request->id_player_win && $championshipMatchData[0]['id_player_b'] != $request->id_player_win){
                return response()->json(array("message"=>"id_player_win invalid", "errors"=>array("player does not participate in this game")), 422);
            }

            ChampionshipMatch::where('id', $idMatch)->update([
                'id_player_win' => $request->id_player_win
            ]);
            ChampionshipPlayer::where('id_championship',$id)
                              ->where('id_player',$request->id_player_win)
                              ->increment('points',$championshipMatchData[0]['points']);
            $playerLoser = $request->id_player_win==$championshipMatchData[0]['id_player_a'] ?$championshipMatchData[0]['id_player_b']:$championshipMatchData[0]['id_player_a'];

            $championshipPlayer = ChampionshipPlayer::where('id_championship',$id)
                                                    ->where('id_player',$playerLoser)
                                                    ->first();

            $championshipPlayer->defeats += 1;
            $championshipPlayer->ppm = $championshipPlayer->ppm/(pow(2, $championshipPlayer->defeats));
            $championshipPlayer->save();

            return response()->json(array("data"=>ChampionshipMatch::find($idMatch)), 200);

        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    /**
     * Internal Functions
     */
    protected function championshipBelongsPlayer($id, $idPlayerHost){
         $championship = Championship::where('id',$id)
                                    ->where('id_player_host', $idPlayerHost)
                                    ->get()
                                    ->first();

        if(!$championship){
            return false;
        }
        return $championship;
    }
}
