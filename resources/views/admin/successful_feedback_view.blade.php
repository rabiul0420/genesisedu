@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Successful Feedback View</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
                     
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Successful Feedback of<b> {{ $successful_view->name }}  </b>                  
                    </div>
                </div>
                        <div class="portlet-body">
                            <div class="row">              
                                <div class="col-sm-4"> 
                                    @if ($successful_view->bmdc_no)                                                                                
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Bmdc No:</label>                                 
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->bmdc_no }}                                  
                                            </div>
                                        </div>
                                    @endif

                                    @if ($successful_view->medical_college_id)                                                                                           
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3"> Medical College:</label>                                   
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->medical_college->name??'' }}                                   
                                            </div>
                                        </div>
                                    @endif 
 
                                    @if ($successful_view->mobile_number)                                                               
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Mobile Number :</label>                                
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->mobile_number }}                                    
                                            </div>
                                        </div>
                                    @endif 

                                    @if ($successful_view->discipline_id)                                                                                         
                                        <div class="form-group row">
                                            <label for="successful_view" class="col-sm-4 col-form-label mt-3"> Discipline  :</label>                               
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->discipline_id }}                                   
                                            </div>
                                        </div>
                                    @endif 

                                    @if ( $successful_view->address)                                                     
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Address:</label>                               
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->address }}                                    
                                            </div>
                                        </div> 
                                    @endif 
                                    
                                    @if($successful_view->batch_name)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Batch Name:</label>                         
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->batch_name }}                                   
                                            </div>
                                        </div> 
                                    @endif 
                                    
                                    @if($successful_view->year)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Year :</label>                              
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->year }}                                   
                                            </div>
                                        </div> 
                                    @endif 

                                    @if($successful_view->session)                    
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3"> Session :</label>                              
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->session }}                                    
                                            </div>
                                        </div>
                                    @endif 

                                    @if ( $successful_view->regular_class)                                                    
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Regular Class:</label>                               
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->regular_class }}                                    
                                            </div>
                                        </div> 
                                    @endif 

                                    @if ( $successful_view->zoom_live_class)                         
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Zoom live Class :</label>   
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->zoom_live_class }}                                   
                                            </div>
                                        </div> 
                                    @endif 

                                    @if ( $successful_view->exam_class)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3"> Exam_Class  :</label>                  
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->exam_class }}                                    
                                            </div>
                                        </div>
                                    @endif 

                                    @if ( $successful_view->lecture_sheet)   
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Lecture Sheet:</label>                                
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->lecture_sheet }}                                    
                                            </div>
                                        </div> 
                                    @endif 
                                    
                                    @if ( $successful_view->solve_class) 
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Solve Class:</label>                       
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->solve_class }}                                    
                                            </div>
                                        </div> 
                                    @endif  
                                    
                                    @if ( $successful_view->it_support)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">It Support :</label>                                  
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->it_support }}                                    
                                            </div>
                                        </div> 
                                    @endif 
                                    
                                    @if ( $successful_view->struggling_history)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3"> Struggling History  :</label>                               
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->struggling_history }}                                   
                                            </div>
                                        </div>
                                    @endif 

                                    @if ( $successful_view->effective_service)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Effective Service:</label>                                
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->effective_service }}                                   
                                            </div>
                                        </div>
                                    @endif  
                                    
                                    @if ( $successful_view->service_improve)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Service Improve:</label>                                   
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->service_improve }}                                     
                                            </div>
                                        </div>
                                    @endif 

                                    @if($successful_view->overall_value)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Overall Value :</label>                                 
                                            <div class="col-sm-8 mt-3">
                                                {{ $successful_view->overall_value }}                                    
                                            </div>
                                        </div>
                                    @endif

                                    @if($successful_view->created_at)
                                        <div class="form-group row">
                                                <label for="successful_view" class="col-sm-4 col-form-label mt-3">Created Date:</label>                                
                                            <div class="col-sm-8 mt-3">
                                                {{ date('d M Y h:m a',strtotime($successful_view->created_at)) }}                                                                        
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-sm-3">
                                    @if($successful_view->image)
                                       <img class="successful-img"  src=" {{ asset($successful_view->image) }}"  alt="{{ $successful_view->photo }}">
                                    @endif
                                </div>
                            </div>                           
                        </div>
                    </div>                                
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                searchable:true,
            })
        })

    </script>

@endsection

