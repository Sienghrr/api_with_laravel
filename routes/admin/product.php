<?php
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
Route::get('/products',[ProductController::class,'getPagination']);
Route::post('/product/create',[ProductController::class,'saveProduct']);
Route::post('/product/update',[ProductController::class,'updateProduct']);
Route::post('/product/delete/soft',[ProductController::class,'softDeleteProduct']);
Route::post('/product/delete/force',[ProductController::class,'forceDeleteProduct']);
Route::post('/product/restore/{id}',[ProductController::class,'restoreProduct']);
Route::get('/product/{id}',[ProductController::class,'getById']);
?>