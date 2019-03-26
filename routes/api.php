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
Route::apiResources(['section' => 'API\SectionController']);
Route::apiResources(['subjecttagging' => 'API\SubjectTaggingController']);
Route::apiResources(['floorplan' => 'API\FloorplanController']);
Route::apiResources(['report' => 'API\ReportController']);

Route::get('get_floorplan', 'API\FloorplanController@get_floorplan');
Route::get('buildingInfo', 'API\FloorController@buildinginfo');
Route::get('classroomTypeInfo', 'API\ClassroomController@classroomTypeInfo');
Route::get('floorsInfo/{id}', 'API\ClassroomController@floorsInfo');
Route::get('getsubjectmeetings/{id}', 'API\SubjectController@getsubjectmeetings');
Route::get('subjectsforcourse', 'API\SubjectController@subjectsforcourse');
Route::get('courses_subjects_per_year_sem/{course_id}/{year}/{sem}', 'API\CourseController@courses_subjects_per_year_sem');
Route::get('subjects_per_course_year_sem/{course_id}/{year}/{sem}', 'API\SubjectTaggingController@subjects_per_course_year_sem');
Route::get('tagged_subject_sections/{section_id}/{sem}/{year}/{year_from}/{year_to}', 'API\SubjectTaggingController@tagged_subject_sections');
Route::get('schedule_per_subject/{id}', 'API\SubjectTaggingController@schedule_per_subject');
Route::get('courses', 'API\SectionController@courses');
Route::get('get_professor', 'API\SubjectTaggingController@get_professor');
Route::get('update_status_subject_schedule/{sem}/{year_from}/{year_to}', 'API\SubjectTaggingController@update_status_subject_schedule');
Route::get('get_floors/{id}', 'API\FloorplanController@get_floors');
Route::get('get_floors_coordinates/{id}', 'API\FloorplanController@get_floors_coordinates');
Route::get('get_classroom_schedules/{id}', 'API\FloorplanController@get_classroom_schedules');
Route::get('print_schedule/{id}', 'API\FloorplanController@print_schedule');
Route::get('get_count_data/{tables}', 'API\UserController@get_count_data');
Route::get('get_count_section_for_tagging', 'API\UserController@get_count_section_for_tagging');
Route::get('print_section_schedule/{id}', 'API\SubjectTaggingController@print_section_schedule');
Route::get('get_profile', 'API\UserController@get_profile');
Route::get('get_backups', 'API\UserController@get_backups');
Route::get('create_backup', 'API\UserController@create_backup');
Route::get('subjecttaggingschedules/{id}', 'API\SubjectTaggingController@subjecttaggingschedules');
Route::get('get_days', 'API\SubjectTaggingController@get_days');
Route::get('get_schedules', 'API\SubjectTaggingController@get_schedules');
Route::get('get_professor_schedule/{id}', 'API\ProfessorController@get_professor_schedule');
Route::get('print_professor_schedule/{id}', 'API\ProfessorController@print_professor_schedule');
Route::get('get_report/{year_from}/{semester}/{section_id}', 'API\ReportController@get_report');
Route::get('print_report/{year_from}/{semester}/{section_id}', 'API\ReportController@print_report');
Route::get('get_classrooms/{id}', 'API\ReportController@get_classrooms');
Route::get('get_classroom_report/{bldg_id}/{bfid}/{day}', 'API\ReportController@get_classroom_report');
Route::get('print_classroom_report/{bldg_id}/{bfid}/{day}', 'API\ReportController@print_classroom_report');
Route::get('get_subjectmeeting_schedule/{id}', 'API\SubjectTaggingController@get_subjectmeeting_schedule');
Route::get('get_classroom_type/{id}', 'API\SubjectTaggingController@get_classroom_type');
// Route::get('download_backup/{file_name}', 'API\UserController@download_backup');

Route::put('download_backup', 'API\UserController@download_backup');
Route::put('delete_backup', 'API\UserController@delete_backup');
Route::put('updatesubjectmeetings1/{id}', 'API\SubjectController@updatesubjectmeetings1');
Route::put('updatesubjectmeetings2/{id}', 'API\SubjectController@updatesubjectmeetings2');
Route::put('update_profile/{id}', 'API\UserController@update_profile');
Route::put('update_subjecttagging/{id}', 'API\SubjectTaggingController@update_subjecttagging');
Route::put('update_tagged_meetings/{id}', 'API\SubjectTaggingController@update_tagged_meetings');

Route::post('create_course_subject_first', 'API\CourseController@create_course_subject_first');
Route::post('classroom_report', 'API\ReportController@classroom_report');

Route::delete('delete_course_subject/{id}', 'API\CourseController@delete_course_subject');
Route::delete('delete_subject_schedule/{id}', 'API\SubjectTaggingController@delete_subject_schedule');


