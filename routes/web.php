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
