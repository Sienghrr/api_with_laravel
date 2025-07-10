<?php
use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;
Route::get('/branches',[BranchController::class,'getPagination']);
Route::post('/branch/create',[BranchController::class,'saveBranch']);
Route::post('/branch/update',[BranchController::class,'updateBranch']);
Route::post('/branch/delete/soft',[BranchController::class,'softDeleteBranch']);
Route::post('/branch/delete/force',[BranchController::class,'forceDeleteBranch']);
Route::post('/branch/restore/{id}',[BranchController::class,'restoreBranch']);
Route::get('/branch/{id}',[BranchController::class,'getById']);
?>