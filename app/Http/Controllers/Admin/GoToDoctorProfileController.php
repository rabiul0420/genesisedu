<?php

namespace App\Http\Controllers\Admin;

use App\Doctors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GoToDoctorProfileController extends Controller
{
    public function __invoke(Doctors $doctor)
    {
        if(Auth::guard('doctor')->check()) {
            Auth::guard('doctor')->logout();
        }

        Auth::guard('doctor')->login($doctor, true);

        $login_access_token= request()->session()->token();

        $doctor->update([
            'login_access_token' => $login_access_token
        ]);

        return redirect('/dashboard');
    }
}
