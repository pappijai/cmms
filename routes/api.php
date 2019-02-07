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
Route::apiResources(['professor' => 'API\ProfessorController']);


Route::get('buildingInfo', 'API\FloorController@buildinginfo');
Route::get('classroomTypeInfo', 'API\ClassroomController@classroomTypeInfo');
Route::get('floorsInfo/{id}', 'API\ClassroomController@floorsInfo');
Route::get('getsubjectmeetings/{id}', 'API\SubjectController@getsubjectmeetings');
Route::put('updatesubjectmeetings1/{id}', 'API\SubjectController@updatesubjectmeetings1');
Route::put('updatesubjectmeetings2/{id}', 'API\SubjectController@updatesubjectmeetings2');
Route::get('subjectsforcourse', 'API\SubjectController@subjectsforcourse');
Route::get('courses_subjects_per_year_sem/{course_id}/{year}/{sem}', 'API\CourseController@courses_subjects_per_year_sem');
Route::post('create_course_subject_first', 'API\CourseController@create_course_subject_first');
Route::delete('delete_course_subject/{id}', 'API\CourseController@delete_course_subject');

