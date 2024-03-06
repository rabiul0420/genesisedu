<?php

namespace App\Http\Controllers\Admin;

use App\Format;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FormatController extends Controller
{
    public function index()
    {
        if(!request()->isXmlHttpRequest()) {
            return view('tailwind.admin.formats.index');
        }

        Format::$raw_dynamic_value = true;

        $query = Format::query();

        $this->search($query);
        $this->filter($query);

        // return
        $formats = $query->latest()->paginate(20);

        return view('tailwind.admin.formats.data', compact('formats'))
            ->render();
    }

    protected function search(&$query)
    {
        $query
            ->when(request('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query
                        ->where('id', 'like', "%{$search}%")
                        ->orWhere('property', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%");
                });
            });
    }

    protected function filter(&$query)
    {
        $query
            ->when(isset(request()->type), function ($query) {
                return $query->where('type', request('type'));
            });
    }

    public function update(Request $request, Format $format)
    {
        Format::$raw_dynamic_value = true;

        if($format->body != $request->body) {
            $histories = $format->histories ?? [];

            array_push($histories, [
                "user_id"       => Auth::id(),
                "updated_at"    => date("d M Y h:i A"),
                "body"          => $request->body,
            ]);

            $format->update([
                "body"      => $request->body,
                "histories" => $histories
            ]);
        }

        return back();
    }
}
