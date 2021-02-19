<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', ['uses' => '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@login', 'as' => 'login']);

Route::middleware(['auth:web', 'impersonate:web'])->group(function() {
    Route::view('/','home')->name('home');

    Route::get('/user/impersonate-stop', [UserController::class, 'stopImpersonate'])
        ->name('user.impersonatestop');

    Route::get('/user/{user}/impersonate/', [UserController::class, 'impersonateUser'])
        ->name('user.impersonate');

    Route::resource('/user', UserController::class);

    Route::view('/feedback', 'feedback.index');
});
