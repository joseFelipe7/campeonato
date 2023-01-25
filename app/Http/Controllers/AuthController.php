<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Player;
use Validator;

class AuthController extends Controller
{
    public function __construct() {
    
    }
    
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return getReturnErrorsValidator($validator); //helper function
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(array("message"=>"wrong credentials", "errors"=>["Your password is incorrect. check it out"]) ,401);
        }
        return $this->createNewToken($token);
    }

    public function logout() {
        auth()->logout();
        return response()->json(array("message"=>"User successfully signed out") ,401);
    }
    
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    
    public function userProfile() {
        return response()->json(auth()->user());
    }
    
    protected function createNewToken($token){
        return response()->json([
            "data"=>[
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => auth()->user()
            ]
        ]);
    }
}