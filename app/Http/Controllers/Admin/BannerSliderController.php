<?php

namespace App\Http\Controllers\Admin;

use App\BannerSlider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class BannerSliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.banner-sliders.index', [
            'bannerSliders' => BannerSlider::orderBy('id', 'desc')->get()
        ]);
    }

    public function create()
    {
        return view('admin.banner-sliders.create', [
            'bannerSlider' => new BannerSlider()
        ]);
    }

    public function store(Request $request)
    {
        $bannerSlider = BannerSlider::create($this->checkValidatedData(request()));
        $this->storeImage($bannerSlider);
        return redirect()
            ->route('banner-sliders.show', $bannerSlider->id)
            ->with('message', 'The record has been successfully added.');
    }

    public function show(BannerSlider $bannerSlider)
    {
        return view('admin.banner-sliders.show', [
            'bannerSlider' => $bannerSlider
        ]);
    }

    public function edit(BannerSlider $bannerSlider)
    {
        return view('admin.banner-sliders.edit', [
            'bannerSlider' => $bannerSlider
        ]);
    }

    public function update(Request $request, BannerSlider $bannerSlider)
    {
        $bannerSlider->update($this->checkValidatedData(request()));
        $this->storeImage($bannerSlider);
        return redirect()
            ->route('banner-sliders.show', $bannerSlider->id)
            ->with('message', 'The record has been successfully updated.');
    }

    public function destroy(BannerSlider $bannerSlider)
    {
        $bannerSlider->delete();
        return redirect()
            ->route('banner-sliders.index')
            ->with('message', 'The record has been successfully deleted.');
    }

    private function checkValidatedData($validatedData)
    {
        return $validatedData->validate([
            'status' => 'bail|required|numeric',
            'priority' => 'bail|required|numeric',
        ]);
    }

    private function storeImage($collection)
    {
        request()->validate([
            'image' => 'bail|sometimes|image',
        ]);

        if (request()->hasFile('image')) {
            if (file_exists($collection->image) && $collection->image != '') {
                unlink($collection->image);
            }

            $image = request()->file('image');
            $fileName = 'banner-slider' . date('Ymdhis') . '.' . strtolower($image->getClientOriginalExtension());
            $dirPath = 'uploads/banner-slider/';

            if (!is_dir($dirPath)) {
                File::makeDirectory($dirPath, $mode = 0777, true, true);
            }

            Image::make($image->getRealPath())
                ->resize(1000, 700)
                ->save($dirPath . $fileName);

            $collection->update([
                'image' => $dirPath . $fileName
            ]);
        }
    }
}
