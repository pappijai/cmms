<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// to name the all the user route list
Route::apiResources(['user' => 'API\UserController']);
Route::apiResources(['building' => 'API\BuildingController']);
Route::apiResources(['floor' => 'API\FloorController']);
Route::apiResources(['classroom' => 'API\ClassroomController']);
Route::apiResources(['classroomType' => 'API\ClassroomTypeController']);
Route::apiResources(['subject' => 'API\SubjectController']);
Route::apiResources(['course' => 'API\CourseController']);


Route::get('buildingInfo', 'API\FloorController@buildinginfo');
Route::get('classroomTypeInfo', 'API\ClassroomController@classroomTypeInfo');
Route::get('floorsInfo/{id}', 'API\ClassroomController@floorsInfo');
Route::get('getsubjectmeetings/{id}', 'API\SubjectController@getsubjectmeetings');
Route::put('updatesubjectmeetings1/{id}', 'API\SubjectController@updatesubjectmeetings1');
Route::put('updatesubjectmeetings2/{id}', 'API\SubjectController@updatesubjectmeetings2');

