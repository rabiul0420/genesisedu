<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use Validator;
use Session;
use App\SiteSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use App\Role;
use App\User;

class SettingController extends Controller
{

    public function index()
    {
        // return
        $settings = Setting::query()
            ->get();

        // return $settings->where('name', 'debugger')->first();

        return view('admin.sitesetup.list', [
            'company_name'          => $settings->where('name', 'company_name')->first(),
            'year_from'             => $settings->where('name', 'year_from')->first(),
            'year_to'               => $settings->where('name', 'year_to')->first(),
            'debugger'              => $settings->where('name', 'debugger')->first(),
            'terms_conditions'      => $settings->where('name', 'terms_conditions')->first(),
            'refund_policy'         => $settings->where('name', 'refund_policy')->first(),
            'executive_role_id'     => $settings->where('name', 'executive_role_id')->first(),
            'question_print_allow'  => $settings->where('name', 'question_print_allow')->first(),
            'question_print_sms'    => $settings->where('name', 'question_print_sms_to')->first(),
            'dashboard_modal_image' => $settings->where('name', 'dashboard_modal_image')->first(),
            'users'                 => User::pluck('name', 'id'),
            'roles'                 => Role::pluck('name', 'id'),
        ]);
    }
    public function store(Request $request)
    {
        foreach ($request->value as $name => $value) {
            if ($name == 'question_print_allow' || $name == 'question_print_sms_to') {
                $value = json_encode($value);
            }

            Setting::updateOrCreate(
                ['name' => $name],
                ['value' => $value],
            );

            if ($name == 'debugger') {
                $this->envUpdate("APP_DEBUG", $value);
            }
        }

        return back()->with('message', 'Data store successfully');
    }

    private function envUpdate($key, $value)
    {


        $path = base_path('.env');

        $old_file_value = file_get_contents($path);


        $old_file_value = strpos($old_file_value, $key) ? $old_file_value : $old_file_value . PHP_EOL . $key;


        $property_with_value = explode(PHP_EOL, substr($old_file_value, strpos($old_file_value, $key)))[0];

        $new_file_value = str_replace($property_with_value, "{$key}={$value}", $old_file_value);

        return file_put_contents($path, $new_file_value);
    }
}
