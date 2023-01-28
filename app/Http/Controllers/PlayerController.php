<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Player;

use App\Services\PaginationService;
use Illuminate\Support\Facades\DB;


class PlayerController extends Controller
{
    public function index(Request $request){
        try {
            
            $validator = Validator::make($request->all(), [
                'page' => 'integer',
                'per_page' => 'integer'
            ]);

            if ($validator->fails()) return getReturnErrorsValidator($validator); //helper function

            $idPlayer = $request['player']['id'];
            $page = $request->query('page',1);
            $perPage = $request->query('per_page',5);
            $sort = $request->query('sort',null);
            $filter = $request->query('filter',[]);

            if (!PaginationService::validSort($sort, ['name', 'id'])){
                return response()->json(array("message"=>"1 errors were found","errors"=>["invalid attribute or format of sort"]), 400) ;
            }

            if (!PaginationService::validFilter($filter, ['name'])){
                return response()->json(array("message"=>"1 errors were found","errors"=>["invalid attribute or format of filter"]), 400) ;
            }

            $playersData = $this->players($idPlayer,$page, $perPage, $sort, $filter);
            $totalPlayer = $this->totalPlayers($idPlayer, $filter);

            $meta =  PaginationService::transformMeta($page, $perPage, $totalPlayer);

            return response()->json(array(
                                        "meta"=>$meta, 
                                        "data"=>["players"=>$playersData]
                                    ), 200);

        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
        
    }
    public function created(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:5|max:255',
                'email' => 'required|email',
                'password'=> 'required'
            ],[
                'name.required' => 'Name is required.',
            ]);
     
            if ($validator->fails()) {
                return getReturnErrorsValidator($validator); //helper function
            }
           
            $playerEmailUsed = Player::where('email',  $request->email)->first();
            if($playerEmailUsed){
                return response()->json(array("message"=>"1 errors were found", "errors"=>array("email already used")), 422);
            }
            
            $player = Player::create([
                                        'name' => $request->name, 
                                        'email' => $request->email, 
                                        'password' => Hash::make($request->password)
                                    ]);

            return response()->json(array("message"=>"Created with success", "data"=>["player"=>Player::Find($player->id)]), 201);
        
        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    public function update(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
            ]);
            
            if ($validator->fails()) {
                return getReturnErrorsValidator($validator); //helper function
            }
            
            $idPlayer = $request['player']['id'];
            
            Player::where('id', $idPlayer)
                            ->update([
                                'name' => $request->name, 
                                'password' => $request->pass?Hash::make($request->pass):$request['player']['password'],
                            ]);

            $player = Player::where('id',  $idPlayer)->first();
            
            return response()->json(array("message"=>"Updated with success", "data"=>["player"=>$player]), 200);
        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    public function secret(Request $request){
        try {
            return response()->json(array("message"=>"oi"));
        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    /**
     * Internal Functions
     */
    protected function players($idPlayer, $page, $itensPerPage, $sort, $filter){

        $startPosition = PaginationService::itemStartPage($page, $itensPerPage);

        $sort = PaginationService::querySort($sort);
        $filter = PaginationService::queryFilter($filter, ['name'], "AND");        

        return  DB::select("SELECT 
                                DISTINCT(p.id), 
                                p.name,
                                f.accept
                            FROM players p
                            LEFT JOIN friends f ON (f.id_player_recived = ? OR f.id_player_send = ?) 
                                                AND (f.id_player_recived = p.id OR f.id_player_send = p.id)
                            WHERE p.id != ? $filter
                            $sort
                            LIMIT $startPosition, $itensPerPage", [$idPlayer, $idPlayer, $idPlayer]);
                        
    }

    protected function totalPlayers($idPlayer, $filter){
        
        $filter = PaginationService::queryFilter($filter, ['name'], "AND");    
               
        $totalItens = DB::select("SELECT 
                                    count(DISTINCT(p.id)) as total
                                FROM players p
                                LEFT JOIN friends f ON (f.id_player_recived = ? OR f.id_player_send = ?) 
                                                    AND (f.id_player_recived = p.id OR f.id_player_send = p.id)
                                WHERE p.id != ? $filter", [$idPlayer, $idPlayer, $idPlayer]);
        
        return $totalItens[0]->total;
    }


}
