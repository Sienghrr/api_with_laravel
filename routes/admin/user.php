<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
Route::get('/users',[UserController::class,'getPagination']);
Route::post('/user/create',[UserController::class,'saveUser']);
Route::post('/user/update',[UserController::class,'updateUser']);
Route::post('/user/delete/soft',[UserController::class,'softDeleteUser']);
Route::post('/user/delete/force',[UserController::class,'forceDeleteUser']);
Route::post('/user/restore/{id}',[UserController::class,'restoreUser']);
Route::get('/user/{id}',[UserController::class,'getById']);
?>