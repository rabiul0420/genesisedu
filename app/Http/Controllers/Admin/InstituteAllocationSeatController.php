<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\InstituteAllocationCourses;
use App\InstituteAllocationSeat;
use App\InstituteDisciplinesAllocationInstitutes;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\InstituteAllocation;
use App\InstituteDiscipline;
use Illuminate\Validation\Rule;

class InstituteAllocationSeatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.institute-allocation-seats.index', [
            'instituteAllocationSeats' => InstituteAllocationSeat::orderBy('Year', 'desc')->get()
        ]);
    }


    private function get_courses( $institute_allocation_id = null ){
        if( !$institute_allocation_id ) return new Collection();
        return Courses::where([ 'status' => 1, 'institute_id' => 6 ])
            ->whereIn(
                'id',  ( InstituteAllocationCourses::select('course_id') ->where( 'allocation_id', $institute_allocation_id )->whereNull( 'deleted_at' ) )
            )
            ->pluck( 'name', 'id' );
    }


    public function create()
    {

        //dd( old( 'institute_discipline_id' ) );

        return view('admin.institute-allocation-seats.create', [
            'instituteAllocationSeat' => new InstituteAllocationSeat(),
//            'instituteAllocations' => InstituteAllocation::orderBy('name', 'asc')->pluck('name', 'id'),
            'instituteAllocations' => $this->getInstituteAllocationList( old('institute_discipline_id', null ) ),
//            'instituteAllocations' =>  old('institute_discipline_id', null ) ? old( 'institute_discipline_id' ) : [],
            'allocationCourses' => $this->get_courses( old('institute_allocation_id', null ) ),
            'instituteDisciplines' => InstituteDiscipline::orderBy('name', 'asc')->when( old( 'institute_discipline_id' ), function ( $query ) {

            })->pluck('name', 'id')
        ]);
    }

    public function store(Request $request)
    {
        InstituteAllocationSeat::create($this->checkValidatedData(request()));
        return redirect()
            ->route('institute-allocation-seats.show', $request->id)
            ->with('message', 'The record has been successfully added.');
    }

    public function show(InstituteAllocationSeat $instituteAllocationSeat)
    {
        return view('admin.institute-allocation-seats.show', [
            'instituteAllocationSeat' => $instituteAllocationSeat
        ]);
    }

    public function getInstituteAllocationList( $institute_discipline_id = null ){
        if( !$institute_discipline_id ) return new Collection();
        return InstituteAllocation::whereIn( 'id',
            InstituteDisciplinesAllocationInstitutes::select( 'institute_id' )
                ->where( 'discipline_id',  $institute_discipline_id )
                ->whereNull( 'deleted_at' )
        )->orderBy('name', 'asc')->pluck('name', 'id');
    }

    public function edit(InstituteAllocationSeat $instituteAllocationSeat)
    {
        //dd( $this->get_courses() );
        return view('admin.institute-allocation-seats.edit', [
            'instituteAllocationSeat' => $instituteAllocationSeat,
            'instituteAllocations' => $this->getInstituteAllocationList( old( 'institute_discipline_id', $instituteAllocationSeat->institute_discipline_id ) ),
            'allocationCourses' => $this->get_courses(old('institute_allocation_id', $instituteAllocationSeat->institute_allocation_id )),
            'instituteDisciplines' => InstituteDiscipline::orderBy('name', 'asc')->pluck('name', 'id')
        ]);
    }

    public function update(Request $request, InstituteAllocationSeat $instituteAllocationSeat)
    {
        $instituteAllocationSeat->update($this->checkValidatedData(request(), $instituteAllocationSeat->id));
        return redirect()
            ->route('institute-allocation-seats.show', $instituteAllocationSeat->id)
            ->with('message', 'The record has been successfully updated.');
    }

    public function destroy(InstituteAllocationSeat $instituteAllocationSeat)
    {
        $instituteAllocationSeat->delete();
        return redirect()
            ->route('institute-allocation-seats.index')
            ->with('message', 'The record has been successfully deleted.');
    }

    private function checkValidatedData($validatedData, $id = '')
    {

        return $validatedData->validate([
            'institute_allocation_id'   =>  [
                'required','numeric', 
                Rule::unique('institute_allocation_seats')
                    ->where('allocation_course_id', $validatedData->allocation_course_id)
                    ->where('institute_discipline_id', $validatedData->institute_discipline_id)
                    ->where('year', $validatedData->year)
                    ->ignore($id)
            ],
            'institute_discipline_id'   => 'required|numeric',
            'year'                      => 'required|numeric',
            'private'                   => 'required|numeric',
            'government'                => 'required|numeric',
            'bsmmu'                     => 'required|numeric',
            'armed_forces'              => 'required|numeric',
            'others'                    => 'required|numeric',
            'allocation_course_id'      => 'required|numeric',
        ]);
    }

    public function duplicate($id)
    {
        $instituteAllocationSeat = InstituteAllocationSeat::where('id',$id)->first();
        return view('admin.institute-allocation-seats.duplicate', [
            'instituteAllocationSeat' => $instituteAllocationSeat,
            'instituteAllocations' => $this->getInstituteAllocationList( old( 'institute_discipline_id', $instituteAllocationSeat->institute_discipline_id ) ),
            'allocationCourses' => $this->get_courses(old('institute_allocation_id', $instituteAllocationSeat->institute_allocation_id )),
            'instituteDisciplines' => InstituteDiscipline::orderBy('name', 'asc')->pluck('name', 'id')
        ]);
    }

    public function duplicate_save(Request $request, InstituteAllocationSeat $instituteAllocationSeat)
    {
        
        $allocation_institute = InstituteAllocationSeat::create($this->checkValidatedData(request()));
        return redirect()
            ->route('institute-allocation-seats.show', $allocation_institute->id)
            ->with('message', 'The record has been successfully added.');
    }
}
