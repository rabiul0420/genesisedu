<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\InstituteAllocation;
use App\InstituteDiscipline;
use App\InstituteDisciplinesAllocationInstitutes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Action;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InstituteDisciplineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.institute-disciplines.index', [
            'instituteDisciplines' => InstituteDiscipline::orderBy('name', 'asc')->get()
        ]);
    }

    private function storeAllocationInstituteIds( $discipline_id, $institute_ids ){
        InstituteDisciplinesAllocationInstitutes::where( [ 'discipline_id' => $discipline_id ] )
            ->whereNotIn( 'institute_id', $institute_ids )
            ->update( [ 'deleted_at' => carbon::now() , 'deleted_by' => Auth::id() ] );


        if( is_array( $institute_ids ) ) {

//            $data = [ ];
//            foreach ( $course_ids as $course_id ) {
//                if( !InstituteAllocationCourses::where( [ 'allocation_id' => $id, 'course_id' => $course_id ] )->exists( ) ) {
//                    $data[] = [ 'allocation_id' => $id, 'course_id' => $course_id ];
//                }
//            }
//            InstituteAllocationCourses::insert( $data );

            foreach ( $institute_ids as $institute_id ) {
                InstituteDisciplinesAllocationInstitutes::withTrashed()
                    ->UpdateOrCreate(
                        [ 'discipline_id' => $discipline_id, 'institute_id' => $institute_id  ],
                        [ 'deleted_at' => null, 'deleted_by' => NULL ]
                    );

            }

        }
    }

    public function create()
    {

        return view('admin.institute-disciplines.create', [
            'instituteDiscipline' => new InstituteDiscipline(),
            'allocation_institutes' => $this->get_allocation_institutes(),
            'selected_allocation_institutes' => []
        ]);
    }

    private function get_allocation_institutes( ){
        return InstituteAllocation::pluck( 'name', 'id' );
    }

    public function store(Request $request)
    {
        $instituteDiscipline = InstituteDiscipline::create($this->checkValidatedData(request(), null, $allocation_institute_ids ));

        $this->storeAllocationInstituteIds( $instituteDiscipline->id, $allocation_institute_ids );

        return redirect()
            ->route('institute-disciplines.show', $request->id)
            ->with('message', 'The record has been successfully added.');
    }

    public function show(InstituteDiscipline $instituteDiscipline)
    {
        return view('admin.institute-disciplines.show', [
            'instituteDiscipline' => $instituteDiscipline
        ]);
    }

    public function edit(InstituteDiscipline $instituteDiscipline)
    {
        return view('admin.institute-disciplines.edit', [
            'instituteDiscipline' => $instituteDiscipline,
            'allocation_institutes' => $this->get_allocation_institutes(),
            'selected_allocation_institutes' =>
                InstituteDisciplinesAllocationInstitutes::where( 'discipline_id', $instituteDiscipline->id)->pluck( 'institute_id' )
        ]);
    }

    public function update(Request $request, InstituteDiscipline $instituteDiscipline)
    {
        $instituteDiscipline->update($this->checkValidatedData(request(), $instituteDiscipline->id, $allocation_institute_ids));

        $this->storeAllocationInstituteIds( $instituteDiscipline->id, $allocation_institute_ids );

        return redirect()->route('institute-disciplines.show', $instituteDiscipline->id)
            ->with('message', 'The record has been successfully updated.');
    }

    public function destroy(InstituteDiscipline $instituteDiscipline)
    {
        $instituteDiscipline->delete();
        return redirect()
            ->route('institute-disciplines.index')
            ->with('message', 'The record has been successfully deleted.');
    }
    
    private function checkValidatedData($validatedData, $id = '', &$allocation_institute_ids = [] )
    {
        $data = $validatedData->validate([
            'name' => [
                'required',
                'max:250',
                Rule::unique('institute_disciplines', 'name')->ignore($id)
            ],
            'allocation_institute_ids' => 'required|array'
        ]);

        $allocation_institute_ids = $data['allocation_institute_ids'];
        return Arr::except( $data, 'allocation_institute_ids' );
    }
}
