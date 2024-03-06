<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Menu;
use App\QuestionTypes;
use Auth;



use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard()
    {
        $title = 'GENESIS Admin : Dashboard';

        // return
        $menus = Menu::query()
            ->with('submenu.thirdmenu')
            ->mainMenu()
            ->get();

        return view('tailwind.admin.dashboard.index', compact('title', 'menus'));
    }
}
