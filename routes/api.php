<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('/conversations', ['uses' => ConversationController::class . '@search']);
    Route::get('/conversations/{id}', ['uses' => ConversationController::class . '@get']);
    Route::delete('/conversations/{id}', ['uses' => ConversationController::class . '@delete']);
    Route::get('/users/{userId}/conversation', ['uses' => ConversationController::class . '@getByUserId']);

    Route::get('/messages', ['uses' => MessageController::class . '@search']);
    Route::post('/messages', ['uses' => MessageController::class . '@create']);
    Route::put('/messages/{id}/read', ['uses' => MessageController::class . '@read']);
});
