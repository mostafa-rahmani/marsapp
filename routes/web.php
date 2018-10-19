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
Route::get('/', function (){
    return view('welcome');
})->name('home');
Route::group(['prefix'=> 'admin'], function(){
	Route::get('/', function (){
		return view('admin.dashboard');
	});
	Route::get('/settings', function(){
		return view('admin.settings');
	});
	Route::get('/users', function(){
		return view('admin.users');
	});
	Route::get('/users/{user}', function(){
		return view('admin.user');
	});
	Route::get('/users/{user}/edit', function(){
		return view('admin.user_edit');
	});
});
