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

// first page that will load when you open the system
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// After login this page will show
Route::get('/dashboard', 'HomeController@index')->name('dashboard');


// this thing help us not to redirect to not found page if u 
//refresh the page with a vue components
Route::get('{path}',"HomeController@index")->where( 'path', '([A-z\d-\/_.]+)?');
