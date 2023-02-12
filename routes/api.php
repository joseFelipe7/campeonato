<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChampionshipsController;
use App\Http\Controllers\FriendController;

 


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
    Route::get('/', [PlayerController::class, 'index'])->middleware('AuthUser');
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
    Route::get('/', [ChampionshipsController::class, 'listChampionships'])->middleware('AuthUser');  
    Route::post('/{id}/match', [ChampionshipsController::class, 'createMatch'])->middleware('AuthUser');  
    Route::get('/{id}/match/current', [ChampionshipsController::class, 'listCurrentMatchChampionship'])->middleware('AuthUser');  
    Route::post('/{id}/match/{idMatch}/finished', [ChampionshipsController::class, 'endMatch'])->middleware('AuthUser');  
    
});


/** Router Friend */
Route::prefix('friend')->group(function () {
    
    Route::get('/', [FriendController::class, 'listFriends'])->middleware('AuthUser');
    Route::post('/', [FriendController::class, 'sendInviteFriend'])->middleware('AuthUser');
    Route::put('/{id}', [FriendController::class, 'responseInviteFriend'])->middleware('AuthUser');
    Route::get('/pending', [FriendController::class, 'listRecived'])->middleware('AuthUser');
    
    
});
