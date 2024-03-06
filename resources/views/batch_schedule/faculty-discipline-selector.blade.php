@extends('layouts.app')
@section('content')

    <div class="container">

        <div class="row">

            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default pt-2">

                    <div class="panel-body px-0 py-3">
                        @if(Session::has('message'))
                            <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                                <p> {!! Session::get('message') !!} </p>
                            </div>
                        @endif

                        <div class="card card-body">
                            <h5 class="text-center">Choice Your <i>{{ $isCombined ? 'residency ':'' }} {{$label}}</i> {!!   $isCombined ? ' and <i>BCPS discipline</i>':'' !!}</h5>
                            <hr>

                            @if ($errors->any())
                                <div class="alert alert-danger my-2">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="residency-discipline-form" method="post" action="">

                                @php $facultyOrDiscipline = isset( $faculties ) && !empty( $faculties )
                                                            ? $faculties
                                                            : ( $disciplines ?? [] );  @endphp

                                <div class="row mx-0 my-2">

                                    <div class="col-md-4 text-right py-2 px-3">
                                        <label>{{$isCombined ? 'Residency':''}} {{ ucfirst( $label ) }}</label>
                                    </div>

                                    <div class="col-md-4">
                                        <select name="faculty-or-discipline-id" class="form-select" id="faculty-or-discipline-id" required>
                                            <option value="">--Select--</option>
                                            @foreach ( $facultyOrDiscipline as $id => $name )
                                                <option value="{{ $id }}" {{old('faculty_or_discipline_id') == $id ? 'selected':''}}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @if( $isCombined )
                                    <div class="row mx-0 my-2">

                                        <div class="col-md-4 text-right py-2 px-3">
                                            <label>BCPS Discipline</label>
                                        </div>

                                        <div class="col-md-4">
                                            <select name="bcps-discipline-id" class="form-select" id="bcps-discipline-id" required>
                                                <option value="">--Select--</option>
                                                @foreach ( $disciplines as $id => $name )
                                                    <option value="{{ $id }}" {{old('bcps_subject_id') == $id ? 'selected':''}}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mx-0 my-2">

                                    <div class="col-md-12 d-flex justify-content-center" style="margin-top: 15px">
                                        <button class="btn btn-success" >View Schedule</button>
                                    </div>

                                </div>


                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        const base_url = "{{ url('') }}";

        $('#residency-discipline-form').on( 'submit', function ( e ){
            e.preventDefault();

            const batch_id = {{ $batch_id }};
            const faculty_or_discipline_id = $(this).find( '#faculty-or-discipline-id' ).val( );
            const bcps_discipline_id = $(this).find( '#bcps-discipline-id' ).val( );

            window.location = base_url + '/view-batch-schedule/' + batch_id + '/' + faculty_or_discipline_id + (bcps_discipline_id ? '/' + bcps_discipline_id: '')

        });

    </script>

@endsection