@extends('layouts.app')
@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ $title . ' Exam Roll' }}</h2>
                    </div>
                </div>

                <div class="panel-body px-0 py-3">
                    @if(Session::has('message'))
                    <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                        <p> {!! Session::get('message') !!} </p>
                    </div>
                    @endif

                    <div class="card card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger my-2">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('collect-doctor-roll.store') }}" method="post" class="py-4">
                            {{ csrf_field() }}
                            <input type="hidden" name="doctor_course_id" value="{{ $doctor_course_id }}">

                            <div style="display: flex; align-items: center; justify-content: center; gap: 16px;">
                                <input type="text" name="roll" class="form-control" placeholder="{{ $title }} Exam Roll" style="flex-grow: 1; flex-shrink: 1; max-width: 300px;" />
                                <input type="submit" onclick="return confirm('Are you sure want to submit ?')" class="btn btn-info btn-sm" value="Save" style="flex-grow: 0; flex-shrink: 0;">
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
