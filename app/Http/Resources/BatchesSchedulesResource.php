<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

class BatchesSchedulesResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        //return parent::toArray($request);

        $data = [];

        $data['name'] = $this->name;
        $data['contact_details'] = $this->contact_details;
        $data['address'] = $this->address;
        $data['year'] = $this->year;
        $data['batch_id'] = $this->batch_id;
        $data['course_id'] = $this->course_id;
        $data['executive_id'] = $this->executive_id;
        $data['faculty_id'] = $this->faculty_id;
        $data['id'] = $this->id;
        $data['initial_date'] = $this->initial_date;
        $data['institute_id'] = $this->institute_id;
        $data['paper'] = $this->paper;
        $data['room_id'] = $this->room_id;
        $data['service_package_id'] = $this->service_package_id;
        $data['session_id'] = $this->session_id;
        $data['status'] = $this->status;
        $data['subject_id'] = $this->subject_id;
        $data['bcps_subject_id'] = $this->bcps_subject_id;
        $data['support_stuff_id'] = $this->support_stuff_id;
        $data['tag_line'] = $this->tag_line;
        $data['terms_and_condition'] = $this->terms_and_condition;

        $data['details'] = [];

        $data['seleted_video_ids'] = &$seleted_video_ids;
        $data['seleted_exam_ids'] = &$seleted_exam_ids;
        $data['fb_links'] = &$fb_links;

        $fb_links = [];

        if ($this->meta instanceof Collection) {
            $links = $this->meta->first();
            if ($links) {
                $fb_links = json_decode($links->value);
            }
        }

        $seleted_video_ids = [];

        if ($this->time_slots instanceof Collection) {
            foreach ($this->time_slots as $slot) {
                $dt = Carbon::make($slot['datetime']);

                $data['details'][] = [
                    'time' => $dt->format('h:i A'),
                    'date' => $dt->format('Y-m-d'),
                    'id' => $slot->id,
                    'contents' => $slot->schedule_details ?? [],
                ];

                if ($slot->schedule_details instanceof Collection) {
                    foreach ($slot->schedule_details as $detail) {
                        if ($detail->type == 'Class') {
                            $seleted_video_ids[] = $detail->class_or_exam_id;
                        } else {
                            $seleted_exam_ids[] = $detail->class_or_exam_id;
                        }
                    }
                }

            }
        }

        return $data;
    }
}
