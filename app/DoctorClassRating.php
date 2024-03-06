<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DoctorClassRating extends Model
{
    //


    public $timestamps = false;
    protected $primaryKey = ['doctor_id', 'details_id', 'criteria'];
    public $incrementing = false;


    const PROGRESS = [ '', 'Average', 'Good', 'Very Good' ,'Excellent' ]; //1=Average, 2=Good, 3=Very Good , 4=Excellent
    const PROGRESSES = [ '', 'Average', 'Good', 'Very Good' ,'Excellent' ]; //1=Average, 2=Good, 3=Very Good , 4=Excellent
    const CLASS_CRITERIA_LIST = [ 'Introduction', 'Knowledge depth of the Mentor', 'Expression Capability', 'Interaction', 'Video Quality', 'Overall' ];
    const SOLVE_CLASS_CRITERIA_LIST = [ 'Introduction', 'Knowledge depth of the Mentor', 'Expression Capability', 'Interaction', 'Video Quality', 'Overall' ];
    const VIDEO_PROGRESSES = [ '', 'Smooth', 'Little bit disturb', 'disturb' ]; //1=Average, 2=Good, 3=Very Good , 4=Excellent
    const VIDEO_QUALITY_CRITERIA_LIST = [ 'Projector Support' , 'Sound System' ];

    function scopeSolveClassRatings( $query ){
        return $query->whereIn( 'criteria', self::SOLVE_CLASS_CRITERIA_LIST );
    }

    function scopeClassRatings( $query ){
        return $query->whereIn( 'criteria', self::CLASS_CRITERIA_LIST );
    }

    function scopeVideoQualityRatings( $query ){
        return $query->whereIn( 'criteria', self::VIDEO_QUALITY_CRITERIA_LIST );
    }

    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName( );

        if( !is_array( $keys ) ){
            return parent::setKeysForSaveQuery($query);
        }

        foreach( $keys as $keyName ){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }


    public function getFeedback( ){
        return self::PROGRESSES[ (int) $this->progress ] ?? '';
    }


}
