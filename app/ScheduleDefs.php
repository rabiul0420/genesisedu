<?php

namespace App;


trait ScheduleDefs
{
    public function schedule_types()
    {
        return $scheduleTypes = ScheduleType::get();
    }
    
    public function schedule_topic_content_types()
    {        
        return $contentTypes =  ScheduleTopicContentType::get();
    }

    public function schedule_module_types()
    {
        return $moduleTypes = ScheduleModuleType::get();
    }

    public function schedule_module_content_types()
    {
        return $moduleContentTypes = ScheduleModuleContentType::get();
    }
    
    public function schedule_media_types()
    {
        return $mediaTypes = ScheduleMediaType::get();
    }

    public function schedule_batch_types()
    {
        return $batchTypes = ScheduleBatchType::get();
    }

    public function program_types()
    {
        return $programTypes = ScheduleProgramType::get();;
    }

    public function schedule_program_content_types()
    {
        return $programContentTypes = ScheduleProgramContentType::get();
    }

    public function get_topic_content_type_id_from_name($name)
    {
        $program_content_type_id = ScheduleTopicContentType::where('name',$name)->first();
        return $program_content_type_id->id??'';
    }

    public function get_program_content_type_id_from_name($name)
    {
        $program_content_type_id = ScheduleProgramContentType::where('name',$name)->first();
        return $program_content_type_id->id??'';
    }

    public function get_module_content_type_id_from_name($name)
    {
        $module_content_type_id = ScheduleModuleContentType::where('name',$name)->first();
        return $module_content_type_id->id??'';
    }

    public function schedule_topic_program_content_type($topic_content_type_id)
    {
        return $programContentTypes = ScheduleProgramContentType::where('topic_content_type_id',$topic_content_type_id)->first();
    }
    
}
