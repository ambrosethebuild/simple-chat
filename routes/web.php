<?php

use Illuminate\Support\Facades\Route;
use SimpleChat\Http\Controllers\SimpleChatController;
use SimpleChat\Http\Controllers\SimpleChatDashboardController;

Route::group([
    'prefix' => config('simple-chat.route_prefix', 'chat'),
    'middleware' => config('simple-chat.middleware', ['web', 'auth']),
    'as' => 'simple-chat.',
], function () {
    Route::get('/', [SimpleChatController::class, 'index'])->name('index');
    Route::get('/dashboard', [SimpleChatDashboardController::class, 'index'])->name('dashboard');
    Route::get('/trashed', [SimpleChatController::class, 'trashed'])->name('trashed');
    Route::get('/create', [SimpleChatController::class, 'create'])->name('create');
    Route::post('/start', [SimpleChatController::class, 'start'])->name('start');
    Route::get('/{conversation}', [SimpleChatController::class, 'show'])->name('show');
    Route::delete('/{conversation}', [SimpleChatController::class, 'destroy'])->name('destroy');
    Route::post('/{conversation}/restore', [SimpleChatController::class, 'restore'])->name('restore');
    Route::post('/{conversation}/close', [SimpleChatController::class, 'close'])->name('close');
    Route::post('/{conversation}/assign', [SimpleChatController::class, 'assign'])->name('assign');
    Route::delete('/{conversation}/unassign/{agent}', [SimpleChatController::class, 'unassign'])->name('unassign');
    Route::get('/{conversation}/messages', [SimpleChatController::class, 'fetchMessages'])->name('messages.fetch');
    Route::post('/{conversation}/messages', [SimpleChatController::class, 'store'])->name('messages.store');
});
