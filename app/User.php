<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password','type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    private static $mentorTopics = null;

    function need_to_filter_question_topic(  &$topic_ids = null, &$subject_ids = null, &$chapter_ids = null ){
        if( $this->hasRole( 'Administrator' ) || $this->hasRole( 'Super Admin' ) ) {
            return false;
        } else if( $this->hasRole( [ 'Mentor' ] ) ) {

            if( self::$mentorTopics === null )
                self::$mentorTopics = $this->mentor_topics()->get( ['topic_id','subject_id','chapter_id'] );

            ///

            $topic_ids = self::$mentorTopics->where( 'topic_id', '!=', null )
                ->pluck( 'topic_id' );

            $chapter_ids = self::$mentorTopics->where( 'topic_id', null )
                ->where( 'chapter_id', '!=', null )
                ->pluck( 'chapter_id' );

            $subject_ids = self::$mentorTopics->where( 'topic_id', null )
                ->where( 'chapter_id', null )->pluck( 'subject_id' );

            ///

            return true;
        }
        return false;
    }

    function isMentor(){
        $userRoles = $this->roles()->pluck('name', 'name');

        if ( $userRoles->has( 'Administrator') || $userRoles->has( 'Super Admin') ) {
            return false;
        }

        return $userRoles->has( 'Mentor' );
    }

    function question(){
        $data = new \stdClass();
        $data->subjects = new QuestionSubject( );
        $data->chapters = new QuestionChapter( );
        $data->topics = new QuestionTopic( );

        if( $this->need_to_filter_question_topic( $topic_ids, $subject_ids, $chapter_ids ) ) {


    //        if( self::$mentorTopics === null )
    //            self::$mentorTopics = $this->mentor_topics()->get( ['topic_id','subject_id','chapter_id'] );

    //
    //        $topic_ids = self::$mentorTopics->where( 'topic_id', '!=', null )
    //            ->pluck( 'topic_id' );
    //
    //        $chapter_ids = self::$mentorTopics->where( 'topic_id', null )
    //            ->where( 'chapter_id', '!=', null )->pluck( 'chapter_id' );
    //
    //        $subject_ids = self::$mentorTopics->where( 'topic_id', null )
    //            ->where( 'chapter_id', null )->pluck( 'subject_id' );

            $data->topics = $this->questionTopic( $subject_ids, $chapter_ids, $topic_ids );
            $topic_subject = $this->questionTopic( $subject_ids, $chapter_ids, $topic_ids );
            $topic_chapter = $this->questionTopic( $subject_ids, $chapter_ids, $topic_ids );

            $subjectWhereIn = $topic_subject->groupBy( 'subject_id' )->select( 'subject_id' );
            $data->subjects = QuestionSubject::whereIn( 'id', $subjectWhereIn  );

            $topicWhereIn = $topic_chapter->groupBy( 'chapter_id' )->select(  'chapter_id' );
            $data->chapters = QuestionChapter::whereIn( 'id', $topicWhereIn );
        }

        return $data;
    }

    protected function questionTopic(  $subject_ids, $chapter_ids, $topic_ids ){
        return QuestionTopic::where( function( $topics ) use( $subject_ids, $chapter_ids, $topic_ids ){
            $topics->whereIn('id', $topic_ids );

            if( $subject_ids->count() > 0)
                $topics->orWhereIn('subject_id', $subject_ids );

            if( $chapter_ids->count() > 0)
                $topics->orWhereIn('chapter_id', $chapter_ids );
        });
    }

    function mentor_topics(){
        $mentor_access = MentorAccess::query()
            ->firstOrCreate(
                [
                    'mentor_id' => $this->id,
                ],
                [
                    'access_upto' => now(),
                ]
            );

        return $this->hasMany(MentorTopic::class, 'user_id', 'id' )
            ->when($mentor_access->access_upto < Carbon::make(date('Y-m-d')), function ($query) {
                $query->take(0);
            });
    }

    public function logs()
    {
        return $this->hasMany(LogHistory::class);
    }

    public function mentor_access()
    {
        return $this->hasOne(MentorAccess::class, 'mentor_id');
    }
}
