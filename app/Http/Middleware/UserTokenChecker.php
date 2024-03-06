<?php

namespace App\Http\Middleware;

use App\Doctors;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class UserTokenChecker
{

    const TOKEN_NAME = '__shfusyyucascsyduyfd';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( Auth::guard('web')->check() ) {
            return $next($request);
        }

        if( Auth::guard('doctor')->check() ) {
            $token = session(self::TOKEN_NAME );

            if(!$token || Auth::guard('doctor')->user()->login_access_token != $token) {
                Auth::guard('doctor' )->logout();
            }

            return $next($request);
        }

        $token = session(self::TOKEN_NAME );

        if( $token ) {
            $this->authenticationByAccessToken( $token );
        }

        return $next( $request );

    }

    protected function authenticationByAccessToken( $token )
    {
        $base_url = env('API_BASE_URL');

        $endpoint = "{$base_url}/doctor";

        $headers = [
            'Content-Type'      => 'application/json',
            'Accept'            => 'application/json',
            'Authorization'     => 'Bearer ' . $token
        ];


        $options = [
            'headers'   => $headers,
        ];

        $client = new Client();

        try {
            $response = $client->request('GET', $endpoint, $options);

            $body = json_decode($response->getBody());

            $doctor_id = $body->user->id ?? null;

            $this->loginOrForgetToken( $doctor_id , $token );

//            if( $doctor_id === null ) {
//                session()->forget( self::TOKEN_NAME );
//                return;
//            }
//
//            Auth::guard('doctor')->loginUsingId($doctor_id);
        }
        catch (\GuzzleHttp\Exception\ClientException $exception) {
            return $exception->getMessage();
        }
    }

    public function loginOrForgetToken( $doctor_id, $token ){
        if( $doctor = Doctors::where('status', 1)->find($doctor_id) ) {
            $doctor->login_access_token = $token;
            $doctor->save();
            Auth::guard('doctor')->login($doctor);
            return;
        }
        session()->forget( self::TOKEN_NAME );
    }

}
