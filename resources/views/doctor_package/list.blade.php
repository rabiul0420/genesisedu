@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            @include('side_bar')

            <div class="col-md-9 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Packages</h3></div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <?php if (isset($_GET['msgs'])) { echo "<div class='alert alert-success'>{$_GET['msgs']}</div>"; } ?>
                        <?php if (isset($_GET['msgf'])) { echo "<div class='alert alert-success' style='color:red;'>{$_GET['msgf']}</div>"; } ?>
                        <?php if (isset($_GET['msgc'])) { echo "<div class='alert alert-success' style='color:red;'>{$_GET['msgc']}</div>"; } ?>



                        <div class="col-md-12 col-md-offset-0" style="">
                            <hr><h4><b>My Packages</b></h4>
                        </div>

                        <div class="col-md-12">
                            <div class="portlet">
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Institute</th>
                                            <th>Course</th>
                                            <th>Faculty</th>
                                            <th>Discipline</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($doctor_packages as $doctor_packages)
                                                <tr>
                                                    <td>{{ $doctor_packages->id }}</td>
                                                    <td>{{ $doctor_packages->name }}</td>
                                                    <td>{{ isset($doctor_packages->package->institute->name)?$doctor_packages->package->institute->name:'' }}</td>
                                                    <td>{{ isset($doctor_packages->package->course->name)?$doctor_packages->package->course->name:'' }}</td>
                                                    <td>{{ isset($doctor_packages->package->faculty->name)?$doctor_packages->package->faculty->name:'' }}</td>
                                                    <td>{{ isset($doctor_packages->package->discipline->name)?$doctor_packages->package->discipline->name:'' }}</td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection