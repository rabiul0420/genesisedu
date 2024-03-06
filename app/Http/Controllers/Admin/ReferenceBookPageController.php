<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ReferenceBook;
use Session;
use Illuminate\Validation\Rule;
use App\ReferenceBookPage;

class ReferenceBookPageController extends Controller
{
    public function store(Request $request, ReferenceBook $reference_book)
    {
        $data = ReferenceBookPage::where(['page_no' => $request->page_no, 'reference_book_id' => $reference_book->id])->get()->toArray();

        if (empty($data)) {

            $reference_book->pages()->create($this->validatedData($request));

            return redirect()->back()->with('message', 'Record has been added successfully');
        } else {
            return redirect()->back()->with('message', 'Data is alreday exist!');
        }
    }

    public function update(Request $request, ReferenceBookPage $reference_book_page)
    {
        $reference_book_page->update($this->validatedData($request, $reference_book_page->id));

        return response([
            'message' => 'Successfully Updated.',
            'referenceBookPage' => $reference_book_page,
        ]);
    }

    public function validatedData($request, $id = '')
    {
        return $request->validate([
            'page_no' => [
                'required',
                'numeric',
            ],
            'body' => [
                'required'
            ]

        ]);
    }
}
