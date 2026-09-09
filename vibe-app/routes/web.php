<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sales', [HomeController::class, 'sales'])->name('sales');
Route::post('/sales', [HomeController::class, 'storeSale'])->name('sales.store');
Route::get('/products', [HomeController::class, 'products'])->name('products');
Route::get('/reimbursements', [HomeController::class, 'reimbursements'])->name('reimbursements');
Route::get('/purchases', [HomeController::class, 'purchases'])->name('purchases');
Route::get('/customers', [HomeController::class, 'customers'])->name('customers');
Route::post('/customers', [HomeController::class, 'storeCustomer'])->name('customers.store');
Route::get('/suppliers', [HomeController::class, 'suppliers'])->name('suppliers');
Route::get('/reports', [HomeController::class, 'reports'])->name('reports');
Route::get('/reports/print', [HomeController::class, 'printReport'])->name('reports.print');
Route::post('/generate', [HomeController::class, 'generate'])->name('generate');
