<?php

use App\Http\Controllers\Controller;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('nothing-to-nothinge', function () {
    //
});

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

Route::get('admin/login', 'Admin\Auth\LoginController@showLoginForm');
Route::post('admin/getlogincode', 'Admin\Auth\LoginController@logincode');
Route::get('admin/login/getlogincode', 'Admin\Auth\LoginController@getLoginCode')->name('getLoginCode')->middleware("guest");


Route::post('admin/login', 'Admin\Auth\LoginController@login');
Route::post('admin/logout', 'Admin\Auth\LoginController@logout');

// Password Reset Routes...
Route::get('admin/password/reset', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
Route::post('admin/password/email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
Route::get('admin/password/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm')->name('admin.password.reset');
Route::post('admin/password/reset', 'Admin\Auth\ResetPasswordController@reset')->name('admin.auth.password.reset');
Route::get('doctor-of-the-day', 'HomeController@doctor_of_the_day');

Route::get('password-send', 'Auth\PasswordSendController@password_send');
Route::post('password-submit', 'Auth\PasswordSendController@password_submit');

Route::post('apply-discount-code', 'AjaxController@apply_discount_code');


//Route::resource('code-set', 'Admin\DiscountCodeController');
//Route::post('submit-discount-code', 'Admin\CodeController@submit_discount_code');


Route::get('registration-status', 'Auth\RegistrationSatusController@registration_status');
Route::post('registration-status-submit', 'Auth\RegistrationSatusController@registration_status_submit');

//Route::get('get-verification-code', 'Auth\VerificationCodeController@get_verification_code');
//Route::post('get-verification-code-submit', 'Auth\VerificationCodeController@get_verification_code_submit');

Route::get('search-college', 'AjaxController@search_college');


Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
    //Route::group([ 'prefix' => 'admin'], function () {

    Route::get('/', 'Admin\HomeController@dashboard');

    \App\Http\Helpers\ContentRoute::set('batch-schedules', 'Admin\BatchSchedulesControllerV2');

    Route::group(['prefix' => 'batch-schedules'], function () {
        Route::get('/', 'Admin\BatchSchedulesController@index')->name('batch_schedules.list');
        Route::get('/doctors-ratings', 'Admin\BatchSchedulesController@doctors_ratings')->name('batch_schedules.ratings');
        Route::get('/datatable', 'Admin\BatchSchedulesController@batch_schedule_list')->name('batch_schedules.datatable');

        Route::get('trash', 'Admin\BatchSchedulesController@trash_bin');
        Route::get('batch_schedule_trash_list', 'Admin\BatchSchedulesController@batch_schedule_trash_list');
        Route::get('batch_schedule_restore/{id}', 'Admin\BatchSchedulesController@batch_schedule_restore');


        //        Route::get('create', 'Admin\BatchSchedulesController@create')->name('batch_schedules.create');
        //        Route::get('{id}/edit', 'Admin\BatchSchedulesController@edit');
        //        Route::get('{id}/duplicate', 'Admin\BatchSchedulesController@duplicate');
        //        Route::post('store', 'Admin\BatchSchedulesController@save')->name('batch-schedule-store');
        //        Route::put('update/{id}', 'Admin\BatchSchedulesController@save')->name('batch-schedule-edit');
        //        Route::get('lecture-exams', 'Admin\BatchSchedulesController@lectures_exams');

        Route::get('{id}/show', 'Admin\BatchSchedulesController@show');
        Route::get('get-data', 'Admin\BatchSchedulesController@data');
        Route::get('lecture-exams-mentors', 'Admin\BatchSchedulesController@lectures_exams_mentors');
        Route::get('course-data', 'Admin\BatchSchedulesController@course_data');
        Route::get('faculties-disciplines', 'Admin\BatchSchedulesController@faculties_disciplines');


        Route::get('lecture-exams', 'Admin\BatchSchedulesControllerV2@lectures_exams');
        Route::get('create', 'Admin\BatchSchedulesControllerV2@create');
        Route::get('{id}/edit', 'Admin\BatchSchedulesControllerV2@edit');
        Route::get('{id}/duplicate', 'Admin\BatchSchedulesControllerV2@duplicate');
        Route::delete('slot/{slot_id}/delete', 'Admin\BatchSchedulesControllerV2@remove_slot');
        Route::delete('class-or-exam/{content_id}/delete', 'Admin\BatchSchedulesControllerV2@remove_class_or_exam');
        Route::post('store', 'Admin\BatchSchedulesControllerV2@save')->name('batch-schedule-store-v2');
        Route::put('update/{id}', 'Admin\BatchSchedulesControllerV2@save')->name('batch-schedule-edit-v2');
    });

    Route::get('doctor-course-active-list/{doctor_course}/quick-edit', 'Admin\DoctorsCoursesController@quickEdit');
    Route::get('doctor-course-active-list', 'Admin\DoctorsCoursesController@doctor_course_active_list');
    Route::get('doctor-course-active-list-ajax', 'Admin\DoctorsCoursesController@doctor_course_active_list_ajax');

    Route::get('master-schedule', 'Admin\BatchSchedulesController@master_schedule');

    Route::post('master-schedule-list', 'Admin\BatchSchedulesController@master_schedule_list');

    Route::post('institute-change-in-topic', 'Admin\TopicsController@institute_change_in_topic');
    Route::post('course-change-in-topic', 'Admin\TopicsController@course_change_in_topic');
    Route::post('year-change-in-topic', 'Admin\TopicsController@year_change_in_topic');
    Route::get('topic-list', 'Admin\TopicsController@topic_list');
    Route::resource('topics', 'Admin\TopicsController');
    Route::get('topics-contents/{id}', 'Admin\TopicsController@topics_contents');
    Route::get('topic-content-add/{topic_id}/{content_id}', 'Admin\TopicsController@topic_content_add');

    Route::get('topic-mentor-list/{topic_id}', 'Admin\TopicsController@topic_mentor_list');
    Route::get('topic-mentor-ajax-list', 'Admin\TopicsController@topic_mentor_ajax_list');
    Route::get('topic-mentor-add/{topic_id}', 'Admin\TopicsController@topic_mentor_add');
    Route::get('topic-mentor-add-list', 'Admin\TopicsController@topic_mentor_add_list');
    Route::post('topic-mentor-save', 'Admin\TopicsController@topic_mentor_save');
    Route::get('topic-mentor-edit/{topic_content_id}', 'Admin\TopicsController@topic_mentor_edit');
    Route::get('topic-mentor-edit-list', 'Admin\TopicsController@topic_mentor_edit_list');
    Route::post('topic-mentor-update', 'Admin\TopicsController@topic_mentor_update');
    Route::get('topic-mentor-delete/{id}', 'Admin\TopicsController@topic_mentor_delete');

    Route::get('topic-lecture-video-list/{topic_id}', 'Admin\TopicsController@topic_lecture_video_list');
    Route::get('topic-lecture-video-ajax-list', 'Admin\TopicsController@topic_lecture_video_ajax_list');
    Route::get('topic-lecture-video-add/{topic_id}', 'Admin\TopicsController@topic_lecture_video_add');
    Route::get('topic-lecture-video-add-list', 'Admin\TopicsController@topic_lecture_video_add_list');
    Route::post('topic-lecture-video-save', 'Admin\TopicsController@topic_lecture_video_save');
    Route::get('topic-lecture-video-edit/{topic_content_id}', 'Admin\TopicsController@topic_lecture_video_edit');
    Route::get('topic-lecture-video-edit-list', 'Admin\TopicsController@topic_lecture_video_edit_list');
    Route::post('topic-lecture-video-update', 'Admin\TopicsController@topic_lecture_video_update');
    Route::get('topic-lecture-video-delete/{id}', 'Admin\TopicsController@topic_lecture_video_delete');

    Route::get('topic-exam-list/{topic_id}', 'Admin\TopicsController@topic_exam_list');
    Route::get('topic-exam-ajax-list', 'Admin\TopicsController@topic_exam_ajax_list');
    Route::get('topic-exam-add/{topic_id}', 'Admin\TopicsController@topic_exam_add');
    Route::get('topic-exam-add-list', 'Admin\TopicsController@topic_exam_add_list');
    Route::post('topic-exam-save', 'Admin\TopicsController@topic_exam_save');
    Route::get('topic-exam-edit/{topic_content_id}', 'Admin\TopicsController@topic_exam_edit');
    Route::get('topic-exam-edit-list', 'Admin\TopicsController@topic_exam_edit_list');
    Route::post('topic-exam-update', 'Admin\TopicsController@topic_exam_update');
    Route::get('topic-exam-delete/{id}', 'Admin\TopicsController@topic_exam_delete');

    Route::get('topic-lecture-sheet-list/{topic_id}', 'Admin\TopicsController@topic_lecture_sheet_list');
    Route::get('topic-lecture-sheet-ajax-list', 'Admin\TopicsController@topic_lecture_sheet_ajax_list');
    Route::get('topic-lecture-sheet-add/{topic_id}', 'Admin\TopicsController@topic_lecture_sheet_add');
    Route::get('topic-lecture-sheet-add-list', 'Admin\TopicsController@topic_lecture_sheet_add_list');
    Route::post('topic-lecture-sheet-save', 'Admin\TopicsController@topic_lecture_sheet_save');
    Route::get('topic-lecture-sheet-edit/{topic_content_id}', 'Admin\TopicsController@topic_lecture_sheet_edit');
    Route::get('topic-lecture-sheet-edit-list', 'Admin\TopicsController@topic_lecture_sheet_edit_list');
    Route::post('topic-lecture-sheet-update', 'Admin\TopicsController@topic_lecture_sheet_update');
    Route::get('topic-lecture-sheet-delete/{id}', 'Admin\TopicsController@topic_lecture_sheet_delete');


    Route::post('institute-change-in-program', 'Admin\ProgramController@institute_change_in_program');
    Route::post('course-change-in-program', 'Admin\ProgramController@course_change_in_program');
    Route::post('year-change-in-program', 'Admin\ProgramController@year_change_in_program');
    Route::get('program-list', 'Admin\ProgramController@program_list');
    Route::resource('program', 'Admin\ProgramController');
    Route::get('program-content/{id}', 'Admin\ProgramController@program_content');
    Route::get('program-content-add/{program_id}/{content_id}', 'Admin\ProgramController@program_content_add');

    Route::get('program-media-type-list/{program_id}', 'Admin\ProgramController@program_media_type_list');
    Route::get('program-media-type-ajax-list', 'Admin\ProgramController@program_media_type_ajax_list');
    Route::get('program-media-type-add/{program_id}', 'Admin\ProgramController@program_media_type_add');
    Route::get('program-media-type-add-list', 'Admin\ProgramController@program_media_type_add_list');
    Route::post('program-media-type-save', 'Admin\ProgramController@program_media_type_save');
    Route::get('program-media-type-edit/{program_content_id}', 'Admin\ProgramController@program_media_type_edit');
    Route::get('program-media-type-edit-list', 'Admin\ProgramController@program_media_type_edit_list');
    Route::post('program-media-type-update', 'Admin\ProgramController@program_media_type_update');
    Route::get('program-media-type-delete/{id}', 'Admin\ProgramController@program_media_type_delete');

    Route::get('program-topic-list/{program_id}', 'Admin\ProgramController@program_topic_list');
    Route::get('program-topic-ajax-list', 'Admin\ProgramController@program_topic_ajax_list');
    Route::get('program-topic-add/{program_id}', 'Admin\ProgramController@program_topic_add');
    Route::get('program-topic-add-list', 'Admin\ProgramController@program_topic_add_list');
    Route::post('program-topic-save', 'Admin\ProgramController@program_topic_save');
    Route::get('program-topic-edit/{program_content_id}', 'Admin\ProgramController@program_topic_edit');
    Route::get('program-topic-edit-list', 'Admin\ProgramController@program_topic_edit_list');
    Route::post('program-topic-update', 'Admin\ProgramController@program_topic_update');
    Route::get('program-topic-delete/{id}', 'Admin\ProgramController@program_topic_delete');

    Route::get('program-mentor-list/{program_id}', 'Admin\ProgramController@program_mentor_list');
    Route::get('program-mentor-ajax-list', 'Admin\ProgramController@program_mentor_ajax_list');
    Route::get('program-mentor-add/{program_id}', 'Admin\ProgramController@program_mentor_add');
    Route::get('program-mentor-add-list', 'Admin\ProgramController@program_mentor_add_list');
    Route::post('program-mentor-save', 'Admin\ProgramController@program_mentor_save');
    Route::get('program-mentor-edit/{program_content_id}', 'Admin\ProgramController@program_mentor_edit');
    Route::get('program-mentor-edit-list', 'Admin\ProgramController@program_mentor_edit_list');
    Route::post('program-mentor-update', 'Admin\ProgramController@program_mentor_update');
    Route::get('program-mentor-delete/{id}', 'Admin\ProgramController@program_mentor_delete');

    Route::get('program-lecture-video-list/{program_id}', 'Admin\ProgramController@program_lecture_video_list');
    Route::get('program-lecture-video-ajax-list', 'Admin\ProgramController@program_lecture_video_ajax_list');
    Route::get('program-lecture-video-add/{program_id}', 'Admin\ProgramController@program_lecture_video_add');
    Route::get('program-lecture-video-add-list', 'Admin\ProgramController@program_lecture_video_add_list');
    Route::post('program-lecture-video-save', 'Admin\ProgramController@program_lecture_video_save');
    Route::get('program-lecture-video-edit/{program_content_id}', 'Admin\ProgramController@program_lecture_video_edit');
    Route::get('program-lecture-video-edit-list', 'Admin\ProgramController@program_lecture_video_edit_list');
    Route::post('program-lecture-video-update', 'Admin\ProgramController@program_lecture_video_update');
    Route::get('program-lecture-video-delete/{id}', 'Admin\ProgramController@program_lecture_video_delete');

    Route::get('program-exam-list/{program_id}', 'Admin\ProgramController@program_exam_list');
    Route::get('program-exam-ajax-list', 'Admin\ProgramController@program_exam_ajax_list');
    Route::get('program-exam-add/{program_id}', 'Admin\ProgramController@program_exam_add');
    Route::get('program-exam-add-list', 'Admin\ProgramController@program_exam_add_list');
    Route::post('program-exam-save', 'Admin\ProgramController@program_exam_save');
    Route::get('program-exam-edit/{program_content_id}', 'Admin\ProgramController@program_exam_edit');
    Route::get('program-exam-edit-list', 'Admin\ProgramController@program_exam_edit_list');
    Route::post('program-exam-update', 'Admin\ProgramController@program_exam_update');
    Route::get('program-exam-delete/{id}', 'Admin\ProgramController@program_exam_delete');

    Route::get('program-lecture-sheet-list/{program_id}', 'Admin\ProgramController@program_lecture_sheet_list');
    Route::get('program-lecture-sheet-ajax-list', 'Admin\ProgramController@program_lecture_sheet_ajax_list');
    Route::get('program-lecture-sheet-add/{program_id}', 'Admin\ProgramController@program_lecture_sheet_add');
    Route::get('program-lecture-sheet-add-list', 'Admin\ProgramController@program_lecture_sheet_add_list');
    Route::post('program-lecture-sheet-save', 'Admin\ProgramController@program_lecture_sheet_save');
    Route::get('program-lecture-sheet-edit/{program_content_id}', 'Admin\ProgramController@program_lecture_sheet_edit');
    Route::get('program-lecture-sheet-edit-list', 'Admin\ProgramController@program_lecture_sheet_edit_list');
    Route::post('program-lecture-sheet-update', 'Admin\ProgramController@program_lecture_sheet_update');
    Route::get('program-lecture-sheet-delete/{id}', 'Admin\ProgramController@program_lecture_sheet_delete');

    Route::post('institute-change-in-program-batch', 'Admin\ProgramController@institute_change_in_program_batch');
    Route::post('course-change-in-program-batch', 'Admin\ProgramController@course_change_in_program_batch');
    Route::post('year-change-in-program-batch', 'Admin\ProgramController@year_change_in_program_batch');

    Route::get('program-batch-list/{program_id}', 'Admin\ProgramController@program_batch_list');
    Route::get('program-batch-ajax-list', 'Admin\ProgramController@program_batch_ajax_list');
    Route::get('program-batch-add/{program_id}', 'Admin\ProgramController@program_batch_add');
    Route::get('program-batch-add-list', 'Admin\ProgramController@program_batch_add_list');
    Route::post('program-batch-save', 'Admin\ProgramController@program_batch_save');
    Route::get('program-batch-edit/{program_content_id}', 'Admin\ProgramController@program_batch_edit');
    Route::get('program-batch-edit-list', 'Admin\ProgramController@program_batch_edit_list');
    Route::post('program-batch-update', 'Admin\ProgramController@program_batch_update');
    Route::get('program-batch-delete/{id}', 'Admin\ProgramController@program_batch_delete');

    Route::post('institute-change-in-module', 'Admin\ModuleController@institute_change_in_module');
    Route::post('course-change-in-module', 'Admin\ModuleController@course_change_in_module');
    Route::post('year-change-in-module', 'Admin\ModuleController@year_change_in_module');
    Route::get('module-list', 'Admin\ModuleController@module_list');
    Route::resource('module', 'Admin\ModuleController');
    Route::get('module-content/{id}', 'Admin\ModuleController@module_content');
    Route::get('module-content-add/{module_id}/{content_id}', 'Admin\ModuleController@module_content_add');

    Route::post('institute-change-in-module-batch', 'Admin\ModuleController@institute_change_in_module_batch');
    Route::post('course-change-in-module-batch', 'Admin\ModuleController@course_change_in_module_batch');
    Route::post('year-change-in-module-batch', 'Admin\ModuleController@year_change_in_module_batch');

    Route::get('module-batch-list/{module_id}', 'Admin\ModuleController@module_batch_list');
    Route::get('module-batch-ajax-list', 'Admin\ModuleController@module_batch_ajax_list');
    Route::get('module-batch-add/{module_id}', 'Admin\ModuleController@module_batch_add');
    Route::get('module-batch-add-list', 'Admin\ModuleController@module_batch_add_list');
    Route::post('module-batch-save', 'Admin\ModuleController@module_batch_save');
    Route::get('module-batch-edit/{module_content_id}', 'Admin\ModuleController@module_batch_edit');
    Route::get('module-batch-edit-list', 'Admin\ModuleController@module_batch_edit_list');
    Route::post('module-batch-update', 'Admin\ModuleController@module_batch_update');
    Route::get('module-batch-delete/{id}', 'Admin\ModuleController@module_batch_delete');

    Route::post('institute-change-in-module-faculty', 'Admin\ModuleController@institute_change_in_module_faculty');

    Route::get('module-faculty-list/{module_id}', 'Admin\ModuleController@module_faculty_list');
    Route::get('module-faculty-ajax-list', 'Admin\ModuleController@module_faculty_ajax_list');
    Route::get('module-faculty-add/{module_id}', 'Admin\ModuleController@module_faculty_add');
    Route::get('module-faculty-add-list', 'Admin\ModuleController@module_faculty_add_list');
    Route::post('module-faculty-save', 'Admin\ModuleController@module_faculty_save');
    Route::get('module-faculty-edit/{module_content_id}', 'Admin\ModuleController@module_faculty_edit');
    Route::get('module-faculty-edit-list', 'Admin\ModuleController@module_faculty_edit_list');
    Route::post('module-faculty-update', 'Admin\ModuleController@module_faculty_update');
    Route::get('module-faculty-delete/{id}', 'Admin\ModuleController@module_faculty_delete');

    Route::post('institute-change-in-module-discipline', 'Admin\ModuleController@institute_change_in_module_discipline');

    Route::get('module-discipline-list/{module_id}', 'Admin\ModuleController@module_discipline_list');
    Route::get('module-discipline-ajax-list', 'Admin\ModuleController@module_discipline_ajax_list');
    Route::get('module-discipline-add/{module_id}', 'Admin\ModuleController@module_discipline_add');
    Route::get('module-discipline-add-list', 'Admin\ModuleController@module_discipline_add_list');
    Route::post('module-discipline-save', 'Admin\ModuleController@module_discipline_save');
    Route::get('module-discipline-edit/{module_content_id}', 'Admin\ModuleController@module_discipline_edit');
    Route::get('module-discipline-edit-list', 'Admin\ModuleController@module_discipline_edit_list');
    Route::post('module-discipline-update', 'Admin\ModuleController@module_discipline_update');
    Route::get('module-discipline-delete/{id}', 'Admin\ModuleController@module_discipline_delete');

    Route::get('module-topic-list/{module_id}', 'Admin\ModuleController@module_topic_list');
    Route::get('module-topic-ajax-list', 'Admin\ModuleController@module_topic_ajax_list');
    Route::get('module-topic-add/{module_id}', 'Admin\ModuleController@module_topic_add');
    Route::get('module-topic-add-list', 'Admin\ModuleController@module_topic_add_list');
    Route::post('module-topic-save', 'Admin\ModuleController@module_topic_save');
    Route::get('module-topic-edit/{module_content_id}', 'Admin\ModuleController@module_topic_edit');
    Route::get('module-topic-edit-list', 'Admin\ModuleController@module_topic_edit_list');
    Route::post('module-topic-update', 'Admin\ModuleController@module_topic_update');
    Route::get('module-topic-delete/{id}', 'Admin\ModuleController@module_topic_delete');

    Route::get('module-program-type-list/{module_id}', 'Admin\ModuleController@module_program_type_list');
    Route::get('module-program-type-ajax-list', 'Admin\ModuleController@module_program_type_ajax_list');
    Route::get('module-program-type-add/{module_id}', 'Admin\ModuleController@module_program_type_add');
    Route::get('module-program-type-add-list', 'Admin\ModuleController@module_program_type_add_list');
    Route::post('module-program-type-save', 'Admin\ModuleController@module_program_type_save');
    Route::get('module-program-type-edit/{module_content_id}', 'Admin\ModuleController@module_program_type_edit');
    Route::get('module-program-type-edit-list', 'Admin\ModuleController@module_program_type_edit_list');
    Route::post('module-program-type-update', 'Admin\ModuleController@module_program_type_update');
    Route::get('module-program-type-delete/{id}', 'Admin\ModuleController@module_program_type_delete');

    Route::get('module-media-type-list/{module_id}', 'Admin\ModuleController@module_media_type_list');
    Route::get('module-media-type-ajax-list', 'Admin\ModuleController@module_media_type_ajax_list');
    Route::get('module-media-type-add/{module_id}', 'Admin\ModuleController@module_media_type_add');
    Route::get('module-media-type-add-list', 'Admin\ModuleController@module_media_type_add_list');
    Route::post('module-media-type-save', 'Admin\ModuleController@module_media_type_save');
    Route::get('module-media-type-edit/{module_content_id}', 'Admin\ModuleController@module_media_type_edit');
    Route::get('module-media-type-edit-list', 'Admin\ModuleController@module_media_type_edit_list');
    Route::post('module-media-type-update', 'Admin\ModuleController@module_media_type_update');
    Route::get('module-media-type-delete/{id}', 'Admin\ModuleController@module_media_type_delete');

    Route::get('module-program-list/{module_id}', 'Admin\ModuleController@module_program_list');
    Route::get('module-program-ajax-list', 'Admin\ModuleController@module_program_ajax_list');
    Route::get('module-program-add/{module_id}', 'Admin\ModuleController@module_program_add');
    Route::get('module-program-add-list', 'Admin\ModuleController@module_program_add_list');
    Route::post('module-program-save', 'Admin\ModuleController@module_program_save');
    Route::get('module-program-edit/{module_content_id}', 'Admin\ModuleController@module_program_edit');
    Route::get('module-program-edit-list', 'Admin\ModuleController@module_program_edit_list');
    Route::post('module-program-update', 'Admin\ModuleController@module_program_update');
    Route::get('module-program-delete/{id}', 'Admin\ModuleController@module_program_delete');

    Route::resource('module-schedule', 'Admin\ModuleScheduleController');
    Route::get('module-schedule-list/{module_id}', 'Admin\ModuleScheduleController@module_schedule_list');
    Route::get('module-schedule-add/{module_id}', 'Admin\ModuleScheduleController@module_schedule_add');
    Route::post('module-schedule-save', 'Admin\ModuleScheduleController@module_schedule_save');
    Route::get('module-schedule-edit/{module_schedule_id}', 'Admin\ModuleScheduleController@module_schedule_edit');
    Route::post('module-schedule-update', 'Admin\ModuleScheduleController@module_schedule_update');
    Route::get('module-schedule-delete/{module_schedule_id}', 'Admin\ModuleScheduleController@module_schedule_delete');
    Route::get('module-schedule-print/{module_schedule_id}', 'Admin\ModuleScheduleController@module_schedule_print');

    Route::resource('module-schedule-slot', 'Admin\ModuleScheduleSlotController');
    Route::get('module-schedule-slot-list/{module_schedule_id}', 'Admin\ModuleScheduleSlotController@module_schedule_slot_list');
    Route::get('module-schedule-slot-add-list', 'Admin\ModuleScheduleSlotController@module_schedule_slot_add_list');
    Route::get('module-schedule-slot-add/{module_schedule_id}', 'Admin\ModuleScheduleSlotController@module_schedule_slot_add');
    Route::post('module-schedule-slot-save', 'Admin\ModuleScheduleSlotController@module_schedule_slot_save');
    Route::get('module-schedule-slot-edit/{module_schedule_slot_id}', 'Admin\ModuleScheduleSlotController@module_schedule_slot_edit');
    Route::get('module-schedule-slot-edit-list', 'Admin\ModuleScheduleSlotController@module_schedule_slot_edit_list');
    Route::post('module-schedule-slot-update', 'Admin\ModuleScheduleSlotController@module_schedule_slot_update');
    Route::get('module-schedule-slot-delete/{module_schedule_slot_id}', 'Admin\ModuleScheduleSlotController@module_schedule_slot_delete');

    Route::get('module-schedule-program-list/{module_schedule_id}', 'Admin\ModuleScheduleSlotController@module_schedule_program_list');
    Route::get('module-schedule-program-add-list', 'Admin\ModuleScheduleSlotController@module_schedule_program_add_list');
    Route::get('module-schedule-program-add/{module_schedule_id}', 'Admin\ModuleScheduleSlotController@module_schedule_program_add');
    Route::post('module-schedule-program-save', 'Admin\ModuleScheduleSlotController@module_schedule_program_save');
    Route::get('module-schedule-program-edit/{module_schedule_slot_id}', 'Admin\ModuleScheduleSlotController@module_schedule_program_edit');
    Route::get('module-schedule-program-edit-list', 'Admin\ModuleScheduleSlotController@module_schedule_program_edit_list');
    Route::post('module-schedule-program-update', 'Admin\ModuleScheduleSlotController@module_schedule_program_update');
    Route::get('module-schedule-program-delete/{module_schedule_slot_id}', 'Admin\ModuleScheduleSlotController@module_schedule_program_delete');
    Route::get('check-mentor-availability/{module_schedule_program_id}/{program_id}', 'Admin\ModuleScheduleSlotController@check_mentor_availability');

    Route::resource('room-slot', 'Admin\RoomSlotController');
    Route::get('room-slot-list/{room_id}', 'Admin\RoomSlotController@room_slot_list');
    Route::get('room-slot-add/{room_id}', 'Admin\RoomSlotController@room_slot_add');
    Route::post('room-slot-save', 'Admin\RoomSlotController@room_slot_save');
    Route::get('room-slot-edit/{room_slot_id}', 'Admin\RoomSlotController@room_slot_edit');
    Route::post('room-slot-update', 'Admin\RoomSlotController@room_slot_update');
    Route::get('room-slot-delete/{room_slot_id}', 'Admin\RoomSlotController@room_slot_delete');
    Route::post('room-slot-save-multiple', 'Admin\RoomSlotController@room_slot_save_multiple');
    
    Route::get('location-list', 'Admin\LocationController@location_list');
    Route::resource('location', 'Admin\LocationController');

    Route::post('branch-change-in-room', 'Admin\RoomController@branch_change_in_room');
    Route::post('location-change-in-room', 'Admin\RoomController@location_change_in_room');
    Route::post('floor-change-in-room', 'Admin\RoomController@floor_change_in_room');
    Route::get('room-list', 'Admin\RoomController@room_list');
    Route::resource('room', 'Admin\RoomController');



    Route::get('doctors-feedback', 'Admin\BatchSchedulesController@doctors_feedback');

    Route::get('mentor-evaluation-print/{id}', 'Admin\BatchSchedulesController@mentor_evaluation')->name('mentor-evaluation-print');

    Route::get('doctors-feedback-list', 'Admin\BatchSchedulesController@doctors_feedback_list');



    Route::post('apply-discount-code', 'Admin\AjaxController@apply_discount_code');

    Route::resource('administrator', 'Admin\AdministratorController');

    Route::get('doctors-list', 'Admin\DoctorsController@doctors_list');
    Route::resource('doctors', 'Admin\DoctorsController');
    //Route::get( 'doctors-excel/{year}', 'Admin\DoctorsController@doctorsExcelExport' );

    Route::get('doctors-excel/{paras}', 'Admin\DoctorsController@doctorsExcel');
    Route::post('update-by-call-center', 'Admin\DoctorsController@update_by_call_center');

    Route::resource('questionlink', 'Admin\QuestionlinkController');
    Route::resource('auto-reply-link', 'Admin\AutoReplyController');

    Route::post('institute-change-in-manual-payment', 'Admin\DoctorCourseManualPaymentController@institute_change_in_manual_payment');
    Route::post('course-change-in-manual-payment', 'Admin\DoctorCourseManualPaymentController@course_change_in_manual_payment');
    Route::post('year-change-in-manual-payment', 'Admin\DoctorCourseManualPaymentController@year_change_in_manual_payment');
    Route::get('doctor-course-manual-payment-list', 'Admin\DoctorCourseManualPaymentController@doctor_course_manual_payment_list');
    Route::resource('doctor-course-manual-payment', 'Admin\DoctorCourseManualPaymentController');
    Route::get('doctor-course-manual-payment-validate/{payment_id}', 'Admin\DoctorCourseManualPaymentController@doctor_course_manual_payment_validate');

    Route::get('payment-list', 'Admin\PaymentController@payment_list');
    Route::get('payment-total', 'Admin\PaymentController@paymentTotal');
    Route::resource('payment', 'Admin\PaymentController');
    Route::post('payment-varification', 'Admin\PaymentController@payment_varification');
    Route::resource('payment-status', 'Admin\SiteSetupController');
    Route::get('payment-excel/{paras}', 'Admin\PaymentController@payment_excel');

    Route::get('doctor-course-payment-form/{doctor_course_id}', 'Admin\DoctorsCoursesController@doctor_course_payment_form');
    Route::post('doctor-course-payment', 'Admin\DoctorsCoursesController@doctor_course_payment');
    Route::get('doctor-lecture-sheet-delivery-print/{doctor_course_id}', 'Admin\DoctorsCoursesController@doctor_course_lecture_sheet_delivery_print');
    Route::get('doctor-course-lecture-sheet-list/{doctor_course_id}', 'Admin\DoctorsCoursesController@doctor_course_lecture_sheet_list');
    Route::post('doctor-course-lecture-sheet', 'Admin\DoctorsCoursesController@doctor_course_lecture_sheet');
    Route::get('doctors-courses-list', 'Admin\DoctorsCoursesController@doctors_courses_list');
    Route::get('batch-excel-download/{params}', 'Admin\DoctorsCoursesController@batch_excel_download');

    Route::get('exam-batch-list', 'Admin\ExamController@exam_batch_list');
    //Route::get('result-excel/{year}/{session_id}/{batch_id}/{exam_id}', 'Admin\ExamController@resultExcelExport');
    Route::get('result-excel/{params}', 'Admin\ExamController@resultExcelExport');

    Route::get('doctors-courses-trash', 'Admin\DoctorsCoursesController@doctors_courses_trash');
    Route::get('doctors-courses-trash-list', 'Admin\DoctorsCoursesController@doctors_courses_trash_list');
    Route::get('doctors-courses-untrash/{id}', 'Admin\DoctorsCoursesController@doctors_courses_untrash');
    Route::get('batch-excel/{paras}', 'Admin\DoctorsCoursesController@batch_excel');
    Route::resource('doctors-courses', 'Admin\DoctorsCoursesController');

    
    // Batch Shift Log
    Route::get('batch-shifts', 'Admin\BatchShiftController@index')->name('batch-shifts.index');
    Route::put('batch-shifts/{batch_shift}', 'Admin\BatchShiftController@update')->name('batch-shifts.update');
    Route::get('doctor-courses/{doctorCourse}/batch-shifts', 'Admin\BatchShiftController@index');




    Route::post('doctors-courses-batch-shifted-details', 'Admin\AjaxController@doctors_courses_batch_shifted_details');
    Route::post('doctors-courses-payemnt-details', 'Admin\AjaxController@doctors_courses_payemnt_details');
    Route::post('doctors-courses-details', 'Admin\AjaxController@doctors_courses_details');
    Route::post('discount-edit-history', 'Admin\AjaxController@discount_edit_history');
    Route::post('profile-edit-history', 'Admin\AjaxController@profile_edit_history');
    Route::post('payment-note', 'Admin\AjaxController@payment_note');
    Route::get('print-courier-address/{doctor_course_id}', 'Admin\DoctorsCoursesController@print_courier_address');
    Route::get('print-course-details/{doctor_course_id}', 'Admin\DoctorsCoursesController@print_course_details');
    Route::get('print-payment-list/{doctor_course_id}', 'Admin\PaymentController@print_payment_list');

    Route::resource('batches-schedules', 'Admin\BatchesSchedulesController');
    Route::get('batch-schedule-list', 'Admin\BatchesSchedulesController@batch_schedule_list');
    Route::get('batches-schedules/lectures-exams-save/{id}', 'Admin\BatchesSchedulesController@lectures_exams_save');
    Route::post('batches-schedules/save-batch-schedule-lectures-exams/{id}', 'Admin\BatchesSchedulesController@save_batch_schedule_lectures_exams');
    Route::get('batches-schedules/print-batch-schedule/{id}', 'Admin\BatchesSchedulesController@print_batch_schedule');


    Route::get('doctors-complain-list', 'Admin\DoctorAskReplyController@doctors_complain_list');

    Route::resource('institutes', 'Admin\InstitutesController');

    Route::resource('courses', 'Admin\CoursesController');
    Route::resource('course-year', 'Admin\CourseYearController');

    Route::resource('coupon', 'Admin\CouponcodeController');
    Route::get('coupon-create', 'Admin\CouponcodeController@coupon_create');
    Route::post('coupon-code-generate', 'Admin\CouponcodeController@coupon_code_generate');

    Route::resource('faculty', 'Admin\FacultyController');
    Route::resource('subjects', 'Admin\SubjectsController');
    Route::resource('service-packages', 'Admin\ServicePackagesController');
    Route::resource('coming-by', 'Admin\ComingByController');

    Route::get('available-batches-trash', 'Admin\AvailableBatchesController@available_batches_trash');
    Route::get('available-batches-restore/{id}', 'Admin\AvailableBatchesController@available_batches_restore');


    \App\Http\Helpers\ContentRoute::set('available-batches', 'Admin\AvailableBatchesController');
    Route::resource('available-batches', 'Admin\AvailableBatchesController');


    Route::resource('doctors-reviews', 'Admin\DoctorsReviewsController');
    Route::get('available-batches/{id}/duplicate', 'Admin\AvailableBatchesController@duplicate');


    Route::resource('advertisements', 'Admin\AdvertisementsController');
    Route::resource('faq', 'Admin\FaqController');
    Route::resource('menus', 'Admin\MenuController');

    //gallery page
    Route::resource('photos', 'Admin\PhotosController');


    //Route::resource('chapter', 'Admin\ChaptersController');

    Route::get('labels', 'Admin\labelController@index')->name('labels.index');
    Route::post('labels/save/{label?}', 'Admin\labelController@save')->name('labels.save');

    Route::resource('question', 'Admin\QuestionsController');

    Route::get('question-mcq-list', 'Admin\QuestionsController@mcq_list');

    Route::get('quetion-print', 'Admin\QuestionPrintController@question_print');

    Route::post('question-view', 'Admin\QuestionsController@question_view');
    Route::post('question-edit-log', 'Admin\QuestionsController@question_edit_log');

    Route::get('question-list', 'Admin\ExamController@question_list');
    Route::post('add-exam-question', 'Admin\ExamController@add_exam_question');
    Route::get('add-questions/{params}', 'Admin\ExamController@add_questions');
    Route::get('add-exam-questions/{exam_id}/{question_type}', 'Admin\ExamController@add_exam_questionss');
    Route::get('exam-questions/{exam_id}', 'Admin\ExamController@exam_questions');
    Route::post('duplicate-exam', 'Admin\ExamController@duplicate_store');
    Route::get('edit-exam-question/{question_id}', 'Admin\ExamController@edit_exam_question');
    Route::get('delete-exam-question/{question_id}', 'Admin\ExamController@delete_exam_question');
    Route::post('update-exam-question', 'Admin\ExamController@update_exam_question');
    Route::post('get-question-details', 'Admin\ExamController@get_question_details');
    Route::get('print-exam/{exam_id}', 'Admin\ExamController@print_exam');


    //Route::resource('question-institute', 'Admin\QuestionInstitutionController');
    Route::resource('question-subject', 'Admin\QuestionSubjectController');
    Route::resource('question-chapter', 'Admin\QuestionChapterController');
    Route::resource('question-topic', 'Admin\QuestionTopicController');

    Route::resource('reference-institute', 'Admin\ReferenceInstitutionController');
    Route::resource('reference-course', 'Admin\ReferenceCourseController');
    Route::resource('reference-faculty', 'Admin\ReferenceFacultyController');
    Route::resource('reference-session', 'Admin\ReferenceSessionController');
    Route::resource('reference-subject', 'Admin\ReferenceSubjectController');

    Route::post('ajax-question-chapter', 'Admin\AjaxQuestionController@ajax_question_chapter');
    Route::post('ajax-question-topic', 'Admin\AjaxQuestionController@ajax_question_topic');

    Route::resource('question-reference-exam', 'Admin\QuestionReferenceExamController');

    Route::get('mcq-list', 'Admin\McqController@mcq_list');
    Route::resource('mcq', 'Admin\McqController');


    Route::get('sba-list', 'Admin\SbaController@sba_list');
    Route::resource('sba', 'Admin\SbaController');
    Route::post('sba-question-edit-log', 'Admin\SbaController@question_edit_log');
    Route::post('sba-question-view', 'Admin\SbaController@question_view');


    Route::resource('batch', 'Admin\BatchController');
    Route::get('batch-list',  'Admin\BatchController@batch_list');
    Route::get('print-batch-doctor-address', 'Admin\BatchController@print_batch_doctor_address');
    Route::post('print-batch-doctors-addresses', 'Admin\BatchController@print_batch_doctors_addresses');
    Route::resource('sessions', 'Admin\SessionsController');
    Route::resource('batch-discipline-fee', 'Admin\BatchDisciplineFeeController');
    Route::resource('batch-faculty-fee', 'Admin\BatchFacultyFeeController');
    Route::get('batch-faculty-fee/{id}/duplicate', 'Admin\BatchFacultyFeeController@duplicate');
    Route::get('batch-discipline-fee/{id}/duplicate', 'Admin\BatchDisciplineFeeController@duplicate');












    Route::get('doctor-course-system-driven/{id}',  'Admin\DoctorsCoursesController@system_driven');
    Route::post('doctor-course-system-driven-save',  'Admin\DoctorsCoursesController@system_driven_save');
    Route::get('batch-system-driven/{id}',  'Admin\BatchController@system_driven');
    Route::post('batch-system-driven-save',  'Admin\BatchController@system_driven_save');

    Route::get('doctor-course/payment-history/{doctor_coruse_id}', 'Admin\DoctorsCoursesController@payment_history');
    Route::get('doctor-course/payments/{id}',  'Admin\DoctorsCoursesController@doctor_course_payments');
    Route::post('doctor-course/payments-save',  'Admin\DoctorsCoursesController@doctor_course_payments_save');
    Route::post('doctor-course/installment-option-save',  'Admin\DoctorsCoursesController@installment_option_save');

    Route::get('batch/payment-option/{id}',  'Admin\BatchController@payment_option');
    Route::post('batch/payment-option-save',  'Admin\BatchController@payment_option_save');
    
    Route::get('doctor-course/payment-option/{doctor_course}',  'Admin\DoctorCoursePaymentController@option')->name('doctor_courses.payment.option');
    Route::post('doctor-course/payment-option/{doctor_course}',  'Admin\DoctorCoursePaymentController@optionStore');

    Route::post('institute-change-in-installemnt-due-list',  'Admin\DoctorsCoursesController@institute_change_in_installemnt_due_list');
    Route::post('course-change-in-installemnt-due-list',  'Admin\DoctorsCoursesController@course_change_in_installemnt_due_list');
    Route::post('year-change-in-installemnt-due-list',  'Admin\DoctorsCoursesController@year_change_in_installemnt_due_list');
    Route::post('session-change-in-installemnt-due-list',  'Admin\DoctorsCoursesController@session_change_in_installemnt_due_list');
    Route::get('batch-change-in-installemnt-due-list',  'Admin\DoctorsCoursesController@batch_change_in_installemnt_due_list');
    Route::get('installment-due-list',  'Admin\DoctorsCoursesController@installment_due_list');
    Route::get('installment-due-ajax-list',  'Admin\DoctorsCoursesController@installment_due_ajax_list');

    Route::post('sms-to-installment-due-list-from-admin',  'Admin\DoctorsCoursesController@sms_to_installment_due_list_from_admin');
    Route::get('sms-to-installment-due-list',  'Admin\DoctorsCoursesController@sms_to_installment_due_list');


    Route::get('lecture-video-price/{id}',  'Admin\LectureVideoController@lecture_video_price');
    Route::post('lecture-video-price-save',  'Admin\LectureVideoController@lecture_video_price_save');

    Route::get('lecture-video-price-edit/{id}',  'Admin\LectureVideoController@lecture_video_price_edit');
    Route::post('lecture-video-price-update',  'Admin\LectureVideoController@lecture_video_price_update');



    Route::get('exam-print/{id}', 'Admin\ExamController@print');
    Route::get('exam-print-ans/{id}', 'Admin\ExamController@print_ans');
    Route::get('exam-print-onlyans/{id}', 'Admin\ExamController@print_onlyans');
    Route::get('exam-print-onlyans/{id}/answer-file', 'Admin\ExamController@print_onlyans_for_file');

    Route::resource('omr-script', 'Admin\OmrScriptController');
    Route::resource('omr-script-property', 'Admin\OmrScriptPropertyController');
    Route::resource('omr-script-omr-script-property', 'Admin\OmrScriptOmrScriptPropertyController');

    // Route::get('upload-result/{id}', 'Admin\ExamController@upload_result');
    Route::get('view-result/{id}', 'Admin\ExamController@view_result');

    Route::get('batch-wise-result-print', 'Admin\ExamController@batch_wise_result_print')->name('batch-wise-result-print');

    // Result Upload
    Route::get('upload-result/{exam}', 'Admin\ResultUploadController@form');
    Route::post('result-submit', 'Admin\ResultUploadController@store');

    // Route::post('result-submit', 'Admin\ExamController@result_submit');
    Route::post('result-submit-faculty', 'Admin\ExamController@result_submit_faculty');
    Route::get('upload-result-combined/{id}', 'Admin\ExamController@upload_result_combined');
    Route::post('result-submit-combined', 'Admin\ExamController@result_submit_combined');

    \App\Http\Helpers\ContentRoute::set('exam', 'Admin\ExamController');
    Route::resource('exam', 'Admin\ExamController');
    Route::get('exam/{id}/duplicate', 'Admin\ExamController@duplicate');
    Route::resource('exam-assign', 'Admin\ExamAssignController');
    Route::post('course-branch-changed-in-exam-batch', 'Admin\AjaxController@course_branch_changed_in_exam_batch');

    Route::resource('teacher', 'Admin\TeacherController');

    Route::get('view-course-result/{id}', 'Admin\DoctorsController@view_course_result');
    Route::get('view-course-result/{id}/print', 'Admin\DoctorsController@view_course_result_print');

    Route::get('profile', 'Admin\ProfileController@show');
    Route::get('profile-edit', 'Admin\ProfileController@profile_edit');
    Route::post('profile-update', 'Admin\ProfileController@profile_update');


    Route::resource('topic', 'Admin\TopicController');
    Route::get('topic/{id}/duplicate', 'Admin\TopicController@duplicate');


    Route::get('answer/create/{id}', 'Admin\AnswersController@create');
    Route::resource('answer', 'Admin\AnswersController');


    Route::get('users-list', 'Admin\UsersController@users_list');

    Route::get('organization-users/{id}', 'Admin\UsersController@organization_users');
    Route::resource('users', 'Admin\UsersController');


    Route::resource('roles', 'Admin\RolesController');
    Route::resource('permissions', 'Admin\PermissionsController');

    Route::resource('sitesetup', 'Admin\SettingController');

    //Route::get('system-settings', 'Admin\SettingsController@system_settings');

    //Route::post('system-settings', 'Admin\SettingsController@system_settings_update');

    Route::resource('question-types', 'Admin\QuestionTypesController');

    Route::resource('package', 'Admin\PackageController');

    Route::resource('page', 'Admin\PageController');
    Route::resource('online-exam-common-code', 'Admin\OnlineExamCommonCodeController');
    Route::resource('online-exam-link', 'Admin\OnlineExamLinkController');

    Route::resource('online-lecture-address', 'Admin\OnlineLectureAddressController');
    Route::resource('online-lecture-link', 'Admin\OnlineLectureLinkController');

    Route::get('lecture-video-trash', 'Admin\LectureVideoController@lecture_video_trash');

    Route::get('lecture-video-restore/{id}', 'Admin\LectureVideoController@lecture_video_restore');

    \App\Http\Helpers\ContentRoute::set('lecture-video', 'Admin\LectureVideoController');

    Route::get('subscription-video', 'Admin\LectureVideoController@subscription_video');

    Route::post('subscription-status/{lecture_video}', 'Admin\LectureVideoController@subscriptionStatus');
    

    Route::post('change-mentor', 'Admin\LectureVideoController@change_mentor');
    Route::resource('lecture-video', 'Admin\LectureVideoController');

    Route::get('lecture-video/{id}/duplicate', 'Admin\LectureVideoController@duplicate');


    Route::resource('lecture-video-assign', 'Admin\LectureVideoAssignController');


    Route::resource('lecture-video-link', 'Admin\LectureVideoLinkController');

    Route::resource('lecture-video-batch', 'Admin\LectureVideoBatchController');

    Route::get('lecture-video-batch-list', 'Admin\LectureVideoBatchController@lecture_video_batch_list');

    Route::resource('lecture-sheet', 'Admin\LectureSheetController');


    Route::resource('lecture-sheet-topic', 'Admin\LectureSheetTopicController');


    Route::get('view-lecture-sheet/{id}/view', 'Admin\LectureSheetTopicController@view_lecture_sheet');


    Route::resource('lecture-sheet-topic-assign', 'Admin\LectureSheetTopicAssignController');
    Route::resource('lecture-sheet-topic-batch', 'Admin\LectureSheetTopicBatchController');
    Route::post('ajax-lecture-sheet-topics', 'Admin\AjaxController@ajax_lecture_sheet_topics');

    Route::resource('lecture-video-link', 'Admin\LectureVideoLinkController');
    Route::resource('exam-group', 'Admin\ExamGroupController');

    Route::resource('exam-batch', 'Admin\ExamBatchController');

    Route::get('exam-batch-trash', 'Admin\ExamBatchController@exam_batch_trash');
    Route::get('exam-batch-restore/{id}', 'Admin\ExamBatchController@exam_batch_restore');

    Route::get('/doctor-exams/{doctor_course_id}', 'Admin\DoctorsCoursesController@doctor_exams');
    Route::get('/doctor-exam-reopen/{doctor_course_id}/{exam_id}', 'Admin\DoctorsCoursesController@doctor_exam_reopen');

    Route::get('download-lecture-related-emails/{id}', 'Admin\LectureVideoController@download_emails');
    Route::resource('lecture-sheet-article', 'Admin\LectureSheetArticleController');
    Route::resource('lecture-sheet-article-batch', 'Admin\LectureSheetArticleBatchController');
    Route::resource('upazila', 'Admin\UpazilaController');

    Route::get('/discount-excel-download', 'Admin\DiscountController@excelDownload');

    Route::resource('discount', 'Admin\DiscountController');
    Route::get('discount-list', 'Admin\DiscountController@discount_list');
    //Route::resource('discount-assign', 'Admin\DiscountAssignController');
    //Route::get('discount-code-create', 'Admin\DiscountAssignController@discount_code_create');

    // discount request admin
    Route::resource('discount-request', 'Admin\DiscountRequestController');
    Route::post('discount-request-feedback', 'Admin\DiscountRequestController@discount_request_feedback');
    Route::get('get-discount/{id}', 'Admin\DiscountRequestController@get_discount');

    // discount request number admin
    Route::resource('discount-request-number', 'Admin\DiscountRequestNumberController');


    // add executive
    Route::resource('executive', 'Admin\executiveController');

    Route::resource('executive-course', 'Admin\ExecutiveCourseController');




    Route::resource('lecture', 'Admin\LectureController');

    //Route::resource('room', 'Admin\RoomController');


    Route::resource('service-point', 'Admin\ServicePointController');
    Route::resource('branches', 'Admin\BranchesController');


    Route::resource('/videos', 'Admin\VideoController');



    Route::resource('upazila', 'Admin\UpazilaController');
    Route::get('complain', 'Admin\ComplainController@index');
    Route::get('complain-reply/{id}', 'Admin\ComplainController@edit');
    Route::post('complain-replied', 'Admin\ComplainController@store');

    Route::get('doctor-question', 'Admin\DoctorQuestionController@index');
    Route::get('question-reply/{id}', 'Admin\DoctorQuestionController@edit');
    Route::post('question-replied', 'Admin\DoctorQuestionController@store');

    Route::get('doctor-ask-reply', 'Admin\DoctorAskReplyController@index');
    Route::get('doctor-ask-reply/{id}', 'Admin\DoctorAskReplyController@reply');
    Route::post('doctor-ask-replied', 'Admin\DoctorAskReplyController@reply_store');

    Route::get('doctors-questions', 'Admin\DoctorAskReplyController@doctors_questions');
    Route::get('view-conversation/{id}', 'Admin\DoctorAskReplyController@view_conversation');
    Route::post('reply_conversation', 'Admin\DoctorAskReplyController@reply_conversation');
    Route::post('doctor-question-feedback', 'Admin\DoctorAskReplyController@doctor_question_feedback');
    Route::get('doctor-questions-print', 'Admin\DoctorAskReplyController@doctor_questions_print');

    Route::get('doctor-complain-list', 'Admin\DoctorComplainController@doctor_complain_list');
    Route::get('doctor-complain-ajax-list', 'Admin\DoctorComplainController@doctor_complain_ajax_list');
    Route::get('doctor-complain-message/{mobile_number}/{user_id}', 'Admin\DoctorComplainController@doctor_complain_message');

    // Route::get('doctor-complain-list', 'Admin\DoctorComplainController@doctor_complain_repley');
    //complain create
    Route::get('complain/create', 'Admin\DoctorComplainController@complain_create');
    Route::post('complain/store', 'Admin\DoctorComplainController@complain_store');
    Route::get('complain/quick-register', 'Admin\DoctorComplainController@complain_quick_register');
    Route::post('complain/quick_register_submit', 'Admin\DoctorComplainController@complain_quick_register_submit');
    Route::get('search-doctors-complain', 'Admin\DoctorComplainController@search_doctors_complain');
    Route::post('complain-related-topics', 'Admin\DoctorComplainController@complain_related_topics');

    Route::get('query', 'Admin\DoctorComplainController@query_optimize');

    //end complain create


    Route::get('view-complain/{id}', 'Admin\DoctorComplainController@view_complain');
    Route::post('reply_complain', 'Admin\DoctorComplainController@reply_complain');
    Route::resource('banner-sliders', 'Admin\BannerSliderController');
    Route::get('reports-payment', 'Admin\ReportsController@payment_list');

    //Route::resource('doctors-say', 'Admin\DoctorsSayController');

    Route::resource('notice', 'Admin\NoticeController');
    Route::get('notice_show/{id}', 'Admin\NoticeController@show');
    Route::resource('notice-assign', 'Admin\NoticeAssignController');
    Route::resource('notice-batch', 'Admin\NoticeBatchController');

    Route::resource('notice-course', 'Admin\NoticeCourseController');
    Route::resource('notice-year', 'Admin\NoticeYearController');
    Route::resource('courier', 'Admin\CourierController');

    Route::resource('notice-board', 'Admin\NoticeBoardController');
    Route::resource('medical-colleges', 'Admin\MedicalCollegeController');
    Route::resource('group', 'Admin\GroupController');
    Route::resource('doctor-group', 'Admin\DoctorGroupController');
    Route::get('doctor-group-list', 'Admin\DoctorGroupController@doctor_group_list');
    Route::resource('information-alumni', 'Admin\InformationalumniController');

    Route::get('sms-list', 'Admin\SmsController@sms_list');
    Route::resource('sms', 'Admin\SmsController');
    Route::get('sms-show/{id}', 'Admin\SmsController@show');
    Route::resource('sms-assign', 'Admin\SmsAssignController');
    Route::resource('sms-batch', 'Admin\SmsBatchController');
    Route::resource('sms-course', 'Admin\SmsCourseController');
    Route::resource('sms-year', 'Admin\SmsYearController');

    Route::post('sms-type', 'Admin\SmsController@sms_type');
    Route::get('sms-search-doctors', 'Admin\SmsController@sms_search_doctors');
    Route::post('sms-institute-course', 'Admin\SmsController@sms_institute_course');
    Route::post('sms-course-batch', 'Admin\SmsController@sms_course_batch');
    Route::post('course-branch-changed-in-sms-batch', 'Admin\SmsController@course_branch_changed_in_sms_batch');
    Route::get('sms-send-list/{id}', 'Admin\SmsController@sms_send_list');
    Route::resource('sms-event', 'Admin\SmsEventController');


    Route::get('batch-change-test', function() {
        $doctor_course = \App\DoctorsCourses::query()
            ->with([
                'batch:id,name',
                'batch.faculties',
                'batch.subjects',
            ])
            ->find(73848, [
                'id',
                'batch_id',
                'faculty_id',
                'subject_id',
            ]);

        return $doctor_course;
    });

    //Merit List
    Route::resource('institute-allocations', 'Admin\InstituteAllocationController');
    Route::resource('institute-disciplines', 'Admin\InstituteDisciplineController');
    Route::resource('institute-allocation-seats', 'Admin\InstituteAllocationSeatController');
    Route::get('institute-allocation-seats-duplicate/{id}', 'Admin\InstituteAllocationSeatController@duplicate')->name('institute-allocation-seats.duplicate');
    Route::post('institute-allocation-seats-duplicate-save/{id}', 'Admin\InstituteAllocationSeatController@duplicate_save')->name('institute-allocation-seats.duplicate-save');
    Route::get('allocation-results/{course_id}/{exam}/{discipline}', 'Admin\AllocationResultController@list')->name('allocation-results.results');
    Route::resource('allocation-results', 'Admin\AllocationResultController');
    Route::get('allocation-results/print/{course_id}/{exam}/{discipline}', 'Admin\AllocationResultController@print');

    /*ajax*/
    Route::post('course-branch-changed-in-notice-batch', 'Admin\AjaxController@course_branch_changed_in_notice_batch');
    Route::post('get-transaction', 'Admin\AjaxController@get_transaction');
    Route::post('change-lecture-sheet-collection', 'Admin\AjaxController@change_lecture_sheet_collection');
    Route::post('change-include-lecture-sheet', 'Admin\AjaxController@change_include_lecture_sheet');
    Route::post('courier-division-district', 'Admin\AjaxController@courier_division_district');
    Route::post('courier-district-upazila', 'Admin\AjaxController@courier_district_upazila');
    Route::post('permanent-division-district', 'Admin\AjaxController@permanent_division_district');
    Route::post('permanent-district-upazila', 'Admin\AjaxController@permanent_district_upazila');
    Route::post('present-division-district', 'Admin\AjaxController@present_division_district');
    Route::post('present-district-upazila', 'Admin\AjaxController@present_district_upazila');
    Route::post('institutes-courses', 'Admin\AjaxController@institutes_courses');
    Route::post('courses-faculties', 'Admin\AjaxController@courses_faculties');
    Route::post('courses-batches', 'Admin\AjaxController@courses_batches');
    Route::post('courses-batches-multiple', 'Admin\AjaxController@courses_batches_multiple');
    Route::post('faculties-subjects', 'Admin\AjaxController@faculties_subjects');
    Route::post('courses-subjects', 'Admin\AjaxController@courses_subjects');
    Route::post('course-sessions', 'Admin\AjaxController@course_sessions');
    Route::post('course-topics', 'Admin\AjaxController@course_topics');
    Route::post('course-topic', 'Admin\AjaxController@course_topic');

    Route::post('batch-details', 'Admin\AjaxController@batch_details');
    Route::post('faculties-subjects-for-batches-schedules', 'Admin\AjaxController@faculties_subjects_for_batches_schedules');
    Route::post('courses-faculties-subjects-batches', 'Admin\AjaxController@courses_faculties_subjects_batches');
    Route::post('institute-courses', 'Admin\AjaxController@institute_courses');
    Route::post('course-subjects', 'Admin\AjaxController@course_subjects');
    Route::post('faculty-subjects', 'Admin\AjaxController@faculty_subjects');
    Route::post('shipment', 'Admin\AjaxController@shipment');
    Route::post('courses-faculties-batches', 'Admin\AjaxController@courses_faculties_batches');
    Route::post('courses-subjects-batches', 'Admin\AjaxController@courses_subjects_batches');
    Route::post('institute-courses-for-topics-batches', 'Admin\AjaxController@institute_courses_for_topics_batches');
    Route::post('courses-faculties-topics-batches', 'Admin\AjaxController@courses_faculties_topics_batches');
    Route::post('courses-subjects-topics-batches', 'Admin\AjaxController@courses_subjects_topics_batches');
    Route::post('institute-courses-for-lectures-topics-batches', 'Admin\AjaxController@institute_courses_for_lectures_topics_batches');
    Route::post('courses-faculties-topics-batches-lectures', 'Admin\AjaxController@courses_faculties_topics_batches_lectures');
    Route::post('courses-subjects-topics-batches-lectures', 'Admin\AjaxController@courses_subjects_topics_batches_lectures');
    Route::post('branch-institute-courses', 'Admin\AjaxController@branch_institute_courses');
    Route::post('branches-courses-faculties-batches', 'Admin\AjaxController@branches_courses_faculties_batches');
    Route::post('branches-courses-subjects-batches', 'Admin\AjaxController@branches_courses_subjects_batches');
    Route::post('institute-courses-for-lectures-videos', 'Admin\AjaxController@institute_courses_for_lectures_videos');
    Route::post('lecture-videos', 'Admin\AjaxController@lecture_videos');

    Route::post('lecture-videos/trash_list', 'Admin\AjaxController@trash');


    Route::post('course-changed-in-lecture-videos', 'Admin\AjaxController@course_changed_in_lecture_videos');
    Route::post('disciplines-by-multiple-faculties', 'Admin\AjaxController@disciplines_by_multiple_faculties');
    Route::post('faculty-changed-in-lecture-videos', 'Admin\AjaxController@faculty_changed_in_lecture_videos');
    Route::post('course-changed-in-batch-discipline-fee', 'Admin\AjaxController@course_changed_in_batch_discipline_fee');
    Route::post('faculty-changed-in-batch-discipline-fee', 'Admin\AjaxController@faculty_changed_in_batch_discipline_fee');
    Route::post('batch-subjects', 'Admin\AjaxController@batch_subjects');
    Route::post('batch-faculties', 'Admin\AjaxController@batch_faculties');
    Route::post('online-exams', 'Admin\AjaxController@online_exams');
    Route::post('course-changed-in-online-exams', 'Admin\AjaxController@course_changed_in_online_exams');
    Route::post('faculty-changed-in-online-exams', 'Admin\AjaxController@faculty_changed_in_online_exams');

    Route::post('batch-changed-in-schedule', 'Admin\AjaxController@batch_changed_in_schedule');
    Route::post('faculty-changed-in-schedule', 'Admin\AjaxController@faculty_changed_in_schedule');

    Route::post('/institute-courses-in-package', 'Admin\AjaxController@institute_courses_in_package');
    Route::post('/course-changed-in-package', 'Admin\AjaxController@course_changed_in_package');
    Route::post('/faculty-changed-in-package', 'Admin\AjaxController@faculty_changed_in_package');



    Route::post('reg-no', 'Admin\AjaxController@reg_no');
    Route::post('set-weekday', 'Admin\AjaxController@set_weekday');
    Route::post('institute-course', 'Admin\AjaxController@institute_course');
    Route::post('course-faculty', 'Admin\AjaxController@course_faculty');
    Route::post('faculty-subject', 'Admin\AjaxController@faculty_subject');
    Route::post('course-subject', 'Admin\AjaxController@course_subject');
    Route::post('question-type-mcq-sba', 'Admin\AjaxController@question_type_mcq_sba');
    Route::get('search-doctors', 'Admin\AjaxController@search_doctors');
    Route::get('search-classes', 'Admin\AjaxController@search_classes');
    // Route::post('search-session-batch', 'Admin\AjaxController@search_session_batch');

    Route::post('search-session', 'Admin\AjaxController@search_session');
    Route::get('session-course-search', 'Admin\AjaxController@session_course_search');

    Route::get('course-class-change', 'Admin\AjaxController@course_class_change');

    Route::get('class-search-session', 'Admin\AjaxController@class_search_session');
    Route::get('schedule-search-session', 'Admin\AjaxController@schedule_search_session');

    Route::get('class-course-search', 'Admin\AjaxController@class_course_search');
    Route::get('schedule-course-search', 'Admin\AjaxController@schedule_course_search');
    Route::get('course-search-by-year', 'Admin\AjaxController@courseSearchByYear');


    Route::post('search-batch', 'Admin\AjaxController@search_batch');
    Route::post('exam-search-batches', 'Admin\AjaxController@exam_search_batches');
    Route::post('view-result-search-batch', 'Admin\AjaxController@view_result_search_batch');

    Route::post('doctor-group-search-course', 'Admin\AjaxController@doctor_group_search_course');



    Route::post('doctor-course-search', 'Admin\AjaxController@doctor_course_search');
    Route::get('batch-search-get-multi-select', 'Admin\AjaxController@batchSearchGetMultiSelect');
    Route::post('doctor-batch-search', 'Admin\AjaxController@doctor_batch_search');
    Route::post('doctor-video-search', 'Admin\AjaxController@doctor_video_search');

    Route::get('complain-print/{paras}', 'Admin\DoctorAskReplyController@complain_print');

    Route::get('doctor-matched-in-group', 'Admin\DoctorGroupController@doctor_matched_in_group');
    //mcq filtering
    Route::post('search-chapter-list', 'Admin\AjaxController@search_chapter_list');
    Route::post('search-topic-list', 'Admin\AjaxController@search_topic_list');
    Route::post('search-source-course', 'Admin\AjaxController@search_source_course');
    Route::post('search-source-faculty', 'Admin\AjaxController@search_source_faculty');
    Route::post('search-source-subject', 'Admin\AjaxController@search_source_subject');




    //mk create
    Route::post('search-course', 'Admin\AjaxController@search_course');
    Route::post('session-batch', 'Admin\AjaxController@session_batch_sarching');
    Route::post('discount-batch', 'Admin\AjaxController@discount_batch');

    Route::post('session-searching', 'Admin\AjaxController@session_search');
    Route::post('batch-searching', 'Admin\AjaxController@batch_search');

    //batch search for payment
    Route::post('batch-search-payment', 'Admin\AjaxController@batch_search_payment');
    Route::post('session-search-payment', 'Admin\AjaxController@session_search_payment');
    //lectureshet coureer
    // Route::post('lecture-sheet-yes', 'Admin\AjaxController@lecture_sheet_yes');


    Route::get('search-batches', 'Admin\AjaxController@search_batches');
    Route::get('search-questions', 'Admin\AjaxController@search_questions');
    Route::get('search-questions-2', 'Admin\AjaxController@search_questions_2');

    Route::post('add-schedule-row', 'Admin\AjaxController@add_schedule_row');

    Route::post('question-info', 'Admin\AjaxController@question_info');

    Route::post('check-bmdc-no', 'Admin\AjaxController@check_bmdc_no');

    Route::post('check-phone-no', 'Admin\AjaxController@check_phone_no');

    Route::post('check-email', 'Admin\AjaxController@check_email');

    Route::post('notice-type', 'Admin\AjaxController@notice_type');
    Route::get('notice_search-doctors', 'Admin\AjaxController@notice_search_doctors');
    Route::post('notice-institute-course', 'Admin\AjaxController@notice_institute_course');
    Route::post('notice-course-batch', 'Admin\AjaxController@notice_course_batch');


    //Route::post('institute-courses', 'Admin\AjaxController@institute_courses');
    Route::post('course-changed', 'Admin\AjaxController@course_changed');
    Route::post('faculty-subjects-in-admission', 'Admin\AjaxController@faculty_subjects_in_admission');
    Route::post('courses-branches-batches', 'Admin\AjaxController@courses_branches_batches');
    Route::post('registration-no', 'Admin\AjaxController@registration_no');
    Route::post('batch-details', 'Admin\AjaxController@batch_details');

    Route::post('institute-changed-in-question-reference-exam', 'Admin\AjaxController@institute_changed_in_question_reference_exam');
    Route::post('course-changed-in-question-reference-exam', 'Admin\AjaxController@course_changed_in_question_reference_exam');

    Route::post('institute-changed-in-question-search-options', 'Admin\AjaxController@institute_changed_in_question_search_options');
    Route::post('course-changed-in-question-search-options', 'Admin\AjaxController@course_changed_in_question_search_options');

    Route::get('allocation-institute-discipline', 'Admin\AjaxController@allocation_institute_discipline');
    Route::get('allocation-courses', 'Admin\AjaxController@allocation_courses');

    // search batch id for discount

    Route::get('batch-id/{batch_id}', 'Admin\AjaxController@batch_id_for_amount');
    Route::post('message', 'Admin\DoctorsCoursesController@message');
    Route::post('message2', 'Admin\DoctorsCoursesController@message2');
    // Route::post('message', 'Admin\DoctorsCoursesController@doctor_course_lecture_sheet');
    // Route::get('view-course-result/{id}', 'Admin\DoctorsCoursesController@course_result');  //duplicate for all course result

    Route::get('sms-log', 'Admin\SmsLogController@index');
    Route::get('sms-log-ajax-list', 'Admin\SmsLogController@sms_log_ajax_list');
    Route::post('update-status', 'Admin\SmsLogController@update_status');

    Route::get('send-Sms', 'Admin\DoctorsController@sendSms');

    // conversation sms
    Route::resource('conversation-sms', 'Admin\ConversationSmsController');
    Route::get('search-doctors-phone', 'Admin\ConversationSmsController@search_doctors_phone');
    Route::post('question-link', 'Admin\ConversationSmsController@question_link');

    Route::get('load-view', 'Admin\AjaxController@load_view')->name('admin.load_view');

    // Route::get('app-info', 'AppInfoController@app_versions');
    // Route::get('app-info/create', 'AppInfoController@create');

    Route::resource('app-info', 'AppInfoController');


    Route::get('successful-feedback', 'Admin\SuccessfulController@index');
    Route::get('successful_feedback_view/{id}', 'Admin\SuccessfulController@successfulview');

    // faculty-list-query

    Route::get('faculty-list', 'Admin\FacultyController@faculty_list');
    Route::get('subjects-list', 'Admin\SubjectsController@discipline_list');
    // Batch Discipline Fee List
    Route::get('batch-discipline-list', 'Admin\BatchDisciplineFeeController@batch_discipline_list');

    //Batch Faculty Fee List
    Route::get('batch-faculty-fee-list', 'Admin\BatchFacultyFeeController@batch_faculty_fees_list');

    //Question Topic List
    Route::get('question-topic-list', 'Admin\QuestionTopicController@question_topic_list');

    // lecture video list
    Route::get('lecture-video-list', 'Admin\LectureVideoController@lecture_video_list');

    // lsubscription video list
    Route::get('subscription-video-list', 'Admin\LectureVideoController@subscription_video_list');
    Route::post('marked-subscription-video-update', 'Admin\AjaxController@marked_subscription_video_update');

    // Class/Chapter List
    Route::get('class-chapter-list', 'Admin\TopicController@class_chapter_list');

    // Exam Assign List
    Route::get('exam-assign-list', 'Admin\ExamAssignController@exam_assign_list');

    // Available Batches List
    Route::get('available-batches-list', 'Admin\AvailableBatchesController@available_batche_list');

    // Lecture Video Assign
    Route::get('lecture-video-assign-list', 'Admin\LectureVideoAssignController@lecture_video_assign_list');

    // Conversation SMS List
    Route::get('conversation-sms-list', 'Admin\ConversationSmsController@con_sms_list');

    // Lecture Video batch List
    Route::get('lecture-video-batch-list', 'Admin\LectureVideoBatchController@lecture_video_batch_list');

    // Notice List
    Route::get('notice-list', 'Admin\NoticeController@notice_list');


    // update for complain box
    Route::resource('user-complain-assign', 'Admin\UserComplainAssignController');



    // Exam List
    Route::get('admin-exam-list', 'Admin\ExamController@exam_list');

    // Doctors Login Activity List
    Route::get('doctors-app-activity', 'Admin\DoctorsActivityController@index');

    Route::get('doctors-app-activity-list', 'Admin\DoctorsActivityController@doctors_activity_list');

    Route::get('results/{batch}', 'Admin\ResultController@index')->name('admin.result.index');
    Route::get('results/{batch}/excel-download', 'Admin\ResultController@excelDownload')->name('admin.results.excel.download');


    Route::get('send-sms-for-absent-in-exam/{batch}/{exam}/{doctor_id?}', 'Admin\ResultController@sendSmsForAbsentInExam');

    Route::get('send-sms-for-question-print', 'Admin\QuestionsController@sendSmsForQuestionPrint');

    Route::get('/go-to-doctor-profile/{doctor}', 'Admin\GoToDoctorProfileController')->name('go-to-doctor-profile');

    Route::get('request-lecture-videos', 'Admin\RequestLectureVideoController@index')->name('request-lecture-videos.index');
    Route::get('request-lecture-videos/{pending_lecture}', 'Admin\RequestLectureVideoController@show')->name('request-lecture-videos.show');
    Route::put('request-lecture-videos/{pending_lecture}', 'Admin\RequestLectureVideoController@update')->name('request-lecture-videos.update');

    Route::get('pending-videos', 'Admin\PendingVideoController@index')->name('pending-videos.index');
    Route::get('pending-videos/{batch}', 'Admin\PendingVideoController@show')->name('pending-videos.show');
    Route::get('pending-videos/{batch}/assign', 'Admin\PendingVideoController@prepare')->name('pending-videos.prepare');
    Route::post('pending-videos/{batch}/assign', 'Admin\PendingVideoController@assign')->name('pending-videos.assign');


    // On-spot Quiz
    Route::get('quizzes', 'Admin\QuizController@index')->name('quizzes.index');
    Route::get('quizzes/{quiz}', 'Admin\QuizController@show')->name('quizzes.show');
    Route::post('quizzes/save/{quiz?}', 'Admin\QuizController@save')->name('quizzes.save');

    // Quiz Assign
    Route::get('quizzes/{quiz}/assign/{type}', 'Admin\QuizController@prepare')->name('quizzes.assign');
    Route::post('quizzes/{quiz}/assign/{type}', 'Admin\QuizController@assign');

    // Quiz Properties
    Route::get('quiz-properties', 'Admin\QuizPropertyController@index')->name('quiz-properties.index');
    Route::post('quiz-properties/save/{quiz_property?}', 'Admin\QuizPropertyController@save')->name('quiz-properties.save');

    // Quiz Result
    Route::get('/quiz-results', 'Admin\QuizController@result')->name('quiz-results');

    // Subscriptions
    Route::group(['prefix' => 'subscriptions'], function () {
        Route::get('/', 'Admin\SubscriptionController@index')->name('subscriptions.index');
        Route::get('/download/{type?}', 'Admin\SubscriptionController@download')->name('subscriptions.download');
        
        Route::group(['prefix' => 'subscribers'], function () {
            Route::get('/', 'Admin\SubscriberController@index')->name('subscribers.index');
            Route::get('/{subscriber}', 'Admin\SubscriberController@show')->name('subscribers.show');
            Route::get('/{subscriber}/orders', 'Admin\SubscriberOrderController@index')->name('subscribers.orders.index');
            Route::get('/{subscriber}/orders/{order}', 'Admin\SubscriberOrderController@show')->name('subscribers.orders.show');
        });
        
        Route::post('/orders/{order}/payments', 'Admin\SubscriptionOrderPaymentController@store')->name('subscriptions.orders.payments.store');
    });
    
    //Manual Payments
    Route::group(['prefix' => 'manual-payments'], function () {
        Route::get('/', 'Admin\ManualPaymentController@index')->name('manual-payments.index');
        Route::get('/download/{type?}', 'Admin\ManualPaymentController@download')->name('manual-payments.download');
    });
    
    //Format
    Route::group(['prefix' => 'formats'], function () {
        Route::get('/', 'Admin\FormatController@index')->name('formats.index');
        Route::put('/{format}', 'Admin\FormatController@update')->name('formats.update');
    });

    // addon services
    Route::get('addon-services', 'Admin\AddonServiceController@index')->name('addon-services.index');
    Route::post('addon-services', 'Admin\AddonServiceController@store')->name('addon-services.store');
    Route::get('addon-services/{addon_service}', 'Admin\AddonServiceController@show')->name('addon-services.show');
    Route::put('addon-services/{addon_service}', 'Admin\AddonServiceController@update')->name('addon-services.update');
    Route::get('addon-services/{addon_service}/assign/{content_type}', 'Admin\AddonServiceController@prepare')->name('addon-services.prepare');
    Route::post('addon-services/{addon_service}/assign/{content_type}', 'Admin\AddonServiceController@assign')->name('addon-services.assign');

    Route::get('reference-books', 'Admin\ReferenceBookController@index')->name('reference-books.index');
    Route::post('reference-books', 'Admin\ReferenceBookController@store')->name('reference-books.store');
    Route::put('reference-books/{reference_book}', 'Admin\ReferenceBookController@update')->name('reference-books.update');
    Route::get('reference-books/{reference_book}', 'Admin\ReferenceBookController@show')->name('reference-books.show');

    Route::post('reference-books/{reference_book}/reference-book-pages', 'Admin\ReferenceBookPageController@store')->name('reference-books.reference-book-pages.store');
    Route::put('reference-book-pages/{reference_book_page}', 'Admin\ReferenceBookPageController@update')->name('reference-book-pages.update');

    // Uploads Image Get Link
    Route::get('upload-image-get-link', 'Controller@formImageGetLink')->name('form-image-get-link')->middleware('auth');
    Route::post('upload-image-get-link/{directory?}/{sotre?}', 'Controller@uploadImageGetLink')->name('upload-image-get-link');

    // Save Sorting
    Route::post('sort--data', 'Controller@sortData')->name('sort--data');
    Route::resource('mentors', 'Admin\MentorController');
});


Route::get('join', 'Auth\JoinController@create')->middleware('guest:doctor')->name('join');
Route::post('join', 'Auth\JoinController@store')->middleware('guest:doctor');

Route::get('v1/join', 'Auth\V1\JoinController@create')->middleware('guest:doctor')->name('v1.join');
Route::post('v1/join', 'Auth\V1\JoinController@store')->middleware('guest:doctor');

Route::get('v1/login', 'Auth\V1\LoginController@create')->middleware('guest:doctor')->name('v1.login');
Route::post('v1/login', 'Auth\V1\LoginController@store')->middleware('guest:doctor');

Route::get('/on-spot-quiz', 'QuizController@index')->name('on-spot-quiz.index');
Route::get('/on-spot-quiz/{key}', 'QuizController@show')->name('on-spot-quiz.show')->middleware('auth:doctor');

Route::prefix('my/subscriptions')->middleware(['auth:doctor'])->group(function () {
    Route::get('/', 'SubscriptionController@index')->name('my.subscriptions.index');

    Route::get('/groups/{group}', 'SubscriptionController@groupsShow')->name('my.subscriptions.groups.show');

    Route::get('/add-subscription', 'SubscriptionController@addSubscription')->name('my.subscriptions.add-subscription');
    Route::post('/add-subscription', 'SubscriptionController@addSubscriptionNext');

    Route::get('/available/{year}/{course}/{session}', 'SubscriptionController@available')->name('my.subscriptions.available');
    Route::post('/available/{year}/{course}/{session}', 'SubscriptionController@availableNext');

    Route::get('/order-confirm/{year}/{course}/{session}', 'SubscriptionController@orderConfirm')->name('my.subscriptions.order-confirm');

    Route::get('/orders', 'SubscriptionController@orderIndex')->name('my.subscriptions.orders.index');
    Route::get('/orders/{order}', 'SubscriptionController@orderShow')->name('my.subscriptions.orders.show');

    Route::get('/orders/{order}/payment', 'SubscriptionController@orderPayment')->name('my.subscriptions.orders.payment');
});

// manual payments
Route::prefix('manual-payments')->middleware(['auth:doctor'])->group(function () {
    Route::get('/subscription-orders/{subscription_order}/{amount?}', 'ManualPayment\SubscriptionOrderController@create');
    Route::post('/subscription-orders/{subscription_order}/{amount?}', 'ManualPayment\SubscriptionOrderController@store')->name('manual-payments.subscription-order');
});

Route::get('special-group', 'DoctorSpecialGroupController@group_list')->name('doctor-group.list');
Route::get('special-group-exams/{group_id}', 'DoctorSpecialGroupController@group_exams')->name('doctor-group.exams');



Route::get('doctor-course-lecture-video/{id}', 'LectureVideoController@doctor_course_lecture_video');
Route::get('doctor-course-lecture-video-ajax/{id}', 'LectureVideoController@doctor_course_lecture_video_ajax');

Route::get('lecture-video-details/{id}', 'LectureVideoController@lecture_video_details');



Route::get('online-exam-details/{id}', 'OnlineExamController@online_exam_details');


Route::get('schedule_a', 'HomeController@schedule_a');
Route::get('/', 'HomeController@index')->name('home');
Route::get('login-phone', 'PageController@login_phone')->name('login_phone');

// Route::get('/home', 'HomeController@index')->name('home');
Route::get('course', 'HomeController@course')->name('course');
Route::get('trash-doctors-courses', 'TrashController@trash_doctors_courses');

Route::get('coursess', 'MergeController@course');
Route::get('question', 'MergeController@questions');

Route::get('aboutus', 'PageController@aboutus');
Route::get('course', 'PageController@course');

Route::get('course-detail/{id}', 'PageController@course_detail');

// Route::get('successstories', 'PageController@successstories')->name('successstories');
Route::get('gallery', 'PageController@gallery');
Route::get('batch', 'PageController@batch');
Route::get('batch-fcps-p-1', 'PageController@fcps_p_1');
Route::get('batch-residency', 'PageController@residency');
Route::get('batch-outlier', 'PageController@outlier');
Route::get('batch-diploma', 'PageController@diploma');
Route::get('batch-combined', 'PageController@combined');


Route::get('batch-details/{batch_id}', 'PageController@batch_details');
Route::get('admission-link/{batch_id}', 'PageController@batch_admission_link');

Route::get('contactus', 'PageController@contactus')->name('contactus');
Route::get('privacy-policy', 'PageController@privacy_policy')->name('privacy-policy');
Route::get('terms-condition', 'PageController@terms_condition')->name('terms-condition');
Route::get('refund-policy', 'PageController@refund_policy')->name('refund-policy');
Route::get('faq', 'PageController@faq')->name('faq');
Route::get('faq-details/{id}', 'PageController@faq_details')->name('faq-details');

// invoice download link
Route::get('payment-details-pdf-public/{id}', 'HomeController@payment_details_pdf_public');

Route::get('promo-code/{doctor_course_id}', 'HomeController@promo_code');
Route::post('apply-promo-code', 'HomeController@apply_promo_code');

//Doctor Profile Links
Route::middleware(['checkToken'])->group(function () {
    Route::get('dashboard', ['middleware' => 'auth:doctor', 'uses' => 'HomeController@dashboard'])->name('dashboard');
    Route::get('my-profile', ['middleware' => 'auth:doctor', 'uses' => 'HomeController@my_profile']);
    Route::get('my-profile/edit/{id}', 'HomeController@edit_profile')->name('edit_profile');
    Route::post('update-profile', 'HomeController@update_profile')->name('update_profile');

    Route::post('/bmdc-email-medical', 'HomeController@bmdc_email_medical');


    Route::post('change-lecture-sheet-collection', 'AjaxController@change_lecture_sheet_collection');
    Route::post('change-include-lecture-sheet', 'AjaxController@change_include_lecture_sheet');
    Route::post('courier-division-district', 'AjaxController@courier_division_district');
    Route::post('courier-district-upazila', 'AjaxController@courier_district_upazila');

    Route::get('doctor-admissions', 'DoctorsAdmissionsController@doctor_admissions')->name('doctor_admission');
    Route::post('doctor-admission-submit', 'DoctorsAdmissionsController@doctor_admission_submit');


    Route::get('payment-history/{doctor_course_id}', 'HomeController@payment_history');
    Route::get('payment-detail/{doctor_course_id}', 'HomeController@payment_detail');
    Route::get('payment/{doctor_course_id}', 'HomeController@payment_detail');
    Route::get('installment-payment/{doctor_course_id}', 'HomeController@installment_payment');
    Route::get('payment-details', 'HomeController@payment_details')->name('payment_details');
    Route::get('payment-details-pdf/{id}', 'HomeController@payment_details_pdf');
    Route::post('pay-now', 'HomeController@pay_now');
    Route::post('get-full-payment-waiver', 'HomeController@get_full_payment_waiver');
    Route::post('set-payment-option', 'HomeController@set_payment_option');
    // Route::get('payment/{course_id}', 'HomeController@payment');
    Route::get('payment-success', 'HomeController@payment_success');

    Route::get('my-courses', ['middleware' => 'auth:doctor', 'uses' => 'HomeController@my_courses'])->name('my_courses');
    Route::get('my-courses/edit-doctor-course-discipline/{doctor_course_id}', 'DoctorsAdmissionsController@edit_doctor_course_discipline');
    Route::get('my-courses/edit-doctor-course-candidate/{doctor_course_id}', 'DoctorsAdmissionsController@edit_doctor_course_candidate');

    Route::get('doctor-result', 'HomeController@doctor_result')->name('doctor_result');

    Route::get('schedule', 'BatchScheduleController@batch_schedules')->name('batch_schedules');
    Route::get('schedule/master-schedule', 'BatchScheduleController@master_schedules');

    Route::get('lecture-video', 'LectureVideoController@lecture_videos');
    Route::get('lecture-video/{course_id}/{batch_id}', 'LectureVideoController@lecture_video');

    Route::get('doctor-lecture-sheet-delivery-print/{doctor_course_id}', 'LectureSheetArticleController@doctor_course_lecture_sheet_delivery_print');
    Route::get('lecture-sheet-article', 'LectureSheetArticleController@lecture_sheet_article');
    Route::get('lecture-sheet-article-topics/{id}', 'LectureSheetArticleController@lecture_sheet_article_topics');
    Route::get('lecture-sheet-article-details/{id}', 'LectureSheetArticleController@lecture_sheet_article_details');
    Route::get('topic-lecture-sheet-articles/{lecture_sheet_article_batch_id}/{topic_id}', 'LectureSheetArticleController@topic_lecture_sheet_articles');

    Route::get('online-exam', 'OnlineExamController@online_exams');
    Route::get('online-exam/{course_id}/{batch_id}', 'OnlineExamController@online_exam');
    Route::get('doctor-course-online-exam/{id}', 'OnlineExamController@doctor_course_online_exam');
    Route::get('doctor-course-online-exam-ajax/{id}', 'OnlineExamController@doctor_course_online_exam_ajax');

    Route::get('notice', 'HomeController@notice')->name('notice');
    Route::get('notice/notice-details/{id}', 'HomeController@notice_details')->name('notice_details');

    Route::get('question-box', 'HomeController@question_box')->name('question_box');
    Route::get('question-add', 'HomeController@question_add')->name('question_add');

    Route::get('complain-box', 'HomeController@complain_box')->name('complain_box');
    Route::post('submit-complain', 'HomeController@submit_complain');
    Route::get('complain-details/{id}', 'HomeController@complain_details')->name('complain_details');
    Route::post('complain-again', 'HomeController@complain_again');

    Route::get('change-password', 'HomeController@change_password')->name('change_password');
    Route::post('update-password', 'HomeController@update_password');
});

Route::get('doc-profile/print-batch-schedule/{id}', 'Admin\BatchesSchedulesController@print_batch_schedule');
Route::get('doc-profile/view-course-result/{id}', 'HomeController@course_result');
//my-orders
Route::get('my-orders/{id}', ['middleware' => 'auth:doctor', 'uses' => 'HomeController@my_orders']);
Route::post('lecture-sheet-delivery/feedback', ['middleware' => 'auth:doctor', 'uses' => 'HomeController@lecture_sheet_delivery_feedback'])->name('lecture_sheet_delivery.feedback');

//new schedule
Route::get('new-schedule/{id}/{doctor_course_id}', 'BatchScheduleController@new_schedule');
Route::get('view-batch-schedule/{batch_id}', 'BatchScheduleController@view_batch_schedule');
Route::get('view-batch-schedule/{batch_id}/{faculty_or_discipline_id?}/{bcps_subject_id?}', 'BatchScheduleController@view_batch_schedule');

Route::get('full-schedule/{id}', 'BatchScheduleController@full_schedule')->name('full-schedule');
Route::get('schedule-print/{id}', 'BatchScheduleController@schedule_print')->name('schedule-print');
Route::get('schedule-print-table/{id}', 'BatchScheduleController@schedule_print_table')->name('schedule-print-table');
Route::get('new-schedule-single/{slot_id}/{doctor_course_id}', 'BatchScheduleController@new_schedule_single')->name('new-schedule-single');

//end myorders
Route::post('update-doctor-course-discipline', 'DoctorsAdmissionsController@update_doctor_course_discipline')->name('update_doctor_course_discipline');
Route::post('update-doctor-course-candidate', 'DoctorsAdmissionsController@update_doctor_course_candidate')->name('update_doctor_course_candidate');

// discount code request
Route::get('discount-request/{course_id}', 'HomeController@discount_request');
Route::post('discount-request-submit', 'HomeController@discount_request_submit');


Route::get('evaluate-teacher', 'HomeController@evaluate_teacher')->name('evaluate_teacher');
Route::get('online-lecture', 'HomeController@online_lecture')->name('online_lecture');
//Route::get('online-exam', 'HomeController@online_exam')->name('online_exam');

Route::get('result', 'HomeController@result')->name('result');
Route::get('schedule/new-schedule/{id}/{doctor_course_id}', 'BatchScheduleController@new_schedule');


Route::get('doctor-course-batch-schedule/{id}', 'BatchScheduleController@doctor_course_batch_schedule')->name('doctor_course_batch_schedule');

Route::post('register-post', 'PageController@register')->name('register-post');

Route::post('course-batch', 'AjaxController@course_batch');
Route::post('batch-lecture-video', 'AjaxController@batch_lecture_video');

Route::post('question-submit', 'HomeController@question_submit');
Route::get('question-answer/{id}', 'HomeController@question_answer');
Route::post('question-submit-final', 'HomeController@question_submit_final');
Route::get('question-answer/{id}', 'HomeController@question_answer');
Route::get('view-answer/{id}', 'HomeController@view_answer');
Route::post('question-again', 'HomeController@question_again');

Route::post('submit-question', 'HomeController@submit_question');
Route::get('question-delete/{id}', 'HomeController@question_delete')->name('question_delete');

Route::post('send-otp', 'HomeController@send_otp');
Route::post('submit-otp', 'HomeController@submit_otp');

Route::post('batch-lecture', 'AjaxController@batch_lecture');
Route::post('batch-details-modal', 'AjaxController@batch_details_modal');


Route::get('doctor-batch-exam-reopen/{doctor_course_id}/{exam_id}', 'ExamController@doctor_batch_exam_reopen');
Route::get('continue-doctor-exam/{doctor_course_id}/{exam_id}', 'ExamController@continue_doctor_exam');
Route::get('doctor-course-exam/{doctor_course_id}/{exam_id}/{schedule_id?}', 'ExamController@exam')->name('doctor-course-exam');
Route::get('doctor-course-class/{lecture_video_id}/{doctor_course_id?}', 'LectureVideoController@lecture_details')->name('doctor-course-class');
Route::get('course-exam-result-submit/{doctor_course_id}/{exam_id}/{schedule_id?}', 'ExamController@course_exam_result_submit');
Route::get('course-view-result/{doctor_course_id}', 'ExamController@course_result');
Route::get('course-exam-result/{doctor_course_id}/{exam_id}/{schedule_id?}', 'ExamController@course_exam_result');

Route::get('course-exam-doctor-answer/{doctor_course_id}/{exam_id}/{schedule_id?}', 'ExamController@course_exam_doctor_answer');

Route::get('reference-book-detail/{reference_book_id}/{page_no}', 'ExamController@reference_book_details')->name('reference-book-detail');

Route::get('only-question-list/{doctor_course_id}/{exam_id}', 'ExamController@only_question_list');
Route::get('question-answer-list/{doctor_course_id}/{exam_id}', 'ExamController@question_answer_list');



Route::post('/submit-answer', 'AjaxController@submit_answer');
Route::post('/submit-answer-and-terminate-exam', 'AjaxController@submit_answer_and_terminate_exam');
Route::post('/skip-question', 'AjaxController@skip_question');

//Merit List
Route::get('doctor-institute-choices', 'DoctorInstituteChoiceController@create')->name('doctor-institute-choices.create');
Route::post('doctor-institute-choices', 'DoctorInstituteChoiceController@store')->name('doctor-institute-choices.store');

Route::post('collect-doctor-roll', 'CollectDoctorRollController@store')->name('collect-doctor-roll.store');

/*Ajax*/
Route::post('permanent-division-district', 'AjaxController@permanent_division_district');
Route::post('permanent-district-upazila', 'AjaxController@permanent_district_upazila');
Route::post('present-division-district', 'AjaxController@present_division_district');
Route::post('present-district-upazila', 'AjaxController@present_district_upazila');


Route::post('branches-courses-faculties-batches', 'AjaxController@branches_courses_faculties_batches');
Route::post('branches-courses-subjects-batches', 'AjaxController@branches_courses_subjects_batches');
Route::post('branches-courses-subjects-batches', 'AjaxController@branches_courses_subjects_batches');

Route::post('institute-courses', 'AjaxController@institute_courses');
Route::post('course-sessions-faculties', 'AjaxController@course_sessions_faculties');
Route::post('course-sessions-subjects', 'AjaxController@course_sessions_subjects');
Route::post('courses-branches-subjects-batches', 'AjaxController@courses_branches_subjects_batches');
Route::post('courses-branches-batches', 'AjaxController@courses_branches_batches');
Route::post('course-changed', 'AjaxController@course_changed');
Route::post('faculty-subjects-in-admission', 'AjaxController@faculty_subjects_in_admission');

//d
Route::post('faculty-subjects', 'AjaxController@faculty_subjects');
Route::post('courses-faculties-batches', 'AjaxController@courses_faculties_batches');
Route::post('courses-subjects-batches', 'AjaxController@courses_subjects_batches');
Route::post('reg-no', 'AjaxController@reg_no');

// Route::post('batch-details', 'AjaxController@batch_details');

Route::get('sms-installment-due-list', 'DefaultController@sms_to_installment_due_list');



Route::post('division-district', 'AjaxController@division_district');
Route::post('district-upazila', 'AjaxController@district_upazila');
Route::post('payment-create/{doctor_id}/{course_id}/{lecture_sheet}', 'HomeController@payment_create');
Route::post('payment-create/{doctor_id}/{course_id}', 'HomeController@payment_create');
Route::get('payment-success/{course_id}/{card_no}/{payment_serial}/{amount}', 'HomeController@payment_success');
Route::get('merge', 'MergeController@course');
Route::post('payment-manual/{doctor_course_id}', 'HomeController@payment_manual');
Route::post('payment-manual-installment/{doctor_course_id}', 'HomeController@payment_manual_installment');
Route::post('payment-manual-save', 'HomeController@payment_manual_save');



Route::post('submit-doctor-ratting', 'DoctorRattingController@submit_doctor_ratting')->name('submit_doctor_ratting')->middleware("auth:doctor");
Route::get('doctor-ratting-modal', 'DoctorRattingController@doctor_ratting_modal')->name('doctor_ratting_modal')->middleware("auth:doctor");


Route::get('app-info', 'AppInfoController@app_versions');

// enroll
Route::get('/password/{mobile_number}/{schedule_id}', 'BatchEnrollController@password');
Route::post('/password-submit-auto', 'BatchEnrollController@password_submit');
Route::get('/enroll-now/{schedule_id}', 'BatchEnrollController@enroll_now');
Route::get('/lecture-sheet-without-batch/{mobile_number}/{schedule_id}', 'BatchEnrollController@lecture_sheet_without_batch');
Route::post('/lecture-sheet', 'BatchEnrollController@lecture_sheet_submit_batch');
Route::get('/discipline-terms-condition', 'BatchEnrollController@discipline_terms_condition');
Route::post('/doctor-course-information-update', 'BatchEnrollController@doctor_course_information_update');


Route::get('/password-request-from-available-batch/{schedule_id}', 'BatchEnrollController@password_request_from_available_batch');
Route::post('/password-submit-from-available-batch', 'BatchEnrollController@password_submit_from_available_batch');

Route::post('/register-name', 'BatchEnrollController@register_name');

Route::post('change-lecture-sheet-collection', 'AjaxController@change_lecture_sheet_collection');
Route::post('change-include-lecture-sheet', 'AjaxController@change_include_lecture_sheet');
Route::post('courier-division-district', 'AjaxController@courier_division_district');
Route::post('courier-district-upazila', 'AjaxController@courier_district_upazila');

//success-list

Route::get('success-list', 'SuccessController@add_personal_details');
Route::post('successfull-personal-detail-submit', 'SuccessController@successfull_personal_detail_submit');
Route::get('genesis-batch-details', 'SuccessController@genesis_batch_details');
Route::post('genesis-batch-details-submit', 'SuccessController@genesis_batch_details_submit');
Route::get('feedback-about-genesis', 'SuccessController@feedback_about_genesis');
Route::post('feedback-about-submit', 'SuccessController@feedback_about_submit');
Route::get('struggling-history', 'SuccessController@struggling_history');
Route::post('struggling-history-submit', 'SuccessController@struggling_history_submit');
Route::get('effective-service', 'SuccessController@effective_service');
Route::get('struggling-history', 'SuccessController@struggling_history');
Route::post('effective-service-submit', 'SuccessController@effective_service_submit');


Route::get('/set-login-access-token', function (Request $request) {
    $request->session()->put('_token', $request->get('_token'));
});

//Doctor Batch Schedule
Route::get('/doctor-course-list-in-schedule', 'HomeController@doctor_course_list_in_schedule');
Route::get('/doctor-course-schedule/{doctor_course_id}', 'HomeController@doctor_course_schedule');
Route::get('/doctor-course-schedule-lecture-video/{lecture_video_id}/{doctor_course_id}', 'HomeController@doctor_course_schedule_lecture_video');

//System driven
Route::get('doctor-course-system-driven/{id}', 'Admin\DoctorsCoursesController@system_driven');
Route::post('doctor-course-system-driven-save', 'Admin\DoctorsCoursesController@system_driven_save');
Route::get('batch-system-driven/{id}', 'Admin\BatchController@system_driven');
Route::post('batch-system-driven-save', 'Admin\BatchController@system_driven_save');
Route::post('/system-driven', 'HomeController@system_driven');
Route::post('/add-system-driven', 'HomeController@add_system_driven');
Route::post('/check-doctor-system-driven', 'HomeController@check_doctor_system_driven');
Route::post('/add-doctor-course-schedule-details', 'HomeController@add_doctor_course_schedule_details');
Route::post('/set-doctor-system-driven-feedback', 'HomeController@set_doctor_system_driven_feedback');

// combine list
Route::get('complain', 'ComplineController@index');
Route::post('submit-phone-number', 'ComplineController@submit_phone_number');
Route::post('password-submit-complain', 'ComplineController@password_submit'); 
Route::get('complain-related', 'ComplineController@complain_related');
Route::post('complain-related-topics', 'ComplineController@complain_related_topics');
Route::get('all-comment', 'ComplineController@all_comment');

// new
Route::get('complain-details-new/{id}', 'ComplineController@complain_details_new');
Route::post('complain-again-new', 'ComplineController@complain_again_new');
Route::get('view-reply', ['middleware' => 'auth:doctor', 'uses' => 'ComplineController@view_reply']);
Route::get('/password-send-complain', 'ComplineController@password_send_complain');
Route::post('/password-recovery-submit', 'ComplineController@password_recovery_complain');
Route::post('complain-submit', 'ComplineController@complain_submit');

Route::get('/counselling', 'CounsellingController@index');

Route::get('/v/{video}', 'FreeVideoController@show');

#request lecture video
Route::get('request-lecture-video', 'RequestLectureVideoController@index')->name('request-lecture-video.index');
Route::get('request-lecture-video/{doctor_course}/{id?}', 'RequestLectureVideoController@show')->name('request-lecture-video.show');
Route::post('request-lecture-video/{doctor_course}/request', 'RequestLectureVideoController@request')->name('request-lecture-video.request');
Route::post('request-lecture-video/{doctor_course}/{pending_video}/complete', 'RequestLectureVideoController@complete')->name('request-lecture-video.complete');

// Uploads Image Get Link
// Route::post('upload-image-get-link/{width?}/{height?}', 'Controller@uploadImageGetLink')->name('upload-image-get-link');

Auth::routes();

Route::post('/authentication-by-token', 'Controller@authenticationByAccessToken');

// v2 routes
Route::group(['prefix' => 'v2'], function () {
    Route::view('{any?}', 'v2.index')->where('any', '.*');
});