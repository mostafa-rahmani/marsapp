<?php

use Illuminate\Support\Facades\Route;

Route::group([ 'prefix' => 'users'],function (){

    Route::get('/', 'UsersController@index');
    Route::get('{user} ', 'UsersController@show');
    Route::post('/update', 'UsersController@update');
    Route::delete('/{user}', 'UsersController@delete');
    Route::get('/follow/{user}', 'UsersController@follow');
    Route::get('/unfollow/{user}', 'UsersController@unfollow');
    Route::get('/followers/{user}', 'UsersController@followers');
    Route::get('/followings/{user}', 'UsersController@followings');

});

Route::group(['prefix' => 'auth'], function (){

    Route::group([
        'namespace' => 'Auth',
        'prefix' => 'password'
    ], function () {
        Route::post('create', 'PasswordResetController@create');
        Route::post('reset', 'PasswordResetController@resetApi');

});

    Route::post('/checkpass', 'AuthController@checkPassword');
    Route::patch('/changepass', 'AuthController@changePassword');
    //== authentication
    Route::post('/register', 'AuthController@register')->name('register');
    Route::post('/login', 'AuthController@login'); // email password password_confirmation
    Route::get('/logout', 'AuthController@logout');
});

Route::group(['prefix' => 'designs'], function(){

    Route::get('/', 'DesignsController@index');
    Route::get('/{design}', 'DesignsController@show');
    Route::get('/{design}/download', 'DesignsController@download');
    Route::delete('/{design}/delete', 'DesignsController@delete');
    Route::post('/list', 'DesignsController@list');
    Route::post('/create', 'DesignsController@store');
    Route::post('/{design}/update', 'DesignsController@update');
    Route::get('/following/get', 'DesignsController@followingDesigns');
    Route::get('/like/{design}', 'DesignsController@likeDesign');
    Route::get('/dislike/{design}', 'DesignsController@dislikeDesign');

});

Route::group(['prefix' => 'comments'], function (){

    Route::patch('/{comment}/update', 'CommentsController@update');
    Route::get('/{comment}', 'CommentsController@show');
    Route::post('/{design}/create', 'CommentsController@store');
    Route::delete('/{comment}/delete', 'CommentsController@delete');

});

Route::post('/search', 'SearchController@search');
