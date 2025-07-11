<?php
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
Route::get('/categories',[CategoryController::class,'getPagination']);
Route::post('/category/create',[CategoryController::class,'saveCategory']);
Route::post('/category/update',[CategoryController::class,'updateCategory']);
Route::post('/category/delete/soft',[CategoryController::class,'softDeleteCategory']);
Route::post('/category/delete/force',[CategoryController::class,'forceDeleteCategory']);
Route::post('/category/restore/{id}',[CategoryController::class,'restoreCategory']);
Route::get('/category/{id}',[CategoryController::class,'getById']);
?>