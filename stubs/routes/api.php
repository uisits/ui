<?php

use \Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\FeedbackApiController;

Route::middleware('auth:api')->group(function() {
    Route::resource('user', UserApiController::class);
    Route::resource('feedback', FeedbackApiController::class);
});
