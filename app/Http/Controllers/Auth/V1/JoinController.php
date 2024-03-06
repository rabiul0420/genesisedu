<?php

namespace App\Http\Controllers\Auth\V1;

use App\Doctors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class JoinController extends Controller
{
    public function create()
    {
        return view('tailwind.client.auth.v1.join');
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'phone'     => [
                'required',
                'string',
                'size:11',
            ],
            'password'  => [],
        ]);

        $phone = $fields["phone"] ?? "";

        $password = $fields["password"] ?? "";

        if($phone && $password) {
            $user = Doctors::query()
                ->where([
                    'mobile_number' => $phone,
                    'main_password' => $password,
                ])
                ->where('status', 1)
                ->first();

            $login = false;

            if($user) {
                if(Auth::guard('doctor')->check()) {
                    Auth::guard('doctor')->logout();
                }
        
                Auth::guard('doctor')->login($user, true);
        
                $login_access_token= request()->session()->token();
        
                $user->update([
                    'login_access_token' => $login_access_token
                ]);

                $login = true;
            }

            return response([
                "message"   => "success",
                'isLogin'   => $login,
                "user"      => $user ?? [],
            ], 200);
        }

        $user = Doctors::query()
            ->where('mobile_number', $phone)
            ->where('status', 1)
            ->first();

        if(!$user) {
            $user = Doctors::create([
                'mobile_number' => $phone,
            ]);
        }

        return response([
            "message"   => "success",
            "phone"     => $phone ?? "",
            "name"      => $user->name ?? "",
            "step"      => $user->name ? 2 : 3,
        ], 200);
    }
}
