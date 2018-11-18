<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('find/{token}', 'auth\PasswordResetController@find');
Route::post('reset', 'auth\PasswordResetController@resetWeb');
Route::get('/', 'AdminController@home')->name('home');
Route::get('/about', 'AdminController@about')->name('about');

Route::group(['prefix' => 'auth'], function (){
    Route::get('/login', 'Admin\AuthController@adminLogin')->name('admin_login');
    Route::post('/login', 'Admin\AuthController@adminLogin');
    Route::get('/logout', 'Admin\AuthController@adminLogout');
    Route::get('/password/change', 'Admin\AuthController@changePass');
    Route::post('/password/change', 'Admin\AuthController@changePass');
    Route::get('/register', 'Admin\AuthController@registerForm');
    Route::post('/register', 'Admin\AuthController@register');
});

Route::group(['prefix'=> 'admin'], function(){
    Route::get('/', 'AdminController@admin');
	Route::get('/settings', 'AdminController@adminSettings');
	Route::post('/settings' , 'AdminController@updateSettings');
	Route::get('/users', 'AdminController@allUsers');
	Route::get('/users/{user}', 'AdminController@showUser');
	Route::get('/users/{user}/block', 'AdminController@blockUser');
	Route::get('/designs/{design}/block', 'AdminController@blockDesign');
    Route::post('/roles/manager', 'AdminController@toggleManager');
    Route::get('/roles/manager/{user}', 'AdminController@removeManager');
});
