<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request ,$id){
        return ['sss','olas', $id];
    }
    public function test(Request $request){
        $nameUser = $request->input('name');
        $dataAll = $request->all();
        $data = new \stdClass;
        $data->name  = $nameUser;
        $data->pass  = Hash::make($request->pass);
        $data->email = $dataAll['email'];
        dump($dataAll);
        //
        $user = User::where('active', 1)
        ->where('id', 1)
        ->orderBy('name')
        ->take(10)
        ->get();
        dump($user);
        dump($user[0]->name);
        //
        $user = new User;
        $user->name      = $data['name'];
        $user->email     = $data['email'];
        $user->password  = $data['pass'];
        $user->save();
        $user->id;
        //
        $validUser = User::where('email', $data['email'])
        ->get();
        dump($validUser);
        foreach($validUser as $users){
            dump($users['name']);
        }
        return ['sss','olas', $nameUser,  $dataAll,  $dataAll['name']];
    }
    public function read(Request $request, $id){
        $user = User::where('active', 1)
        ->where('id', $id)
        ->get()
        ->first();
        if(!$user){
            return response()->json(array("menssage"=>"User not found"), 404);
        }
        return response()->json($user, 200);
    }
    public function create(Request $request){
        $data = $request->all();
        $validUser = User::where('email', $data['email'])
        ->get()
        ->first();
        
        if($validUser){
            return response()->json(array("menssage"=>"already exists"), 409);
        }
        $user = new User;
        $user->name      = $data['name'];
        $user->email     = $data['email'];
        $user->password  = Hash::make($data['pass']);
        $user->save();
        return response()->json(array("menssage"=>"created with success", "data"=>$user), 201);
        
    }
    public function update(Request $request){
        $nameUser = $request->input('name');
        $dataAll = $request->all();
        $data = new \stdClass;
        $data->name  = $nameUser;
        $data->pass  = $dataAll['pass'];
        $data->email = $dataAll['email'];
        return  $data;
        return ['sss','olas', $nameUser,  $dataAll,  $dataAll['name']];
    } 
    public function delete(Request $request){
        $nameUser = $request->input('name');
        $dataAll = $request->all();
        $data = new \stdClass;
        $data->name  = $nameUser;
        $data->pass  = $dataAll['pass'];
        $data->email = $dataAll['email'];
        return  $data;
        return ['sss','olas', $nameUser,  $dataAll,  $dataAll['name']];
    }
}
