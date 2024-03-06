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

Route::get('institutes', 'Api\InstituteController@index');
Route::get('courses', 'Api\CourseController@index');
Route::get('sessions', 'Api\SessionController@index');

Route::get('question-subjects', 'Api\QuestionSubjectController@index');
Route::get('question-chapters', 'Api\QuestionChapterController@index');
Route::get('question-topics', 'Api\QuestionTopicController@index');
