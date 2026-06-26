<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupportController;
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


Route::get('dashboard', [DashboardController::class, 'index']);

Route::apiResource('customers', CustomerController::class);

Route::apiResource('projects', ProjectController::class);

Route::apiResource('invoices', InvoiceController::class);
Route::patch('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel']);

Route::post('payments', [PaymentController::class, 'store']);
Route::delete('payments/{payment}', [PaymentController::class, 'destroy']);

Route::get('reports/due', [ReportController::class, 'dueReport']);


Route::post('required_data', [SupportController::class, 'requiredData']);
