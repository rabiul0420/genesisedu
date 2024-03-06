<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteSetup extends Model
{
    protected $table = 'site_setup';

    // public static function getItem( $name ){
    //     return self::where( 'name', $name )->value( 'value' );
    // }

    // public static function saveItem( $name, $value ){
    //     $setup =  self::where( 'name', $name );
    //     $setup = $setup->exists() ? $setup->first() : new SiteSetup();
    //     $setup->value = $value;
    //     return $setup->save( );
    // }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }
}
