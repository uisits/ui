<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', ['uses' => '\StudentAffairsUwm\Shibboleth\Controllers\ShibbolethController@login', 'as' => 'login']);

Route::middleware(['auth:web', 'impersonate:web'])->group(function() {
    Route::view('/','home')->name('home');

    Route::get('/user/impersonate-stop', 'UserController@stopImpersonate')
        ->name('user.impersonatestop');

    Route::get('/user/{user}/impersonate/', 'UserController@impersonateUser')
        ->name('user.impersonate');

    Route::resource('/user', 'UserController');

    Route::view('/feedback', 'feedback.index');

    Route::view('/help', 'help.index');
});
