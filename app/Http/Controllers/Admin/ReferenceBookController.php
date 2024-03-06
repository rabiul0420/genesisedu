<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ReferenceBook;
use Session;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;

class ReferenceBookController extends Controller
{
    public function index()
    {
        // return
        $reference_books = ReferenceBook::query()
            ->latest()
            ->paginate();

        return view('tailwind.admin.reference-books.index', compact('reference_books'));
    }

    public function store(Request $request)
    {
        ReferenceBook::create($this->validatedData($request));

        return redirect()->route('reference-books.index')->with('message', 'Record has been added successfully');
    }

    public function show(ReferenceBook $reference_book)
    {
        $reference_book_pages = $reference_book->pages()->orderBy('page_no')->paginate(20);

        return view('tailwind.admin.reference-books.show', compact('reference_book', 'reference_book_pages'));
    }

    public function update(Request $request, ReferenceBook $reference_book)
    {
        $response = $reference_book->update($this->validatedData($request, $reference_book->id));

        return response([
            'status' => (bool) $response,
            'message' => (string) $response ? "Success" : "Try Again",
        ], 200);
    }

    public function validatedData($request, $id = '')
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('reference_books', 'name')->ignore($id),

            ],
            'total_pages' => [
                'required',
                'numeric'
            ]
        ]);
    }
}
