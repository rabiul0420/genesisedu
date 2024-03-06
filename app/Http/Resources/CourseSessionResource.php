<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CourseSessionResource extends Resource
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

        $data["id"] = $this->id;
        $data["course_id"] = $this->course_id;
        $data["name"] = $this->name;
        $data["status"] = $this->status;
        $data["year"] = $this->year;

        return $data;
    }
}
