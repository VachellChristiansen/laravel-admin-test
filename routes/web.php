<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BotmanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Info
Route::get('/info', function () {
    return view('phpinfo');
});

Route::get('/test' , function () {
    $response = Http::acceptJson()->get('http://127.0.0.1:5000');
    return view('testing', [
        'res' => $response
    ]);
});

// Home
Route::get('/', [HomeController::class, 'show']);

// Admin
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('adminLogin');

Route::post('/adm/login', [AdminController::class, 'login']);

Route::middleware(['auth'])->group( function () {
    Route::get('/admin', [AdminController::class, 'show']);
    
    Route::get('/admin/{current}', [AdminController::class, 'show']);
});

// Form Inputs
Route::get('/forms', [FormController::class, 'show']);

Route::post('/forms/submit', [FormController::class, 'submit']);
