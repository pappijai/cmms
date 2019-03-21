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
Route::get('/view_floorplan', 'FloorplanController@index')->name('view_floorplan');
Route::get('/view_schedule', 'FloorplanController@view_schedule')->name('view_schedule');
Route::get('/get_floors/{id}', 'FloorplanController@get_floors')->name('get_floors');
Route::get('/get_floor_classroom/{id}/{floor_name}/{floor_photo}/{bldgid}/{bldgname}', 'FloorplanController@get_floor_classroom')->name('get_floor_classroom');
Route::get('/get_floors_classrooms_schedule/{id}/{floor_id}/{floor_name}/{floor_photo}/{bldgid}/{bldgname}/{classroom_name}', 'FloorplanController@get_floors_classrooms_schedule')->name('get_floors_classrooms_schedule');
Route::get('/print_schedule/{id}', 'FloorplanController@print_schedule')->name('print_schedule');
Route::get('/get_schedule/{id}', 'FloorplanController@get_schedule')->name('get_schedule');

Route::get('download_backup/{file_name}', 'API\UserController@download_backup');



// this thing help us not to redirect to not found page if u 
//refresh the page with a vue components
Route::get('{path}',"HomeController@index")->where( 'path', '([A-z\d-\/_.]+)?');
