<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Championship;
use App\Models\ChampionshipPlayer;


class ChampionshipsController extends Controller
{
    public function createChampionships(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'players' => 'required|array',
                'id_type_championship'=> 'required'

            ],[
                'name.required' => 'Name of championship required.',
            ]);

            if ($validator->fails()) {
                return response()->json(array("message"=>count($validator->errors())." errors were found", "errors"=>array($validator->errors())) ,422);
            }

            $idPlayer = $request['player']['id'];

            $championship = Championship::create([
                'name' => $request->name, 
                'id_player_host' => $idPlayer,
                'id_type_championship'=> $request->id_type_championship   
            ]);

            foreach($request->players as $player){
                $players[] = ChampionshipPlayer::create([
                    'id_championship' => $championship->id, 
                    'id_player' => $player,
                ]);
            }

            return response()->json(array("message"=>"Created with success", "data"=>array("championship"=>$championship, "players"=>$players)), 201);
        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
}
