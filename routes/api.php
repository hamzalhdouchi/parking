<?php

use App\Http\Controllers\authController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ReservationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register' ,[authController::class ,'register']);
Route::post('/login' ,[authController::class ,'login']);
Route::post('/logout' ,[authController::class ,'logout']);

Route::middleware('auth:sanctum')->group(function(){

        Route::post('/parkings', [ParkingController::class, 'store']); 
        Route::put('/parkings/{id}', [ParkingController::class, 'update']); 
        Route::delete('/parkings/{id}', [ParkingController::class, 'destroy']);
    
    Route::get('/parkings', [ParkingController::class, 'index']); 
    Route::get('/parkings/{id}', [ParkingController::class, 'show']);
    
    Route::get('/rechercher', [ParkingController::class, 'search']);
    Route::get('/stats', [ParkingController::class, 'stats']);
    
    Route::post('/reservations', [ReservationController::class, 'create']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);
    Route::get('/reservations/{id}', [ReservationController::class, 'index']);
});





