<?php

namespace App\Http\Controllers\Admin;

use App\CourseYear;
use App\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MentorAccess;
use App\MentorTopic;
use App\QuestionChapter;
use App\QuestionSubject;
use App\QuestionTopic;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class MentorController extends Controller
{
    public function index()
    {
        // $mentors = Role::where('id', 20)->get();
        $mentors = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('id', 20);
            })
            ->get();

        return view('admin.mentors.list', ['mentors' => $mentors]);
    }

    public function edit($id)
    {
        $user = User::find( $id );
        
        // return
        $mentor_topic = $user->mentor_topics( )->get( ['subject_id', 'chapter_id'] )->unique();
        $mentor_subjects_ids =$mentor_topic->pluck('subject_id' )->unique();
        $mentor_chapter_ids =$mentor_topic->pluck('chapter_id' )->unique();


        $roles = Role::get()->pluck('name', 'name');
        $userRoles = $user->roles()->pluck( 'name', 'name');
        $mentorTopic = MentorTopic::where([ 'user_id' => $id ])->get([ 'topic_id', 'subject_id', 'chapter_id', 'access_upto' ]);

        // return
        $mentor_access = MentorAccess::query()
            ->firstOrCreate(
                [
                    'mentor_id' => $id,
                ],
                [
                    'access_upto' => now(),
                ]
            );

        $access_upto = $mentor_access->access_upto ?? '';
        $access_upto = $access_upto ? $access_upto->format('Y-m-d') : '';

        $selected_exam_ids = $mentor_access->exam_ids ?? [];

        $years = CourseYear::query()
            ->where('status', 1)
            ->pluck('year');

        // return
        $exams = Exam::query()
            ->whereIn('year', $years)
            ->select('id', DB::raw('CONCAT(id, " | ", name) as name'))
            ->pluck('name', 'id');

        $selected_topics = $mentorTopic->pluck( 'topic_id' );
        $selected_subjects = $mentorTopic->pluck( 'subject_id' )->unique( );
        $selected_chapters = $mentorTopic->pluck( 'chapter_id' )->unique( );

        $question_subjects = QuestionSubject::pluck( 'subject_name', 'id' );

        $chapters = QuestionChapter::whereIn( 'subject_id', $selected_subjects )->get(['chapter_name', 'id']);

        $question_chapters = $chapters->pluck( 'chapter_name', 'id' );

        $question_topics = QuestionTopic::whereIn( 'chapter_id', $chapters->pluck( 'id' ) )->pluck( 'topic_name', 'id' );

        $title = 'GENESIS Admin : Mentor Edit';

        return view('admin.mentors.edit',
            compact([
                'user',
                'question_topics',
                'question_subjects',
                'question_chapters',
                'title',
                'roles',
                'selected_topics',
                'selected_subjects',
                'selected_chapters',
                'access_upto',
                'selected_exam_ids',
                'exams',
            ]));
    }

    public function update(Request $request, $id)
    {
        $mentor_access = MentorAccess::query()
            ->firstOrCreate(
                [
                    'mentor_id' => $id,
                ],
                [
                    'access_upto' => now(),
                ]
            );

        $mentor_access->update([
            "access_upto"   => $request->access_upto,
            "exam_ids"      => $request->exam_ids,
        ]);

        $user = User::find($id);

        MentorTopic::where( [ 'user_id' => $user->id ] )->update( [ 'deleted_by' => Auth::id() ]);
        MentorTopic::where( [ 'user_id' => $user->id ] )->delete();

        $mentorInsertData = [];

        if( empty( $request->subject_id ) === false && is_array( $request->subject_id ) ) {

            $subject_ids = QuestionSubject::whereIn( 'id', $request->subject_id );

            if( is_array( $request->topic_id ) && empty( $request->topic_id ) === false ) {
                $subject_ids = $subject_ids->whereNotIn( 'id', QuestionTopic::select( 'subject_id' )->whereIn( 'id', $request->topic_id ) );
            }

            if( is_array( $request->chapter_id ) && empty( $request->chapter_id ) === false ) {
                $subject_ids = $subject_ids->whereNotIn( 'id', QuestionChapter::select( 'subject_id' )->whereIn( 'id', $request->chapter_id ) );
            }

            $subject_ids = $subject_ids->pluck( 'id' );

            foreach ( $subject_ids as $subject_id ) {
                $data = [ 'subject_id' => $subject_id, 'chapter_id' => null, 'topic_id' => null, 'user_id' => $user->id ];
                $this->updateMentorTopic($data, $mentorInsertData);
            }
        }

        if( empty( $request->chapter_id ) === false && is_array( $request->chapter_id ) ) {

            $chapters = QuestionChapter::whereIn( 'id', $request->chapter_id );

            if( is_array( $request->topic_id ) && empty( $request->topic_id ) === false ) {
                $chapters = $chapters->whereNotIn( 'id', QuestionTopic::select( 'chapter_id' )->whereIn( 'id', $request->topic_id ) );
            }

            $chapters = $chapters->get( [ 'id', 'subject_id' ] );

            foreach ( $chapters as $chapter ) {
                $data = [ 'subject_id' => $chapter->subject_id, 'chapter_id' => $chapter->id, 'topic_id' => null, 'user_id' => $user->id ];
                $this->updateMentorTopic($data, $mentorInsertData);
            }
        }

        if( empty($request->topic_id) === false && is_array( $request->topic_id ) ) {


            foreach ( $request->topic_id as $topic_id ) {

                $questTopic = QuestionTopic::find( $topic_id );
                $data = [ 'topic_id' => $topic_id, 'user_id' => $user->id, 'subject_id' => $questTopic->subject_id, 'chapter_id' => $questTopic->chapter_id ];


                $this->updateMentorTopic( $data, $mentorInsertData );
            }
        }

        if( count($mentorInsertData) > 0 ) {
            MentorTopic::insert( $mentorInsertData );
        }

        Session::flash('message', 'Record has been updated successfully');

        return back();

    }

    function updateMentorTopic( $data, &$mentorInsertData = [] ){

        $mentor = MentorTopic::withTrashed()->where( $data );

        if( $mentor->exists() ) {
            $mentor = $mentor->first( );
            $mentor->deleted_at = NULL;
            $mentor->deleted_by = NULL;
            $mentor->push();
        } else {
            $mentorInsertData[] = $data;
        }

    }
}
