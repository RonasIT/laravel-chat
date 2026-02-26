<?php

use Illuminate\Support\Facades\Route;
use RonasIT\Chat\Http\Controllers\ConversationController;
use RonasIT\Chat\Http\Controllers\MessageController;
use RonasIT\Chat\Http\Middlewares\CheckManuallyRegisteredRoutesMiddleware;

Route::group(['middleware' => ['auth', CheckManuallyRegisteredRoutesMiddleware::class]], function () {
    Route::get('conversations', [ConversationController::class, 'search']);
    Route::post('conversations/private', [ConversationController::class, 'getOrCreatePrivate']);
    Route::get('conversations/{id}', [ConversationController::class, 'get']);
    Route::put('conversations/{id}', [ConversationController::class, 'update']);
    Route::delete('conversations/{id}', [ConversationController::class, 'delete']);

    Route::get('messages', [MessageController::class, 'search']);
    Route::post('messages/{conversation_id}', [MessageController::class, 'create']);
    Route::put('messages/{id}/read', [MessageController::class, 'read']);
});
