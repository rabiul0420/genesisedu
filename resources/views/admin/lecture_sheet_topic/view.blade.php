@extends('admin.layouts.app')

@section('content')
        {{-- @foreach ($lecture_sheets as $lecture_sheet)

        {{ $loop->iteration }} {{ $lecture_sheet->lecture_sheet->name }} <br>

        @endforeach --}}
        <div class="row ">
            <div class="col-md-12">
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet">
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover datatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                            </tr>
                            </thead>
                            <tbody>
    
                            @foreach($lecture_sheets as $index=>$lecture_sheet)
                                <tr>
                                    <td>{{ $index+=1 }}</td>
                                    <td>{{ $lecture_sheet->lecture_sheet->name }}</td>
                                </tr>
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
        $(document).ready(function() {
        })
    </script>


@endsection
