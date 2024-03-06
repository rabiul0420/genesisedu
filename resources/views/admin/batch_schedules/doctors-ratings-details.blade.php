@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                Doctor ratings
            </li>
        </ul>

    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Doctor Ratings EE
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-body">
                        <form>

                            <div class="row sc_search">

                                <div class="lecture_video">
                                    <div class="form-group">
                                        <h5>Video Name <span class="text-danger"></span></h5>
                                        <div class="controls">
                                            @php  $lecture_video->prepend('Select Video', ''); @endphp
                                            {!! Form::select('lecture_video_id', $lecture_video, '', ['class' => 'form-control batch2', 'required' => 'required', 'id' => 'batch_id']) !!}
                                            <i></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="mentor">
                                    <div class="form-group">
                                        <h5>Video Name <span class="text-danger"></span></h5>
                                        <div class="controls">
                                            @php  $mentors->prepend('Select Mentor', ''); @endphp
                                            {!! Form::select('mentor_id', $mentors, '', ['class' => 'form-control', 'id' => 'mentor_id']) !!}
                                            <i></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-success">View Ratings</button>
                                </div>

                            </div>

                        </form>

                        <div class="row">

                        </div>

                    </div>
                </div>
            </div>



        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Ratings description
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="modal-body" style="background-color: #f0fdf8">
                        <div>
                            <div class="badge bg-info"
                                style="margin-bottom: 10px; text-align: center; display: block; padding: 12px 15px; font-size: 15px; white-space: normal ">
                                $lecture_video_name</div>

                            <div class="badge bg-success">Mentor</div>
                            <div class="text-info">GENESIS MENTORS</div>
                            <div class="text-success" style="font-size: 11px">RESIDENTS</div>

                        </div>

                        <style>
                            .table>:not(:last-child)>:last-child>* {
                                border-bottom: grey 1px solid;
                            }

                            .table>:not(:last-child)>:last-child>td {
                                vertical-align: middle;
                            }

                        </style>


                        <table class="table table-striped" style="margin-top: 15px">
                            <thead>
                                <tr style="border-top: 1px solid grey;border-bottom: 1px solid grey">
                                    <th>Criteria</th>



                                    <th style="text-align: center">Average</th>


                                    <th style="text-align: center">Good</th>


                                    <th style="text-align: center">Very Good</th>


                                    <th style="text-align: center">Excellent</th>

                                </tr>
                            </thead>
                            <tbody>

                                <tr class="criteria-list" data-criteria="Introduction">
                                    <td class="title">
                                        <div>Introduction</div>
                                    </td>



                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Introduction]" value="1"
                                            checked="" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Introduction]" value="2"
                                            disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Introduction]" value="3"
                                            disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Introduction]" value="4"
                                            disabled="">
                                    </td>
                                </tr>
                                <tr class="criteria-list" data-criteria="Knowledge depth of Mentor">
                                    <td class="title">
                                        <div>Knowledge depth of Mentor</div>
                                    </td>



                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Knowledge depth of Mentor]"
                                            value="1" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Knowledge depth of Mentor]"
                                            value="2" checked="" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Knowledge depth of Mentor]"
                                            value="3" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Knowledge depth of Mentor]"
                                            value="4" disabled="">
                                    </td>
                                </tr>
                                <tr class="criteria-list" data-criteria="Expression Capability">
                                    <td class="title">
                                        <div>Expression Capability</div>
                                    </td>



                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Expression Capability]"
                                            value="1" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Expression Capability]"
                                            value="2" checked="" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Expression Capability]"
                                            value="3" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Expression Capability]"
                                            value="4" disabled="">
                                    </td>
                                </tr>
                                <tr class="criteria-list" data-criteria="Interaction">
                                    <td class="title">
                                        <div>Interaction</div>
                                    </td>



                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Interaction]" value="1"
                                            disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Interaction]" value="2"
                                            disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Interaction]" value="3"
                                            disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Interaction]" value="4"
                                            checked="" disabled="">
                                    </td>
                                </tr>
                                <tr class="criteria-list" data-criteria="Overall">
                                    <td class="title">
                                        <div>Overall</div>
                                    </td>



                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Overall]" value="1"
                                            disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Overall]" value="2"
                                            disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Overall]" value="3"
                                            checked="" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[primary][Overall]" value="4"
                                            disabled="">
                                    </td>
                                </tr>

                            </tbody>

                        </table>


                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Criteria</th>
                                    <th style="text-align: center">Smooth</th>
                                    <th style="text-align: center">Little bit disturb</th>
                                    <th style="text-align: center">disturb</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr class="criteria-list" data-criteria="Projector Support">
                                    <td class="title">
                                        <div>Projector Support</div>
                                    </td>




                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Projector Support]"
                                            value="1" checked="" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Projector Support]"
                                            value="2" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Projector Support]"
                                            value="3" disabled="">
                                    </td>
                                </tr>
                                <tr class="criteria-list" data-criteria="Sound System">
                                    <td class="title">
                                        <div>Sound System</div>
                                    </td>




                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Sound System]"
                                            value="1" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Sound System]"
                                            value="2" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Sound System]"
                                            value="3" checked="" disabled="">
                                    </td>
                                </tr>
                                <tr class="criteria-list" data-criteria="Video Quality">
                                    <td class="title">
                                        <div>Video Quality</div>
                                    </td>




                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Video Quality]"
                                            value="1" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Video Quality]"
                                            value="2" checked="" disabled="">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle">
                                        <input required="" type="radio" name="progress[video-quality][Video Quality]"
                                            value="3" disabled="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>




                        <div style=" font-size: 12px; color: green" id="modal-message">Check all criteria and hit submit</div>
                    </div>
                </div>
            </div>



        </div>
    </div>

@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />

    <style>
        .sc_search {
            display: flex;
            justify-content: center;
            align-items: center;
        }

    </style>

@endsection

@section('js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script>
        $('#batch_id').select2({});
    </script>
@endsection
