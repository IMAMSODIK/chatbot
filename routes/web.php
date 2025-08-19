<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ComplyIsoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::fallback(function () {
    return redirect('/');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users/delete', [UserController::class, 'deactivate']);

        Route::get('/dokumen', [DocumentsController::class, 'index'])->name('users');
        Route::post('/dokumen/store', [DocumentsController::class, 'store']);
        Route::post('/dokumen/delete', [DocumentsController::class, 'delete']);
    });

    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/send-message', [ChatController::class, 'send']);

    Route::get('/chat/get-group-chat', [ChatController::class, 'getGroupChat']);
    Route::get('/chat/get-chats', [ChatController::class, 'getChat']);
    Route::get('/chat/get-group', [ChatController::class, 'getGroup']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
