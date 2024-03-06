<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\InstituteAllocation;
use App\InstituteAllocationCourses;
use App\Institutes;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class InstituteAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.institute-allocations.index', [
            'instituteAllocations' => InstituteAllocation::orderBy('name', 'asc')->get()
        ]);
    }

    private function get_courses(){
        return Courses::where([ 'status' => 1, 'institute_id' => 6 ])->pluck( 'name', 'id' );
    }

    public function create()
    {

        return view('admin.institute-allocations.create', [
            'instituteAllocation' => new InstituteAllocation( ),
            'courses' => $this->get_courses( ),
            'selected_courses' => []
        ]);
    }

    private function storeCourseIds( $id, $course_ids ){
        InstituteAllocationCourses::where( [ 'allocation_id' => $id ] )
            ->whereNotIn( 'course_id', $course_ids )
            ->update( [ 'deleted_at' => carbon::now() , 'deleted_by' => Auth::id() ] );


        if( is_array( $course_ids ) ) {

//            $data = [ ];
//            foreach ( $course_ids as $course_id ) {
//                if( !InstituteAllocationCourses::where( [ 'allocation_id' => $id, 'course_id' => $course_id ] )->exists( ) ) {
//                    $data[] = [ 'allocation_id' => $id, 'course_id' => $course_id ];
//                }
//            }
//
//
//            InstituteAllocationCourses::insert( $data );

            foreach ( $course_ids as $course_id ) {
                InstituteAllocationCourses::withTrashed()
                    ->UpdateOrCreate(
                        ['allocation_id' => $id, 'course_id' => $course_id],
                        ['deleted_at' => null]
                    );
//            }
            }

        }
    }

    public function store(Request $request)
    {
        $instituteAllocation = InstituteAllocation::create( $this->checkValidatedData( request(), null, $course_ids ) );

        $this->storeCourseIds( $instituteAllocation->id, $course_ids );

        return redirect()
            ->route('institute-allocations.show', $request->id)
            ->with('message', 'The record has been successfully added.');
    }

    public function show(InstituteAllocation $instituteAllocation)
    {
        return view('admin.institute-allocations.show', [
            'instituteAllocation' => $instituteAllocation
        ]);
    }

    public function edit(InstituteAllocation $instituteAllocation)
    {
        return view('admin.institute-allocations.edit', [
            'instituteAllocation' => $instituteAllocation,
            'courses' => $this->get_courses(),
            'selected_courses' =>
                InstituteAllocationCourses::where('allocation_id', $instituteAllocation->id )->pluck( 'course_id' ),
        ]);
    }

    public function update(Request $request, InstituteAllocation $instituteAllocation)
    {
        $instituteAllocation->update($this->checkValidatedData(request(), $instituteAllocation->id, $course_ids ));
        $this->storeCourseIds( $instituteAllocation->id, $course_ids );

        return view('admin.institute-allocations.edit', [
            'instituteAllocation' => $instituteAllocation,
            'courses' => $this->get_courses(),
            'selected_courses' =>
                InstituteAllocationCourses::where('allocation_id', $instituteAllocation->id )->pluck( 'course_id' ),
        ]);

        return redirect()
            ->route('institute-allocations.show', $instituteAllocation->id)
            ->with('message', 'The record has been successfully updated.');
    }

    public function destroy(InstituteAllocation $instituteAllocation)
    {
        $instituteAllocation->delete();
        return redirect()
            ->route('institute-allocations.index')
            ->with('message', 'The record has been successfully deleted.');
    }

    private function checkValidatedData($validatedData, $id = '', &$course_ids = [])
    {
        $data = $validatedData->validate([
            'name' => [
                'required',
                'max:250',
                Rule::unique('institute_allocations', 'name')->ignore($id)
            ],
            'course_ids' => 'required|array'
        ]);
        $course_ids = $data['course_ids'];
        return Arr::except($data, 'course_ids' );
    }
}
