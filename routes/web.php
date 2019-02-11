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
Route::get('find/{token}', 'Auth\PasswordResetController@find');
Route::post('reset', 'Auth\PasswordResetController@resetWeb');
Route::get('/', 'AdminController@home')->name('home');
Route::get('/about', 'aboutController@about')->name('about');
Route::get('/download', 'AdminController@download');
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
	Route::post('/settings/apk', 'AdminController@apkUpload');
	Route::post('/settings' , 'AdminController@updateSettings');
	Route::get('/users', 'AdminController@allUsers');
	Route::get('/users/{user}', 'AdminController@showUser');
	Route::get('/users/{user}/block', 'AdminController@blockUser');
	Route::get('/designs/{design}/block', 'AdminController@blockDesign');
    Route::post('/roles/manager', 'AdminController@toggleManager');
    Route::get('/roles/manager/{user}', 'AdminController@removeManager');
    //== footer
    Route::patch('/footerlink' , 'AdminController@footerlinks');
    Route::post('/footerlink', 'AdminController@footerlinks');
    Route::get('/footerlink/{id}/delete', 'AdminController@deleteLink');
    Route::post('/footer/settings', 'AdminController@footerSettings');
    // == about page ==
    Route::get('/about', 'aboutController@aboutAdmin');
    Route::post('/about', 'aboutController@update');
});
