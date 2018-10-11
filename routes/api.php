<?php

Route::group([ 'prefix' => 'users' ],function (){

    Route::get('/', 'UsersController@index');
    Route::get('{user}', 'UsersController@show');
    Route::patch('/{user}/update', 'UsersController@update');
    Route::delete('/{user}', 'UsersController@delete');
    Route::post('/follows/{user}', 'UsersController@follows');
    Route::post('/likes/{user}', 'UsersControllers@likes');

});
Route::group([
    'namespace' => 'Auth',
//    'middleware' => 'auth:api',
    'prefix' => 'password'
], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});
Route::group(['prefix' => 'auth'], function (){
    //== authentication

    Route::post('/checkpass', function (){
        return 'checks users password ';
    });
    Route::post('/changepass', function (){
        return 'change user password ';
    });

    Route::post('/recover', function (){
        return 'send a password rest email';
    });
    Route::post('/register', 'AuthController@register')->name('register');
    Route::post('/login', 'AuthController@login')->name('login');
});

Route::group(['prefix' => 'designs'], function(){

    Route::get('/', 'DesignsController@index');
    Route::get('/{design}', 'DesignsController@show');
    Route::post('/list', 'DesignsController@list');
    Route::post('/create', 'DesignsController@store');
    Route::post('/{design}/delete', 'DesignsController@delete');
    Route::patch('/{design}/update', 'DesignsController@update');
    Route::get('/{design}/download', 'DesignsController@downlod');
    Route::get('/{user}/followingdesigns', 'DesignsController@followingDesigns');
});

Route::group(['prefix' => 'comments'], function (){

    Route::patch('/{comment}/update', 'CommentsController@update');
    Route::get('/{comment}', 'CommentsController@show');
    Route::post('/create', 'CommentsController@store');
    Route::delete('/{comment}/delete', 'CommentsController@delete');

});
Route::post('/search',function (){
    return 'result of the search';
});


