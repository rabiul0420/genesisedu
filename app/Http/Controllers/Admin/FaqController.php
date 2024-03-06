<?php

namespace App\Http\Controllers\Admin;

use App\Faqs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Session;
use Auth;

class FaqController extends Controller
{
    public function index()
    {
    
        $data['faq_details'] = Faqs::get();
        $data['title'] = 'Genesis Admin : Faq';
        return view('admin.faqs.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data['module_name'] = 'Faq ';
        $data['title'] = 'Genesis Admin : Faq Create';
        return view('admin.faqs.create',$data);
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
            'description' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Enter Fields Correctly');
            return redirect()->action('Admin\FaqController@create')->withInput();
        }

        $faq_details = new Faqs;
        $faq_details->title = $request->title;
        $faq_details->description = $request->description;
        $faq_details->priority = $request->priority;
        $faq_details->status = $request->status;
        $faq_details->save();

        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\FaqController@index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['faq_details'] = Faqs::get();
        $data['title'] = 'Genesis Admin : Faq';
        return view('admin.faq.list',$data);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
   
        $data['faq_details'] = Faqs::find($id);
        $data['title'] = 'Genesis Admin : Faq Edit';
        return view('admin.faqs.edit',$data);
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
            'description' => ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\FaqController@update')->withInput();
        }

        $faq_details = Faqs::find($id);
        $faq_details->title = $request->title;
        $faq_details->description = $request->description;
        $faq_details->priority = $request->priority;
        $faq_details->status = $request->status;
        $faq_details->push();
        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\FaqController@index');

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
        // $batchs =AvailableBatches::find($id);
        // $batchs->deleted_by=Auth::id();
        // $batchs->push();
        
        if( Faqs::find( $id ) ) {
            Faqs::where( 'id', $id )->update( [ 'deleted_by' => Auth::id() ]);
            Faqs::where( 'id', $id )->delete( ); //1 way
        }

        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\FaqController@index');
    }
}
