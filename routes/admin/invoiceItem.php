<?php
use App\Http\Controllers\InvoiceItemController;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Route;
Route::get('/invoiceItems',[InvoiceItemController::class,'getPagination']);
Route::post('/invoiceItem/create',[InvoiceItemController::class,'saveInvoiceItem']);
Route::post('/invoiceItem/update',[InvoiceItemController::class,'updateInvoiceItem']);
Route::post('/invoiceItem/delete/soft',[InvoiceItemController::class,'softDeleteInvoiceItem']);
Route::post('/invoiceItem/delete/force',[InvoiceItemController::class,'forceDeleteInvoiceItem']);
Route::post('/invoiceItem/restore/{id}',[InvoiceItemController::class,'restoreInvoiceItem']);
Route::get('/invoiceItem/{id}',[InvoiceItemController::class,'getById']);
?>