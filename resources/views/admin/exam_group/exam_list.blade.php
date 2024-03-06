{!! Form::select('exam_id[]', $exams, old( 'exam_id', $exam_group_exam_ids ?? [] ) ,
    ['class'=>'form-control select2', 'multiple' => 'multiple', 'required'=>'required', 'id'=>'exam_id', 'data-placeholder' => 'Select Exams' ])
!!}<i></i>