<?php


namespace App\Http\Helpers;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;

class ContentRoute extends Route 
{
    public static function set( $resource_name, $controller ){

        self::group( [ 'prefix' => $resource_name ], function() use ( $controller, $resource_name ){
            foreach ( [ 'institutes', 'courses', 'sessions', 'batches', 'faculties', 'residency_disciplines', 'bcps_disciplines' ] as $content ) {
                Route::get( $content, $controller  . '@' . $content );
            }
        });
    }

}