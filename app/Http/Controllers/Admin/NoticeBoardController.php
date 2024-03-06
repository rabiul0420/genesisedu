<?php

namespace App\Http\Controllers\Admin;
use App\NoticeBoard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoticeBoardController extends Controller
{
    public function index()
    {
        return view('admin.notice-board.index',[
            'noticeBoards' => NoticeBoard::latest()->get()
        ]);
    }

    public function create()
    {
        return view('admin.notice-board.create',[
            'noticeBoard' => new NoticeBoard()
        ]);
    }

    public function store(Request $request)
    {
        $noticeBoard = NoticeBoard::create($this->checkValidatedData(request()));
        return redirect()
            ->route('notice-board.show', $noticeBoard->id)
            ->with('message', 'The record has been successfully added.');
    }

    public function show(NoticeBoard $noticeBoard)
    {
        return view('admin.notice-board.show',[
            'noticeBoard' => $noticeBoard
        ]);
    }

    public function edit(NoticeBoard $noticeBoard)
    {
        return view('admin.notice-board.edit',[
            'noticeBoard' => $noticeBoard
        ]);
    }

    public function update(Request $request, NoticeBoard $noticeBoard)
    {
        $noticeBoard->update($this->checkValidatedData(request()));
        return redirect()
            ->route('notice-board.show', $noticeBoard->id)
            ->with('message', 'The record has been successfully updated.');
    }

    public function destroy(NoticeBoard $noticeBoard)
    {
    
        NoticeBoard::destroy($noticeBoard->id);

        return redirect()
            ->route('notice-board.index')
            ->with('message', 'The record has been successfully deleted.');
    }

    private function checkValidatedData($validatedData)
    {
        return $validatedData->validate([
            'title' => 'bail|required|max:100',
            'description' => 'bail|required',
            'status' => 'bail|required|numeric',
        ]);
    }
}
