<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class CounsellingController extends Controller
{
    public function index()
    {
        if(!Auth::guard('doctor')->check()){
            return view('counselling.give_mobile_number');
        }

        return view('counselling.index', [
            'images' => [
                'images/counselling.webp',
                'images/counselling3.jpg',
                'images/counselling2.jpg',
            ]
        ]);
    }
}
