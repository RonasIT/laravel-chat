<?php

use RonasIT\Chat\Http\Controllers\ConversationController;
use RonasIT\Chat\Http\Controllers\MessageController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('conversations', [ConversationController::class, 'search']);
    Route::get('conversations/{id}', [ConversationController::class, 'get']);
    Route::delete('conversations/{id}', [ConversationController::class, 'delete']);
    Route::get('users/{userId}/conversation', [ConversationController::class, 'getByUserId']);

    Route::get('messages', [MessageController::class, 'search']);
    Route::post('messages', [MessageController::class, 'create']);
    Route::put('messages/{id}/read', [MessageController::class, 'read']);
});
