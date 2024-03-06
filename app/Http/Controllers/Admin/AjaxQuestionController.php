<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\QuestionTopic;
use App\QuestionChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;

class AjaxQuestionController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //$this->middleware('auth');
    }


    public function ajax_question_chapter(Request $request)
    {

        //dd( 'DDT' );

        $view = $request->multiple == 'true' ? 'admin.ajax_questions.chapters_multiple' : 'admin.ajax_questions.chapters';

        $id = $request->subject_id;
        $chapters = new QuestionChapter();
        $chapters = Auth::user( )->question( )->chapters;

        if( is_array( $id ) ) {
            $chapters = $chapters->whereIn( 'subject_id', $id );
        } else {
            $chapters =$chapters->where( 'subject_id', $id );
        }



        $chapters = $chapters->orderBy('chapter_name','asc')->pluck('chapter_name','id');


        return json_encode(array('chapters' => view( $view, [ 'chapters' => $chapters ])->render(),), JSON_FORCE_OBJECT);
    }

    public function ajax_question_topic(Request $request)
    {
        $view = $request->multiple == 'true' ? 'admin.ajax_questions.topics_multiple' : 'admin.ajax_questions.topics';

        $subject_id = $request->subject_id;
        $chapter_id = $request->chapter_id;
        $topics = new QuestionTopic();
        $topics = Auth::user( )->question( )->topics;

        if( is_array( $subject_id ) ) {
            $topics = $topics->whereIn( 'subject_id', $subject_id );
        }else {
            $topics = $topics->where( 'subject_id', $subject_id );
        }

        if( is_array( $chapter_id ) ) {
            $topics = $topics->whereIn( 'chapter_id', $chapter_id );
        } else {
            $topics = $topics->where( 'chapter_id', $chapter_id );
        }

//        if( Auth::user()->need_to_filter_question_topic( $topicIds ) ) {
//            $subjectIds = QuestionTopic::where( 'id', $topicIds )->pluck( 'subject_id' );
//            $topics->whereIn( 'id', $subjectIds );
//        }

        $topics = $topics->orderBy('topic_name','asc')->pluck('topic_name','id');

//        dd( $topics );

        return json_encode(array('topics' => view( $view, ['topics' => $topics])->render(),), JSON_FORCE_OBJECT);
    }

}
