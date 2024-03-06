<?php

namespace App\Http\Controllers\Admin;

use App\AddonContent;
use App\AddonService;
use App\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LectureVideo;
use Illuminate\Validation\Rule;

class AddonServiceController extends Controller
{
    public function index()
    {
        $addon_services = AddonService::query()
            ->with('addon_contents.contentable')
            ->latest()
            ->paginate(10);

        // return $addon_services;

        return view('tailwind.admin.addon-services.index', [
            "addon_services" => $addon_services
        ]);
    }

    public function store(Request $request)
    {
        // return $request;
        $addon_service = AddonService::create($this->validatedData($request));

        return redirect()->route('addon-services.show', $addon_service->id)
            ->with('message', 'Record has been added successfully');
    }

    public function show(AddonService $addon_service)
    {
        $addon_service->load('addon_contents.contentable');

        return view('tailwind.admin.addon-services.show', [
            'addon_service' => $addon_service
        ]);
    }

    public function update(Request $request, AddonService $addon_service)
    {
        $response = $addon_service->update($this->validatedData($request, $addon_service->id));

        return response([
            'status' => (bool) $response,
            'message' => (string) $response ? "Success" : "Try Again",
        ], 200);
    }

    public function prepare(AddonService $addon_service, $content_type)
    {
        $selected_content_ids = Array();
        $contents = null;
        $search = request()->search;
        $page = request()->page;

        if(!request()->flag) {
            return view('tailwind.admin.addon-services.prepare', compact(
                'addon_service',
                'content_type',
                'search',
                'page',
            ));
        }

        if($content_type === 'lecture') {
            $contents = $this->getContent(LectureVideo::query())->appends(['search' => request()->search]);
            $selected_content_ids = $this->getSelectedContentId($addon_service, LectureVideo::class);
        }
        
        if($content_type === 'exam') {
            $contents = $this->getContent(Exam::query())->appends(['search' => request()->search]);
            $selected_content_ids = $this->getSelectedContentId($addon_service, Exam::class);
        }

        return response([
            "html"          => view('tailwind.admin.addon-services.data', compact('contents', 'selected_content_ids'))->render(),
            "message"       => "Success",
            "totalContent"  => count($selected_content_ids),
        ], 200);
    }

    protected function getContent($query)
    {
        return $query
            // ->orderBy('name')
            ->when(request()->search, function ($query, $search) {
                $query->where(function($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(20);
    }

    protected function getSelectedContentId($addon_service, $contentable_type)
    {
        return $addon_service
            ->addon_contents()
            ->where('contentable_type', $contentable_type)
            ->pluck('contentable_id')
            ->toArray();
    }

    public function assign(Request $request, AddonService $addon_service, $content_type)
    {
        $content = null;
        $contentable_type = null;

        if($content_type === 'lecture') {
            $content = LectureVideo::where('id', $request->content_id)->first() ?? null;
            $contentable_type = LectureVideo::class;
        }
        
        if($content_type === 'exam') {
            $content = Exam::where('id', $request->content_id)->first() ?? null;
            $contentable_type = Exam::class;
        }

        if($content) {
            $addon_content = AddonContent::withTrashed()->updateOrCreate(
                [
                    'addon_service_id' => $addon_service->id,
                    'contentable_id' => $content->id,
                    'contentable_type' => $contentable_type,
                ],
                [
                    'deleted_at' => $request->checked ? NULL : now(),
                    'priority' => $request->checked ? $this->getSuitablePriority($addon_service) : 0,
                ]
            );
        }

        return response([
            'content_id'    => $request->content_id,
            'message'       => $request->checked ? 'Added!' : 'Removed!',
            'totalContent'  => $addon_service->addon_contents()->where('contentable_type', $contentable_type)->count(),
        ], 200);

        return redirect()->route('addon-services.show', $addon_service->id)
            ->with('message', 'Record has been added successfully');
    }

    protected function getSuitablePriority($addon_service, $priority = null)
    {
        if($priority) {
            return $priority;
        }

        $max_priority = AddonContent::where('addon_service_id', $addon_service->id)->max('priority');

        return $max_priority + 1;
    }

    private function validatedData($request, $id = '')
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('addon_services', 'name')->ignore($id),

            ],
            'regular_price' => [
                'required',
                'numeric',
            ],
            'sale_price' => [
                'required',
                'numeric',
            ],
        ]);
    }
}
