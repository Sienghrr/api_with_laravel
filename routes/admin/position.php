<?php
use App\Http\Controllers\PositionController;
use Illuminate\Support\Facades\Route;
Route::get('/positions',[PositionController::class,'getPagination']);
Route::post('/position/create',[PositionController::class,'savePosition']);
Route::post('/position/update',[PositionController::class,'updatePosition']);
Route::post('/position/delete/soft',[PositionController::class,'softDeletePosition']);
Route::post('/position/delete/force',[PositionController::class,'forceDeletePosition']);
Route::post('/position/restore/{id}',[PositionController::class,'restorePosition']);
Route::get('/position/{id}',[PositionController::class,'getById']);
?>