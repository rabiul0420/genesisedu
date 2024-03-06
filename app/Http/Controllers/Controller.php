<?php

namespace App\Http\Controllers;

use App\AddonContent;
use App\Doctors;
use App\Interfaces\RedisConstants;
use App\PendingVideo;
use App\Traits\RedisHelper;
use App\UploadImageLink;
use Exception;
use GuzzleHttp\Client;
use Session;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic;

class Controller extends BaseController implements RedisConstants
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getDeletedByColumn()
    {
        return 'deleted_by';
    }

    public function save_relation($modelClass, $deleteWhere, $relation_ids, $insert)
    {

        $model = app()->make($modelClass);

        $first = $model->withTrashed()->first();

        $hasDeletedBy = isset($first->deleted_by);

        $deleted_by_column = $this->getDeletedByColumn();

        if ($model->where($deleteWhere)->exists()) {

            if ($hasDeletedBy) {
                $model->where($deleteWhere)->update([$deleted_by_column => Auth::guard('doctor')->id()]);
            }

            $model->where($deleteWhere)->delete();
        }


        if ($relation_ids && is_array($relation_ids)) {

            $previous_values = $model->where($deleteWhere)->withTrashed()->get();

            $insertData = [];
            $updateWhere = [];
            $unique_col = null;

            foreach ($relation_ids as  $value) {

                $i_data = [];

                if (!empty($value)) {

                    foreach ($insert as $col => $item) {
                        if ($item == '@value@') {
                            $unique_col = $col;
                        }

                        $i_data[$col] = $item == '@value@' ? ($value ?? '') : $item;
                    }

                    if ($unique_col) {
                        $prev = $previous_values->where($unique_col, $value);

                        if ($prev->count()) {
                            $updateWhere[] =  $value;
                        } else {
                            $insertData[] = $i_data;
                        }
                    }
                }
            }

            $model->where($deleteWhere)->whereIn($unique_col, $updateWhere)->restore();
            $model->insert($insertData);
        }
    }

    protected function getSubscriptionPaymentLink()
    {
        return url('/manual-payments/subscription-orders') . "/%ORDER_ID%/%PAYABLE_ID%";

        // return env('SUBSCRIPTION_PAYMENT_LINK', "https://banglamedexam.com/sif-subscription-payment/%ORDER_ID%/%PAYABLE_ID%");
    }

    public function checkSubscriberAbility()
    {
        if(Auth::guard('doctor')->user()->subscriber) {
            return true;
        }

        if($this->checkAndSetSubscriberAbility()) {
            return true;
        }

        return false;
    }

    public function checkAndSetSubscriberAbility()
    {
        $subscriber_ability = Doctors::query()
            ->whereHas('doctorcourses.batch', function ($query) {
                $query->whereIn('batch_type', ['Regular', 'Exam']);
            })
            ->where('id', Auth::guard('doctor')->id())
            ->exists();

        if($subscriber_ability) {
            Doctors::query()
                ->where('id', Auth::guard('doctor')->id())
                ->update(['subscriber' => 1]);
        }

        return $subscriber_ability;
    }

    public function getDoctorAccessToken($put_into_session = true)
    {
        // if($put_into_session) {
        //     $token = Session::get('token');

        //     if($token) {
        //         return $token;
        //     }
        // }

        $base_url = env('API_BASE_URL');

        $endpoint = "{$base_url}/doctor/login";

        $headers = [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ];

        $doctor = Auth::guard('doctor')->user();

        $body = [
            "bmdc_no"   => $doctor->bmdc_no,
            "password"  => $doctor->main_password,
        ];

        $options = [
            'headers'   => $headers,
            'query'     => $body,
        ];

        $client = new Client();

        try {
            $response = $client->request('POST', $endpoint, $options);

            $body = json_decode($response->getBody());

            // if($put_into_session) {
            //     Session::put('token', $body->token);
            // }

            return $body->token;
        } 
        catch (\GuzzleHttp\Exception\ClientException $exception) {
            return $exception->getMessage();
        }
    }

    public function authenticationByAccessToken( $token = null )
    {
        $token = $token ?? request()->token;

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

            $user_id = $body->user->id ?? null;

            $session_token = request()->session()->token();

            $doctor = Doctors::query()
                ->where([
                    'status'    => 1,
                    'id'        => $user_id,
                ])->first();

            if($doctor) {

                $doctor->update([
                    'login_access_token' => $session_token,
                ]);

                Auth::guard('doctor')->login($doctor, 1);
            }

        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            return $exception->getMessage();
        }
    }

    public function formImageGetLink()
    {
        $upladed_image_links = UploadImageLink::latest()->paginate();

        return view('tailwind.admin.upload-image.form', compact('upladed_image_links'));
    }

    public function uploadImageGetLink($directory = 'uploads', $sotore = false)
    {
        $image = request()->file('upload');

        $extention = strtolower($image->getClientOriginalExtension());

        $fileName = Str::random(10) . Str::random(10) . '.' . $extention;

        $path = $directory . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';

        Storage::disk('s3')->put($path . $fileName, file_get_contents($image));

        $url = Storage::disk('s3')->url($path . $fileName);

        if($sotore && $url) {
            $upload_image_link = UploadImageLink::query()
                ->create([
                    'url' => $url,
                    'user_id' => Auth::id(),
                ]);
        }

        return response()->json([
            'url' => $url,
        ]);
    }

    public function sortData()
    {
        $data   = request()->data;

        $priority_column = request()->priority_column ?? 'priority';

        foreach($data as $row) {
    
            if(request()->model == 'addon-content') {
                $this->storePriority(AddonContent::query(), $row, $priority_column);
            }
    
            if(request()->model == 'pending-video') {
                $this->storePriority(PendingVideo::query(), $row, $priority_column);
            }
        }

        return response([
            'message' => 'Success',
        ], 200);
    }

    protected function storePriority($query, $row = [], $priority_column = 'priority')
    {
        if($query && is_array($row)) {
            $query->where("id", $row["id"])
                ->update([
                    $priority_column => $row["priority"]
                ]);
        }

        return $query;
    }

    protected function preOutput($mix)
    {
        return "<pre>" . print_r($mix, true) . "</pre>";
    }

    protected function useRedis($name, $callbak)
    {
        try {
            $data = Redis::get($name) ?? null;
        } catch (Exception $exception) {
            return $callbak();
        }

        if(!$data) {
            $data = $callbak();

            Redis::set($name, $data);
        }

        return $data;
    }
}
