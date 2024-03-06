@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
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
                        <i class="fa fa-globe"></i>Payment List
                        
                    </div>
                </div>
                
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="100">Date</th>
                            <th>Doctor Name</th>
                            <th>Registration No</th>
                            <th>Trans. ID</th>
                            <th>Paid Amount</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($payments as $sl=>$payment)
                        <tr>
                            <td>{{ $sl+1 }}</td>
                            <td>{{ substr($payment->created_at,0,10) }}</td>
                            <td>
                                <?php 
                                    if (isset($payment->course_info->doctor_id)) {
                                        $doctor = \App\Doctors::select('*')->where('id', $payment->course_info->doctor_id)->first();
                                        echo $doctor->name;
                                    }
                                ?>
                            </td>
                            <td>{{ (isset($payment->course_info->reg_no))?$payment->course_info->reg_no:'' }}</td>
                            <td>{{ $payment->trans_id }}</td>
                            <td>{{ $payment->amount }}</td>
                            
                        </tr>
                        </tbody>
                        @endforeach
                        </tbody>
                    </table>
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
                "columnDefs": [
                    { "searchable": false, "targets": 5 },
                    { "orderable": false, "targets": 5 }
                ]
            })
        })
    </script>

@endsection