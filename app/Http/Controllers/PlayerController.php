<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Player;


class PlayerController extends Controller
{
    public function index(Request $request){
        try {
            return Player::orderBy('name')
            ->take(10)
            ->get();
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }
    public function created(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email',
                'password'=> 'required'
            ],[
                'name.required' => 'Name is required.',
            ]);
     
            if ($validator->fails()) {
                return response()->json(array("message"=>count($validator->errors())." errors were found", "errors"=>array($validator->errors())) ,422);
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
            return response()->json(array("message"=>"Created with success", "data"=>$player), 201);
        } catch (\Throwable $th) {
            return response()->json(array("message"=>"an unexpected error occurred","errors"=>array($th->getMessage())), 400) ;
        }
    }
    public function update(Request $request){
        try {
            $idPlayer = $request['player']['id'];
            
            Player::where('id', $idPlayer)
                            ->update([
                                'name' => $request->name, 
                                'password' => $request->pass?Hash::make($request->pass):$request['player']['password'],
                            ]);

            $player = Player::where('id',  $idPlayer)->first();
            
            return response()->json(array("message"=>"Updated with success", "data"=>$player), 200);
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


}
