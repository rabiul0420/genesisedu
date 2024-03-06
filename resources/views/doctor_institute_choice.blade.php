@extends('layouts.app')
@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'Exam' }}</h2>
                    </div>
                </div>

                <div class="panel-body px-0 py-3">
                    @if(Session::has('message'))
                    <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                        <p> {!! Session::get('message') !!} </p>
                    </div>
                    @endif

                    <div class="card card-body">
                        <h5 class="text-center">Choice Your Institutes</h5>
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

                        <form action="{{ route('doctor-institute-choices.store') }}" method="post">
                            {{ csrf_field() }}

                            <div class="row mx-0 my-2">
                                <div class="col-md-4 text-right py-2 px-3">
                                    <label>First Choice</label>
                                </div>
                                <div class="col-md-6">
                                    <select name="first_institute" class="form-select">
                                        <option value="">--select institute--</option>
                                        @foreach ($instituteAllocations as $key => $instituteAllocation)
                                        <option {{ old('first_institute') == $key ? 'selected' : '' }}
                                            value="{{ $key }}">{{ $instituteAllocation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mx-0 my-2">
                                <div class="col-md-4 text-right py-2 px-3">
                                    <label>Second Choice</label>
                                </div>
                                <div class="col-md-6">
                                    <select name="second_institute" class="form-select">
                                        <option value="">--select institute--</option>
                                        @foreach ($instituteAllocations as $key => $instituteAllocation)
                                        <option {{ old('second_institute') == $key ? 'selected' : '' }}
                                            value="{{ $key }}">{{ $instituteAllocation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mx-0 my-2">
                                <div class="col-md-4 text-right py-2 px-3">
                                    <label>Third Choice</label>
                                </div>
                                <div class="col-md-6">
                                    <select name="third_institute" class="form-select">
                                        <option value="">--select institute--</option>
                                        @foreach ($instituteAllocations as $key => $instituteAllocation)
                                        <option {{ old('third_institute') == $key ? 'selected' : '' }}
                                            value="{{ $key }}">{{ $instituteAllocation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name="exam_id" value="{{ $exam_id }}">
                            <input type="hidden" name="doctor_course_id" value="{{ $doctor_course_id }}">

                            <div class="row mx-0">
                                <div class="col-6 offset-md-4">
                                    <div class="py-3">
                                        <input type="submit" onclick="return confirm('Are you sure want to submit ?')" class="btn btn-info btn-sm" value="Submit">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

@endsection


@section('js')

    <script>

        $(document).ready( function(){

            let allList = [];

            $('[name="first_institute"] option').each( (i, opt) => { allList.push({id: $(opt).val(), name: $(opt).html() }) });

            $('[name="first_institute"],[name="second_institute"], [name="third_institute"]').each(function(i, institute){
                $(this).change( () => filterOthers(this) );
            });

            const others = {
                first_institute: '[name="second_institute"],[name="third_institute"]',
                second_institute: '[name="first_institute"],[name="third_institute"]',
                third_institute: '[name="first_institute"],[name="second_institute"]',
            }


            function filterOthers( INSTITUTE ) {
                let name = $( INSTITUTE ).attr( 'name' );

                $( others[name] ).each( filterOptions )

                function filterOptions(i, institute) {
                    let _name = $(institute).attr( 'name' );
                    console.log( _name );

                    let newList = '';
                    let removedIds = getSelected( others[ _name ] );

                    let SelectedID = $(institute).val();

                    allList.map( function( element ){
                        let id = element.id;
                        if( removedIds.indexOf( Number(id) ) == -1 ){
                            newList += '<option value="' +id+ '" '+( SelectedID == id ? ' selected ':'' )+'>' + element.name + '</option>\n';
                        }
                    })

                    $(institute).html( newList )
                }
            }




            function getSelected( x ) {
                selectedIds = [];
                $(x).each( function(){
                    var id = $(this).val();

                    if( id != '' ){
                        selectedIds.push( Number(id) );
                    }
                })

                return selectedIds
            }
        });

    </script>
@endsection
