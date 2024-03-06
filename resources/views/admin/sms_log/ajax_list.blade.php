<button data-id="{{ $sms_log_ajax_list->id }}" data-job-id="{{ $sms_log_ajax_list->job_id }}" 
    {{ $sms_log_ajax_list->delivery_status == 'Delivered' ? 'Disabled':'' }}
    class="btn btn-xs btn-primary">View Status</button> 