<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

include 'admin/branch.php';


