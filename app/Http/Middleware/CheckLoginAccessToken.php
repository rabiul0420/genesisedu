<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckLoginAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $login_access_token = request()->session()->token();
        if(Auth::user()->login_access_token == $login_access_token){
            return $next($request);
        }
        
        Auth::logout();
        
        $request->session()->invalidate();

        session()->flash('login_access_token','Logout for another login');
        
        return redirect('/login');

        return $next($request);
    }
}
