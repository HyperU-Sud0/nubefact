<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('get_invoice',[\App\Http\Controllers\ApiInvoice::class, 'index'])->name('get_invoice');
Route::post('cancel_invoice',[\App\Http\Controllers\ApiInvoice::class, 'CancelInvoice'])->name('cancel_invoice');
Route::post('query_invoice',[\App\Http\Controllers\ApiInvoice::class, 'QueryInvoice'])->name('query_invoice');
<<<<<<< HEAD
Route::post('query_invoice_xml',[\App\Http\Controllers\ApiInvoice::class, 'QueryInvoiceXML'])->name('query_invoice_xml');
Route::post('query_invoice_qr',[\App\Http\Controllers\ApiInvoice::class, 'QueryInvoiceQR'])->name('query_invoice_qr');
=======
Route::post('query_invoice_canceled',[\App\Http\Controllers\ApiInvoice::class, 'QueryInvoiceCanceled'])->name('query_invoice_canceled');
>>>>>>> 731a64b5a8f3ced84c0b3d2751605c77bca948c7
