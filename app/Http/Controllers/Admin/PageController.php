<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Page;
use App\Sessions;
use Illuminate\Http\Request;
use App\Exam;
use App\Exam_question;
use App\Institutes;
use App\Courses;
use App\Faculty;
use App\Subjects;
use App\Batches;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;


class PageController extends Controller
{
    //
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      /*  if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['pages'] = Page::get();
        $data['module_name'] = 'Pages';
        $data['title'] = 'Page List';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.page.list',$data);
                
        //echo $Institutes;
        //echo $title;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // $user=Subjects::find(Auth::id());
        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $data['module_name'] = 'Pages';
        $data['title'] = 'Page Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';

        return view('admin.page.create',$data);
        //echo "Topic create";
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            /*'description' => ['required'],*/
            'status' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\PageController@create')->withInput();
        }

        if (Page::where('title',$request->title)->exists()){
            Session::flash('class', 'alert-danger');
            session()->flash('message','This title already exists');
            return redirect()->action('Admin\PageController@create')->withInput();
        }
        else{

            $page = new Page();
            $page->title = $request->title;
            $page->description = $request->description;
            $page->status=$request->status;
            $page->created_by=Auth::id();
            $page->save();

            Session::flash('message', 'Record has been added successfully');

            return redirect()->action('Admin\PageController@index');
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page=Page::select('pages.*')->find($id);
        return view('admin.page.show',['page'=>$page]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       /* $user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['page'] = Page::find($id);
        $data['module_name'] = 'Pages';
        $data['title'] = 'Page Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Update';
        return view('admin.page.edit', $data);
        
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            /*'description' => ['required'],*/
            'status' => ['required']
        ]);
        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', "Please enter valid Data");
            return back()->withInput();
        }

        $page = Page::find($id);

        if($page->title != $request->title) {

            if (Page::where('title', $request->title)->exists()) {
                Session::flash('class', 'alert-danger');
                session()->flash('message', 'This title already exists');
                return redirect()->action('Admin\PageController@edit', [$id])->withInput();
            }

        }
        
        $page->title = $request->title;
        $page->description = $request->description;
        $page->status=$request->status;
        $page->updated_by=Auth::id();
        $page->push();
        Session::flash('message', 'Record has been updated successfully');
        return back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$user=Subjects::find(Auth::id());
        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        Page::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\PageController@index');
    }
}