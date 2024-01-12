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
/**
 * SANDBOX
 */
Route::post('sandbox',[\App\Http\Controllers\ApiInvoice::class, 'QueryInvoiceSandbox'])->name('sandbox');
Route::post('cancel_sandbox',[\App\Http\Controllers\ApiInvoice::class, 'CancelInvoiceSandbox'])->name('cancel_sandbox');
Route::post('cancel_sandbox_query',[\App\Http\Controllers\ApiInvoice::class, 'CancelInvoiceQuerySandbox'])->name('cancel_sandbox_query');

/**
 * FIN SANDBOX
 */

Route::post('get_invoice',[\App\Http\Controllers\ApiInvoice::class, 'index'])->name('get_invoice');
Route::post('cancel_invoice',[\App\Http\Controllers\ApiInvoice::class, 'CancelInvoice'])->name('cancel_invoice');
Route::post('query_invoice',[\App\Http\Controllers\ApiInvoice::class, 'QueryInvoice'])->name('query_invoice');
Route::post('query_invoice_xml',[\App\Http\Controllers\ApiInvoice::class, 'QueryInvoiceXML'])->name('query_invoice_xml');
Route::post('query_invoice_qr',[\App\Http\Controllers\ApiInvoice::class, 'QueryInvoiceQR'])->name('query_invoice_qr');

Route::get('get_invoice',function(){
    return abort(404);
});
Route::get('cancel_invoice',function(){
    return abort(404);
});
Route::get('query_invoice',function(){
    return abort(404);
});
Route::get('query_invoice_xml',function(){
    return abort(404);
});
Route::get('query_invoice_qr',function(){
    return abort(404);
});
