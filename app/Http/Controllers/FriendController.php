<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Friend;
use App\Models\Player;
use App\Services\PaginationService;


class FriendController extends Controller
{
    public function listFriends(Request $request){
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

            $friendsData = $this->friendsConfirmed($idPlayer,$page, $perPage, $sort, $filter);
            $totalFriend = $this->totalFriendsConfirmed($idPlayer, $filter);

            $meta =  PaginationService::transformMeta($page, $perPage, $totalFriend);

            return response()->json(array(
                                        "meta"=>$meta, 
                                        "data"=>["friends"=>$friendsData]
                                    ), 200);

        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    public function listRecived(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'page' => 'integer',
                'per_page' => 'integer'
            ]);

            if ($validator->fails()) {
                return getReturnErrorsValidator($validator); //helper function
            }
            
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


            $friendsData = $this->friendsRecived($idPlayer,$page, $perPage, $sort, $filter);
            $totalFriend = $this->totalFriendsRecived($idPlayer, $filter);

            $meta = PaginationService::transformMeta($page, $perPage, $totalFriend);

            return response()->json(array(
                                        "meta"=>$meta, 
                                        "data"=>["friends"=>$friendsData]
                                    ), 200);

        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    public function sendInviteFriend(Request $request){
        $validator = Validator::make($request->all(), [
            'id_player' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return getReturnErrorsValidator($validator); //helper function
        }

        $idPlayer = $request['player']['id'];
        $idPlayerFriend = $request->id_player;

        if(!Player::find($idPlayerFriend)) return response()->json(array("message"=>"non-existent player", "errors"=>array("player id not found in database")), 422);
        
        if( Friend::where('id_player_send', $idPlayer)->where('id_player_recived', $idPlayerFriend)->count()){
            return response()->json(array("message"=>"existing friend request", "errors"=>array("friend request already sent")), 422);
        }
        if( Friend::where('id_player_send', $idPlayerFriend)->where('id_player_recived', $idPlayer)->count()){
            return response()->json(array("message"=>"existing friend request", "errors"=>array("friend request already received")), 422);
        }

        $friend = Friend::create([
            'id_player_send' => $idPlayer,  
            'id_player_recived' => $idPlayerFriend
        ]);

        return response()->json(array("message"=>"Created with success", "data"=>["friend"=>Friend::Find($friend->id)]), 201);

    }
    public function responseInviteFriend(Request $request, $id){
        
        $idPlayer = $request['player']['id'];
        
        $friend = Friend::find($id);

        if(!$friend) return response()->json(array("message"=>"non-existent friend invite", "errors"=>array("friend id not found in database")), 422);
        
        if(!($friend->id_player_recived == $idPlayer)) return response()->json(array("message"=>"permission denied for this invite", "errors"=>array("this invite friend does not belong to this user")), 401);

        if($friend->accept != 0) return response()->json(array("message"=>"invitation already answered", "errors"=>array("this friend request has already been answered")), 422);

        $friend->accept = 1;
        $friend->save();
        
        return response()->json(array("message"=>"Updated with success", "data"=>["friend"=>$friend]), 200);

    }
    /**
     * Internal Functions
     */
    
    protected function friendsConfirmed($idPlayer, $page, $itensPerPage, $sort, $filter){
       
        $startPosition = PaginationService::itemStartPage($page, $itensPerPage);

        $sort = PaginationService::querySort($sort);
        $filter = PaginationService::queryFilter($filter, ['name'], "AND");            
        
        return DB::select("SELECT 
                                f.id,
                                p.id id_friend,
                                p.name,
                                f.*
                            FROM friends f
                            LEFT JOIN players p ON (p.id = f.id_player_send OR p.id = f.id_player_recived) AND p.id != ? 
                            WHERE (f.id_player_send = ? OR f.id_player_recived = ?) AND f.accept = 1 $filter
                            $sort
                            LIMIT $startPosition, $itensPerPage", [$idPlayer, $idPlayer, $idPlayer]
                            );

    }

    protected function totalFriendsConfirmed($idPlayer, $filter){

        $filter = PaginationService::queryFilter($filter, ['name'], "AND");

        $totalItens = DB::select("SELECT 
                                    COUNT(f.id) as total_friends
                                FROM friends f
                                LEFT JOIN players p ON (p.id = f.id_player_send OR p.id = f.id_player_recived) AND p.id != ? 
                                WHERE (f.id_player_send = ? OR f.id_player_recived = ?) AND f.accept = 1 $filter", 
                                [$idPlayer, $idPlayer, $idPlayer]
                            );
        return $totalItens[0]->total_friends;

    }
    
    protected function friendsRecived($idPlayer, $page, $itensPerPage, $sort, $filter){

        $startPosition = PaginationService::itemStartPage($page, $itensPerPage);

        $sort = PaginationService::querySort($sort);
        $filter = PaginationService::queryFilter($filter, ['name'], "AND");        

        return DB::select(
                        "SELECT 
                            f.id,
                            p.name,
                            f.*
                        FROM friends f
                        LEFT JOIN players p ON p.id = f.id_player_send
                        WHERE f.id_player_recived = ? AND f.accept = 0 $filter
                        $sort
                        LIMIT $startPosition, $itensPerPage", [$idPlayer]
                    );

    }

    protected function totalFriendsRecived($idPlayer, $filter){
        
        $filter = PaginationService::queryFilter($filter, ['name'], "AND");        
        
        $totalItens = DB::select(
                                "SELECT 
                                    COUNT(f.id) as total_friends
                                FROM friends f
                                LEFT JOIN players p ON p.id = f.id_player_send
                                WHERE f.id_player_recived = ? AND f.accept = 0 $filter", [$idPlayer]
                            );

        return $totalItens[0]->total_friends;
    }
}
