<?php

use App\Http\Controllers\ChatController;
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

Route::post('/sendmessage', [ChatController::class, 'sendMessage'])->name('chat');
Route::get('/list-user', [ChatController::class, 'listUser'])->name('list-user');
Route::post('/store-user', [ChatController::class, 'storeUser'])->name('store-user');
Route::post('/store-group', [ChatController::class, 'storeGroup'])->name('store-group');
Route::get('/list-user-chat', [ChatController::class, 'getUserChat'])->name('list-user-chat');
Route::get('/chat', [ChatController::class, 'getMessage'])->name('list-chat');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', [ChatController::class, 'index'])->name('dashboard');
});
