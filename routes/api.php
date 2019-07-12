<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Squadron\User\Http\Controllers\Api')
    ->group(function () {
        // auth
        Route::post('/oauth/token', [
            'uses' => 'AccessTokenController@issueToken',
            'middleware' => 'throttle',
        ]);

        Route::prefix('api')
            ->middleware('api')
            ->group(function () {
                // registration
                Route::post('/registration', 'UserController@register');
                Route::post('/registration/validate', 'UserController@doValidation');

                // social auth
                Route::get('/social/redirect/{provider}', 'SocialAuthController@redirect');
                Route::get('/social/redirectBack/{provider}', 'SocialAuthController@redirectBack');
                Route::post('/social/auth', 'SocialAuthController@auth');

                // reset password
                Route::post('/password/token', 'ForgotPasswordController@sendResetLinkEmail');
                Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.reset');

                Route::middleware('auth:api')->group(function () {
                    Route::get('/user/current', 'UserController@current');
                    Route::post('/user/current', 'UserController@updateCurrent');
                });
            });
    });
