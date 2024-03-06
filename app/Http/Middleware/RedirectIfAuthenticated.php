<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->checkAdminUrl($request) && Auth::guard('web')->check()){
            return redirect('admin');
        }

        if (!$this->checkAdminUrl($request) && Auth::guard('doctor')->check()) {
            return redirect('');
        }

        return $next($request);
    }

    private function checkAdminUrl($request) 
    {
        return (boolean) ((explode('/', $request->getRequestUri())[1] ?? '') === 'admin');
    }
}
