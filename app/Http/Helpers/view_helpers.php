<?php


use Illuminate\Support\Collection;

function highlight_filter_text($main_text, $sub_text ){

    $sub_text = trim(urldecode( $sub_text ));

    if( $sub_text ) {
        $start =  stripos( $main_text, $sub_text );

        if( $start === false )
            return $main_text;

        $sub_txt = substr( $main_text, $start, strlen($sub_text) );

        return str_replace($sub_txt, "<mark>" . $sub_txt . "</mark>", $main_text );
    }

    return $main_text;
}

function calculate_percent( $partial, $total, $decimals = 2, $decimal_separator = '.'  ){
    if( $total >  0 ){
        return get_percent( $partial / $total , $decimals, $decimal_separator );
    }
    return  get_percent(0.00, $decimals, $decimal_separator );
}

function get_percent( $float_number, $decimals = 2, $decimal_separator = '.' ){
    return number_format($float_number * 100, $decimals, $decimal_separator, '' );
}


