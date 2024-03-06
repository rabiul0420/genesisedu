<?php

namespace App\Http\Controllers\Admin;
use App\AvailableBatches;
use App\ExamFaculty;
use App\Http\Controllers\Controller;

use App\Http\Traits\ContentSelector;
use Illuminate\Http\Request;
use App\Institutes;
use App\Models\Moreinfo;
use Session;
use Auth;
use Validator;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Redis;


class AvailableBatchesController extends Controller
{
    use ContentSelector;
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Auth::loginUsingId(1);
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['available_batches'] = AvailableBatches::with('batch' )->get();
        $data['title'] = 'Genesis Admin : Available Batches';
        return view('admin.available_batches.list',$data);
    }

    public function available_batche_list(Request $request) {

        $year = (int) $request->year;
        $course_id = (int) $request->course_id;
        $session_id = (int) $request->session_id;
        $status = (string) $request->status;

        $available_batche_list = DB::table('available_batches as d1')
            ->leftjoin('batches as d2', 'd1.batch_id', '=','d2.id');

        if(in_array($status, ["0", "1"])){
            $available_batche_list 
                ->where('d1.status', $status);
        }

        if($year) {
            $available_batche_list = $available_batche_list->where('d2.year', $year);
        }

        if($course_id) {
            $available_batche_list = $available_batche_list->where('d2.course_id', $course_id);
        }

        if($session_id) {
            $available_batche_list = $available_batche_list->where('d2.session_id', $session_id);
        }

        $available_batche_list->select(
            'd1.id as id',
            'd1.course_name as course_name',
            'd1.batch_name as batch_name',
            'd2.name as main_batch_name',
            'd1.start_date as start_date',
            'd1.days as days',
            'd1.time as time',
            'd1.details as details',
            'd1.status as status',
        );

        $available_batche_list = $available_batche_list->whereNull('d1.deleted_at');
        
        return DataTables::of($available_batche_list)
            ->addColumn('action', function ($available_batche_list) {
                return view('admin.available_batches.available_batch_ajax_list',(['available_batche_list'=>$available_batche_list]));
            })
            
            ->addColumn('status',function($available_batche_list){
                return '<span style="color:' .( $available_batche_list->status == 1 ? 'green;':'red;' ).'">'
                        . ($available_batche_list->status == 1 ? 'Active':'Inactive') . '</span>';
            })
            ->rawColumns(['action','status',])

        ->make(true);
    }


    protected function selection_config( )
    {
        return [
            'institutes' => [
                'label_column_count' => 2
            ],
            'courses' => [
                'label_column_count' => 2
            ],
            'sessions' => [
                'label_column_count' => 2
            ],
            'batches' => [
                'label_column_count' => 2
            ],
        ];
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( )
    {
        //
        $data[ 'years' ] = $this->years( );
        $data[ 'institutes_view' ] = $this->institutes( request( ) )->render( );
        $data[ 'courses_view' ] = $this->courses( request( ) )->render( );
        $data[ 'sessions_view' ] = $this->sessions( request( ) )->render( );
        $data[ 'batches_view' ] = $this->batches( request( ) )->render( );

        $this->setLinksInData( $data );

        $data[ 'module_name' ] = 'Available ';
        $data[ 'title' ] = 'Genesis Admin : Available Batches Create';

        // $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        // $data['submit_value'] = 'Submit';

        return view('admin.available_batches.create',$data);
    }

    public function setLinks( Request $request, &$available_batches ){
        
        if( is_array( $request->links ) && !empty($request->links) ) {

            $link_output = [];

            foreach( $request->links as $link ) {
                $link_contents = [];


                foreach( (array) ($link[ 'link_contents' ] ?? []) as $content ) {
                    $link_contents[] = [
                        'title' => $content['title'] ?? '',
                        'url' => $content['url'] ?? ''
                    ];
                }

                $link_output[] = [
                    'headline' => $link[ 'headline' ] ?? '',
                    'link_contents' => $link_contents
                ];
            }



            $available_batches->links = json_encode( $link_output ); 
        }else {
            $available_batches->links = null;
        }
         
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
            'course_name' => ['required'],
            'course_type' => ['required'],
            'batch_name' => ['required'],
            'start_date' => ['required'],
            'days' => ['required'],
            'time' => ['required'],
            'details' => ['required'],
            'batch_id' => ['required']
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Enter Fields Correctly');
            return redirect()->action('Admin\AvailableBatchesController@create')->withInput();
        }

        $available_batches = new AvailableBatches;

        $available_batches->course_name = $request->course_name;
        $available_batches->course_type = $request->course_type;
        $available_batches->batch_name  = $request->batch_name;
        $available_batches->start_date  = $request->start_date;
        $available_batches->days        = $request->days;
        $available_batches->time        = $request->time;
        $available_batches->details     = $request->details;
        $available_batches->status      = $request->status;
        $available_batches->batch_id    = $request->batch_id;
        $available_batches->meta_banner = $request->meta_banner ?? null;

        $this->setLinks( $request, $available_batches );

        $available_batches->save();

        Redis::del('Home_Page_AvailableBatches');

        Session::flash( 'message', 'Record has been added successfully' );

        return redirect()->action('Admin\AvailableBatchesController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $available_batches=AvailableBatches::select('service_packages.*')
        //     ->find($id);
        // return view('admin.available_batches.show',['available_batches'=>$service_package]);
        $data['available_batches'] = AvailableBatches::get();
        $data['title'] = 'Genesis Admin : Available Batches';
        // $data['module_name'] = 'Coming By';
        // $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        return view('admin.available_batches.list',$data);
    }



    protected function setLinksInData( &$data, $available_batches = null ){

        $links = old( 'links', $available_batches->links ?? '' );

        $data['links_array'] = is_array( $links )
            ? $links
            : (json_decode( $links ?? '', true ) ?? [ 'headline' => '', 'link_contents' =>[ [ 'title' => '', 'url' => '' ]]]);



    }


    /**
     * @param $id
     * @return mixed
     * @throws \Throwable
     */
    protected function editing_data($id)
    {

        $available_batches = AvailableBatches::with('batch'  )->find($id);

        $data[ 'available_batches' ] = $available_batches;

        $this->setLinksInData( $data, $available_batches );

        $data[ 'years' ] = $this->years( );
        $data[ 'title' ] = 'Genesis Admin : Available Batches Edit';

        $data[ 'institutes_view' ] = $this->institutes( request( ),
            $available_batches->batch->institute_id ?? null
        )->render( );

        $data[ 'courses_view' ] = $this->courses( request( ),
            $available_batches->batch->course_id ?? null,
            $available_batches->batch->institute_id ?? null
        )->render( );

        $data[ 'sessions_view' ] = $this->sessions( request( ),
            $available_batches->batch->session_id ?? null,
            $available_batches->batch->course_id ?? null,
            $available_batches->batch->year ?? null )->render( );

        $data[ 'batches_view' ] = $this->batches( request( ),
            $available_batches->batch->id ?? null, [
                'year' => $available_batches->batch->year ?? null,
                'institute_id' => $available_batches->batch->institute_id ?? null,
                'course_id' => $available_batches->batch->course_id ?? null,
                'session_id' => $available_batches->batch->session_id ?? null,
            ])->render( );

        return $data;

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data = $this->editing_data($id);


        return view( 'admin.available_batches.edit', $data );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id)
    {

        $data = $this->editing_data($id);
        $data[ 'action'] = 'duplicate';
        $data[ 'available_batches' ]->days = '';
        $data[ 'available_batches' ]->time = '';


        return view( 'admin.available_batches.edit', $data );
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

        // return $request;

        $validator = Validator::make($request->all(), [
            'course_name' => ['required'],
            'course_type' => ['required'],
            'batch_name' => ['required'],
            'start_date' => ['required'],
            'days' => ['required'],
            'time' => ['required'],
            'details' => ['required'],
            'status' => ['required'],
            'batch_id' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Enter Fields Correctly');
            return back()->withInput();
        }

        $available_batches = AvailableBatches::find( $id );

        $available_batches->course_name = $request->course_name;
        // dd($available_batch);
        $available_batches->course_type = $request->course_type;
        $available_batches->batch_name = $request->batch_name;
        $available_batches->start_date = $request->start_date;
        $available_batches->days = $request->days;
        $available_batches->time = $request->time;
        $available_batches->details = $request->details;
        $available_batches->status = $request->status;
        $available_batches->batch_id = $request->batch_id;
        $available_batches->meta_banner = $request->meta_banner ?? null;
        
        $this->setLinks( $request, $available_batches );
        
        $available_batches->push();

        Redis::del('Home_Page_AvailableBatches');

        Session::flash('message', 'Record has been updated successfully');

        return back();

        return redirect()->action('Admin\AvailableBatchesController@index');

    }

    public function available_batches_trash()
    {
        $data =  AvailableBatches::onlyTrashed()->orderBy('deleted_at', 'asc')->get();
        return view('admin.available_batches.available-batches-trash', ['data'=>$data , 'trash'=> true]);
    }

    public function available_batches_restore($id)
    {
        AvailableBatches::withTrashed()->where('id', $id)->restore();
        return redirect()->action('Admin\AvailableBatchesController@index')->withInput();
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$user=Institutes::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        $batchs =AvailableBatches::find($id);
        $batchs->deleted_by=Auth::id();
        $batchs->push();
        
        if( AvailableBatches::find( $id ) ) {
            AvailableBatches::where( 'id', $id )->update( [ 'deleted_by' => Auth::id() ]);
            AvailableBatches::where( 'id', $id )->delete( ); //1 way
        }

        // $available_batches = collect(json_decode (Redis::get('Home_Page_AvailableBatches')));
        // $available_batch   = $available_batches->where('id',$id)->first();
        // $index             = $available_batches->search($available_batch);
        // $available_batch->forget($index);
      
        // Redis::set('Home_Page_AvailableBatches', json_encode($available_batches, TRUE));
        
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\AvailableBatchesController@index');
    }





}
