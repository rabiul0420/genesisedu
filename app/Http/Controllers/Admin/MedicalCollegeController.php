<?php

namespace App\Http\Controllers\Admin;

use App\MedicalCollege;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MedicalColleges;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class MedicalCollegeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.medical-colleges.index', [
            'medicalColleges' => MedicalCollege::with('user:id,name')->orderBy('name', 'asc')->get()
        ]);
    }


    public function create()
    {
        return view('admin.medical-colleges.create', [
            'medicalCollege' => new MedicalCollege()
        ]);
    }

    public function store(Request $request)
    {
        $medicalCollege = MedicalCollege::create($this->checkValidatedData(request()));

        Cache::forget(self::HOME_PAGE_MEDICAL_COLLEGE);

        return redirect()
            ->route('medical-colleges.show', $medicalCollege->id)
            ->with('message', 'The record has been successfully added.');
    }

    public function show(MedicalCollege $medicalCollege)
    {
        return view('admin.medical-colleges.show', [
            'medicalCollege' => $medicalCollege
        ]);
    }

    public function edit(MedicalCollege $medicalCollege)
    {
        return view('admin.medical-colleges.edit', [
            'medicalCollege' => $medicalCollege
        ]);
    }

    public function update(Request $request, MedicalCollege $medicalCollege)
    {
        $medicalCollege->update($this->checkValidatedData(request()));

        Cache::forget(self::HOME_PAGE_MEDICAL_COLLEGE);

        return redirect()
            ->route('medical-colleges.show', $medicalCollege->id)
            ->with('message', 'The record has been successfully updated.');
    }

    public function destroy(MedicalCollege $medicalCollege)
    {
        $medicalCollege->delete();
        return redirect()
            ->route('medical-colleges.index')
            ->with('message', 'The record has been successfully deleted.');
    }

    private function checkValidatedData($validatedData)
    {
        return $validatedData->validate([
            'name' => 'required|max:150',
            'type' => 'required',
            'status' => 'required|numeric',
            'created_by' => '',
            'updated_by' => '',
            'type' => 'required',
        ]);
    }
}
