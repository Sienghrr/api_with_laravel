<?php
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;
Route::get('/staffs',[StaffController::class,'getPagination']);
Route::post('/staff/create',[StaffController::class,'saveStaff']);
Route::post('/staff/update',[StaffController::class,'updateStaff']);
Route::post('/staff/delete/soft',[StaffController::class,'softDeleteStaff']);
Route::post('/staff/delete/force',[StaffController::class,'forceDeleteStaff']);
Route::post('/staff/restore/{id}',[StaffController::class,'restoreStaff']);
Route::get('/staff/{id}',[StaffController::class,'getById']);
?>