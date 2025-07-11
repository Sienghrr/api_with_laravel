<?php
use App\Http\Controllers\InvoiceController;
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
Route::get('/invoices',[InvoiceController::class,'getPagination']);
Route::post('/invoice/create',[InvoiceController::class,'saveInvoice']);
Route::post('/invoice/update',[InvoiceController::class,'updateInvoice']);
Route::post('/invoice/delete/soft',[InvoiceController::class,'softDeleteInvoice']);
Route::post('/invoice/delete/force',[InvoiceController::class,'forceDeleteInvoice']);
Route::post('/invoice/restore/{id}',[InvoiceController::class,'restoreInvoice']);
Route::get('/invoice/{id}',[InvoiceController::class,'getById']);
?>