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


    Route::post('/parkings', [ParkingController::class, 'store']); 
    Route::put('/parkings/{id}', [ParkingController::class, 'update']); 
    Route::delete('/parkings/{id}', [ParkingController::class, 'destroy']);

Route::get('/parkings', [ParkingController::class, 'index']); 
Route::get('/parkings/{id}', [ParkingController::class, 'show']);

Route::get('/parkings/{id}/stats', [ParkingController::class, 'statistiquesParkingParId']);
Route::get('/rechercher/parking', [ParkingController::class, 'search'])->name('parking.search');



Route::get('/parkings/stats', [ParkingController::class, 'statistiquesParkings']);

Route::post('/reservations', [ReservationController::class, 'create']);
Route::put('/reservations/{id}', [ReservationController::class, 'update']);
Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);
Route::POST('/reservations/{id}', [ReservationController::class, 'index']);





