<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    public static $latters = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
    public static $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26];
    const STATUS_ARRAY = [
        0 => 'Unpublish',
        1 => 'Publish',
        2 => 'Closed',
    ];

    protected $guarded = [];

    protected $appends = [
        'key'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function getKeyAttribute()
    {
        $value = $this->id;

        $array = Array();

        while($value > 0) {
            $latter = self::$latters[($value - 1) % 26]; // find latter
            $array[] = ($value % 2) ? strtoupper($latter) : strtolower($latter); // Lowercase & Uppercase mix
            $value = floor($value / 26); // new value
        }

        $key = implode("", array_reverse($array));

        return $key;
    }

    public function scopeKey($query, $key)
    {
        $array = array_reverse(str_split(strtolower($key)));

        // dd($array);
        $id = 0;

        foreach($array as $index => $latter) {
            dd(in_array($latter, self::$latters) && $number = str_replace(self::$latters, self::$numbers, $latter));
            if(in_array($latter, self::$latters) && $number = str_replace(self::$latters, self::$numbers, $latter)) {
                $id += (int) ($number * (26 ** $index));
            }
        }

        return $query->where('id', $id);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    public function quiz_property()
    {
        return $this->belongsTo(QuizProperty::class);
    }

    public function quiz_questions()
    {
        return $this->hasMany(QuizQuestion::class)
            ->orderBy('question_type')
            ->orderBy('serial');
    }

    public function quiz_participants()
    {
        return $this->hasMany(QuizParticipant::class);
    }
}
