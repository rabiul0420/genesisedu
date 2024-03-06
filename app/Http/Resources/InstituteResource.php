<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class InstituteResource extends Resource
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
        $data["id"]  = $this->id;
        $data["name"]  = $this->name;
        $data["courses"]  = CourseResource::collection( $this->courses );

        return  $data;

    }
}
