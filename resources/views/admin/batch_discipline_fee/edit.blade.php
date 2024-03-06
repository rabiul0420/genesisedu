@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{ $title }}</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif
    @if(Session::has('error'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-danger' }}" role="alert">
            <p> {{ Session::get('error') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{ $title }}
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    @if ($action ?? '' == 'duplicate' )
                    {!! Form::open(['action'=>['Admin\BatchDisciplineFeeController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    @else
                    {!! Form::open(['action'=>['Admin\BatchDisciplineFeeController@update', $batch_discipline_fee->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    @endif
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $batches->prepend('Select Batch', ''); @endphp
                                {!! Form::select('batch_id',$batches, $batch_discipline_fee->batch_id,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="subjects">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Subject (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    @php  $subjects->prepend('Select Subject', ''); @endphp
                                    {!! Form::select('subject_id',$subjects, $batch_discipline_fee->subject_id,['class'=>'form-control','required'=>'required']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Admission Fee (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="admission_fee" min="0" required value="{{ $batch_discipline_fee->admission_fee }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Lecture Sheet Fee (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="lecture_sheet_fee" min="0" required value="{{ $batch_discipline_fee->lecture_sheet_fee }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Discount From Regular (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="discount_from_regular" min="0" required value="{{ $batch_discipline_fee->discount_from_regular }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Discount From Exam (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="discount_from_exam" min="0" required value="{{ $batch_discipline_fee->discount_from_exam }}" class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/batch-discipline-fee') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->

                </div>
            </div>

        </div>
    </div>



@endsection

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {

            $("body").on( "change", "[name='batch_id']", function() {
                var batch_id = $("[name='batch_id']").val();
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/batch-subjects',
                    dataType: 'HTML',
                    data: {batch_id:batch_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.subjects').html('');
                        $('.subjects').html(data['subjects']);            
                        
                    }
                });
            })     

            
        })
    </script>


@endsection