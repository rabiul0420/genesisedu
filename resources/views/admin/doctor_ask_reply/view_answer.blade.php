@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'">'.$value.'</a> </li>';
            }
            ?>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i><?php echo $module_name;?> List
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body">
                <div class="col-md-12 col-md-offset-0" style="">

                    <div class="portlet">
                        <div class="portlet-body">
                            
                            @foreach($answer_info as $key => $answer)
                                <div class="col-md-12" style=" padding:10px; margin: 2px;
                                background-color:{{($answer->user_id!=0)?'#FFFFFF':'#E9E9E9'}};">
                                    {!! ($answer->user_id!=0)?'Replied'.'<br>( '.date('d M Y h:m a',strtotime($answer->created_at)).' )' :'My Question'.'<br>( '.date('d M Y h:m a',strtotime($answer->created_at)).' )' !!} :
                                    @php echo strip_tags($answer->message); @endphp
                                </div>
                            @endforeach
                            
                        </div>
                    </div>

                    </div>

                    <div class="col-md-12" style="margin-top: 20px;">
                    <hr>
                    <h4>New Question</h4>
                    {!! Form::open(['url'=>['question-again'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">
                        
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="input-icon right">
                                    <textarea id="description" name="description" required></textarea>
                                    <input type="hidden" name="ask_id" value="{{$ask_id}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-0 col-md-9">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


@endsection

@section('js')

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            CKEDITOR.replace( 'description' );
            // $('.select2').select2();
        })
    </script>

@endsection