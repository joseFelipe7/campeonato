<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChampionshipsController;
 


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** Router of Players */
Route::prefix('player')->group(function () {
    Route::get('/', [PlayerController::class, 'index']);
    Route::post('/', [PlayerController::class, 'created']);
    Route::put('/', [PlayerController::class, 'update'])->middleware('AuthUser');
    Route::get('/list/{id}', [PlayerController::class, 'index']);
    Route::get('/secret', [PlayerController::class, 'secret'])->middleware('AuthUser');
    
});   
 
/** Router Auth */
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);  

});

/** Router Championship */
Route::prefix('championship')->group(function () {
    Route::post('/created', [ChampionshipsController::class, 'createChampionships'])->middleware('AuthUser');  
    Route::post('/{id}/match', [ChampionshipsController::class, 'createMatch'])->middleware('AuthUser');  
    Route::get('/{id}/match/current', [ChampionshipsController::class, 'listCurrentMatchChampionship'])->middleware('AuthUser');  
    Route::post('/{id}/match/{idMatch}/finished', [ChampionshipsController::class, 'endMatch'])->middleware('AuthUser');  
    
});

































//test
Route::prefix('user')->group(function () {
    Route::get('/one/{id}', [UsersController::class, 'index']);
    Route::get('/list/{id}', [UsersController::class, 'read']);
    Route::post('/', [UsersController::class, 'create']);
    
});


Route::get('/hello', function (Request $request, Response $response){
    $std = new \stdClass;
    $std->hello = 'ola mundo';
    return json_encode($std);
});
Route::get('/params/{id}/{name?}', function (Request $request, $id, $name = '') {
    return 'User '.$id.' Nome:'.$name;
})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

Route::get('/profile-fake', function(){//redireciona para outra rota aparti do alias
    return redirect()->route('profile');
});

Route::get('/profile', function () {
    return 'profile';
})->name('profile');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
