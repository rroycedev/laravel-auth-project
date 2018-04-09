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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('/user/create', 'UsersController@create')->name('user.create');
Route::post('/user/store', 'UsersController@store')->name('user.store');
Route::post('/user/docreate', 'UsersController@doCreate')->name('user.docreate');

Route::get('/user/edit', 'UsersController@editIndex')->name('user.edit');
Route::get('/user/delete', 'UsersController@deleteIndex')->name('user.delete');

Route::get('/user/{uid}/edit', 'UsersController@edit')->name('user.edit');

Route::post('/user/update', 'UsersController@update')->name('user.update');
Route::get('/user/{uid}/destroy', 'UsersController@destroy');

// Password Reset Routes...

Route::post(
    'password/email',
    '\App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail'
)
    ->name('password.email');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.passwords.reset');
Route::get(
    'password/reset/{token}',
    '\App\Http\Controllers\Auth\ResetPasswordController@showResetForm'
)
    ->name('password.reset');

Route::get(
    '/password/change',
    '\App\Http\Controllers\UsersController@showChangePasswordForm'
)
    ->name('password.change');

    Route::post('password/change', '\App\Http\Controllers\UsersController@changePswd')->name('password.change');
