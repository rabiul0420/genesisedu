<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Label;

class labelController extends Controller
{
    public function index()
    {
        $labels = Label::query()
            ->latest()
            ->paginate();

        return view('tailwind.admin.labels.index', compact('labels'));
    }

    public function save(Request $request, $label_id = null)
    {
        $request->validate([
            'name'      => 'required|max:255',
            'status'    => 'required|numeric',
        ]);

        $label = Label::updateOrCreate(
            [
                'id'        => $label_id,
            ],
            [
                'name'      => $request->name,
                'status'    => $request->status,
            ]
        );


        return $label_id
            ? back()
            : redirect()->route('labels.index');
    }

}
