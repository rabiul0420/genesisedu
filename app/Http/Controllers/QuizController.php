<?php

namespace App\Http\Controllers;

use App\Quiz;
use App\QuizParticipant;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = $quizzes = Quiz::query()
            ->with([
                'quiz_property',
                'quiz_questions:id,quiz_id',
                'quiz_participants' => function($query) {
                    $query
                        ->select([
                            'id',
                            'doctor_id',
                            'quiz_id',
                            'obtained_mark',
                            'coupon',
                        ])
                        ->whereNotNull('doctor_id')
                        ->where('doctor_id', Auth::guard('doctor')->id());
                }
            ])
            ->published()
            ->latest()
            ->paginate();

        $token = "";
        
        if(Auth::guard('doctor')->check()) {
            $token = $this->getDoctorAccessToken(false);
        }

        $base_url = env('API_BASE_URL');

        $url = "{$base_url}/v2/quizzes";

        return view('tailwind.client.on-spot-quiz.index', compact('quizzes', 'url', 'token'));
    }

    public function show($key)
    {
        return
        $quiz = Quiz::query()
            ->key($key)
            ->firstOrFail();

        if(!Auth::guard('doctor')->check()) {
            $base_url = env('API_BASE_URL');
            $join_url = "{$base_url}/doctor/join";
            $login_url = "{$base_url}/doctor/login";

            return view('tailwind.client.auth.join', compact('join_url', 'login_url'));
        }

        $token = $this->getDoctorAccessToken(false);

        $base_url = env('API_BASE_URL');

        $url = "{$base_url}/v2/doctor/on-spot-quiz/{$quiz->key}";

        return view('tailwind.client.on-spot-quiz.show', compact('token', 'url'));
    }
}
