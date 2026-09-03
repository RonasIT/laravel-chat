<?php

use Illuminate\Support\Facades\Route;
use RonasIT\Chat\Http\Controllers\ConversationController;
use RonasIT\Chat\Http\Controllers\MessageController;

Route::group(['middleware' => ['auth']], function () {
    Route::get('conversations', [ConversationController::class, 'search']);
    Route::get('conversations/{id}', [ConversationController::class, 'get'])->whereNumber('id');
    Route::delete('conversations/{id}', [ConversationController::class, 'delete'])->whereNumber('id');
    Route::get('users/{userId}/conversation', [ConversationController::class, 'getByUserId'])->whereNumber('userId');

    Route::get('messages', [MessageController::class, 'search']);
    Route::post('messages', [MessageController::class, 'create']);
    Route::post('messages/{id}/read-to', [MessageController::class, 'readUpTo'])->whereNumber('id');
    Route::post('messages/{id}/pin', [MessageController::class, 'pin'])->whereNumber('id');
    Route::post('messages/{id}/unpin', [MessageController::class, 'unpin'])->whereNumber('id');
});
