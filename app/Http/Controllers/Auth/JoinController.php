<?php

namespace App\Http\Controllers\Auth;

use App\Doctors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SendSms;
use Illuminate\Support\Facades\Auth;

class JoinController extends Controller
{
    use SendSms;

    public function create()
    {
        $base_url = env('API_BASE_URL');

        $join_url = "{$base_url}/doctor/join";

        $login_url = "{$base_url}/doctor/login";

        return view('tailwind.client.auth.join', compact('join_url', 'login_url'));
    }
}
