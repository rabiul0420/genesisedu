@extends('layouts.app')
@section('content')
    <style>
        .tablink {
            background-color: #208a39;
            height: 35px;
            border: none;
            color: rgb(255, 255, 255);
            border-radius: 25px;
            padding: 8px 25px;
            margin: 2px 5px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
        
        a.tablink:hover {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            background: #208a39 !important;
            color: rgb(255, 255, 255) !important;
        }

        @media only screen and (max-width: 600px) {
            .tablink {
                /* background-color: #28a745; */
                height: 25px;
                border: none;
                color: rgba(239, 241, 236, 0.911);
                border-radius: 25px;
                padding: 8px 7px;
                margin: 0px 0px;
                font-size: 15px;
                display: inline-flex;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
    
    <section id="batch_part">
        <div class="container px-sm-0">
            <div class="big-demo" data-js="hero-demo">
                <div class="row">
                    <div class="col py-2">
                        {{-- <h2>Available Batches</h2> --}}
                        <a class="btn btn-success rounded-pill" href="{{ url('batch') }}"><h1>Available Batches</h1></a>
                    </div>
                </div>

                @if(Session::has('message'))
                    <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
                        <p> {{ Session::get('message') }}</p>
                    </div>
                @endif

                <div class="tab_view">
                    @foreach( App\AvailableBatches::getCourselink() as $key => $courseName  )
                    <a class="tablink" href="{{$courseName['link']}}">{{$courseName['name']}}</a>                  
                    @endforeach

                    <div class="form-group col-md-2 mx-auto mt-2">
                        <div class="controls">
                            <input type="text"  size="20" class="form-control" oninput="search(this.value)"  name="" placeholder="Batch Search ...">
                        </div>
                    </div>

                    <div id="availableBatchContainer">
                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function search(text = '') {
            let url = `{{ \Request::getRequestUri() }}`;
            axios.get(url, {
                params: {
                    flag: true,
                    search: text,
                }
            })
            .then(function (response) {
                document.getElementById("availableBatchContainer").innerHTML = response.data;
                // console.log(response);
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            });
        }

        search();

    </script>
@endsection
