<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/admin', function () {
        return view('login');
    })->name('login');                           // only ONE route named 'login'

    Route::post('/admin', [AuthController::class, 'doLogin']);

    Route::get('/admin/register', function () {
        return view('register');
    })->name('register');
    Route::post('/admin/register', [AuthController::class, 'doRegister']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect('/admin/dashboard');
});

Route::get('/item', function () {
    return view('frontend.item');
});

Route::get('/items', function () {
    return view('frontend.items-app');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/invoices/{invoice}/print', function (\App\Models\Invoice $invoice) {
        $invoice->load(['project.customer', 'payments']);
        return view('billing.invoice-print', compact('invoice'));
    })->name('invoices.print');

    Route::get('/admin/{any}', function () {
        return view('index');
    })->where('any', '.*');
});