<?php


namespace App\Http\Traits;


use App\BatchesSchedules;
use App\History;

trait ManageHistory
{

    protected static function getTableModel( ){
        return self::class;
    }

    protected static function primaryKey( ){
        return (new static())->getKey();
    }


    public function array_walk( array $array, &$_array, &$array_relations ){
        array_walk($array, function ( $value, $key ) use ( &$_array, &$array_relations ){
            if( is_string($value ) || is_numeric($value ) ) {
                $_array[] = $value;
            }else {
                $array_relations[$key] = $value;
            }
        });
    }


    public function findDiff( $array1, $array2 ){

        $this->array_walk($array1, $_array1, $array1_relations );
        $this->array_walk($array2, $_array2, $array2_relations );

        dd($array1_relations, $array2_relations );
        dd($_array1, $_array2 );

    }


    public static function saveHistory( $old, $new, $id ){

        $b = BatchesSchedules::with('time_slots.schedule_details')->find(  690, ['name','tag_line','contact_details', 'id'] );
        $c = BatchesSchedules::with('time_slots.schedule_details')->find(  692, ['name','tag_line','contact_details', 'id'] );


        ( new static() )->findDiff( $b->toArray(), $c->toArray() );


        $history = new History( );
        $history->old_data = $old;
        $history->new_data = $new;
        $history->table_model = self::getTableModel();
        $history->table_id = $id;

        return $history;

        return $history->save( );
    }

}