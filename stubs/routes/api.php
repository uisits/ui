<?php

use \Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function() {
    Route::resource('user', 'Api\UserApiController');
    Route::resource('feedback', 'Api\FeedbackApiController');
});
