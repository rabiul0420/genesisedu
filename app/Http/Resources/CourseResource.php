<?php

namespace App\Http\Resources;

use App\CourseSessions;
use Illuminate\Http\Resources\Json\Resource;

class CourseResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $data = [];

        $data["institute_id"] = $this->institute_id;
        $data["id"] = $this->id;
        $data["name"] = $this->name;
        $data["course_sessions"] = CourseSessionResource::collection($this->course_years );

        return $data;
    }
}
