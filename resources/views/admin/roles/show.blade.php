@extends('layouts.app')

@section('content')

    <div id="main" role="main">
        <div id="ribbon">
				<span class="ribbon-button-alignment">
					<span id="refresh" class="btn btn-ribbon" data-action="resetWidgets" data-title="refresh"  rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true">
						<i class="fa fa-refresh"></i>
					</span>
				</span>
            <ol class="breadcrumb">
                <li>Home</li><li>Add User</li>
            </ol>
        </div>

        <div id="content">

            <div class="row">
                <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                    <h1 class="page-title txt-color-blueDark">
                        <i class="fa fa-edit fa-fw "></i>
                        Roles
                        <span>> Add User</span>
                    </h1>
                </div>
            </div>

            @if(Session::has('message'))
                <div class="allert-message alert-success-message pgray  alert-lg" role="alert">
                    <p class=""> {{ Session::get('message') }}</p>
                </div>
        @endif


        <!-- widget grid -->
            <section id="widget-grid" class="">



                <article class="">
                    <div class="jarviswidget" id="wid-id-1" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false">
                        <header>
                            <span class="widget-icon"> <i class="fa fa-edit"></i> </span>
                            <h2>Add Roles </h2>
                        </header>
                        <div>
                            <div class="widget-body no-padding">

                                <div class="row">
                                    <fieldset class="col-md-6">
                                        <div style="padding: 10px" class="box-body">


                                            <div class="form-group">
                                                <label for="name">Name: {{ $user->name }}</label>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email: {{ $user->email }}</label>
                                            </div>

                                            <div class="form-group">
                                                <label for="name">Phone: {{ $user->phone }}</label>
                                            </div>

                                            <div class="form-group">
                                                <label>User Status : @php echo ($user->status==0)?'InActive':(($user->status==1)?'Pending':'Active') @endphp</label>
                                            </div>
                                            <div class="form-group">
                                                <label>Role : {{$user->role_title}}</label>
                                            </div>


                                            <a class="btn btn-primary" href="{{ url('profile-edit',['id'=>Auth::id()]) }}"> Edit</a>


                                        </div>


                                    </fieldset>
                                    <fieldset class="col-md-5">
                                        <section>
                                            <div class="box">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title text-center">Profile Image</h3>
                                                </div>
                                                <div class="box-body text-center">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="max-width: 200px; max-height: 200px;">
                                                            <img src="@if($user->profile_image != ''){{ asset($user->profile_image) }} @else{{ 'http://placehold.it/200x200' }} @endif" width="100%" alt="...">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>



            </section>



        </div>


    </div>
@endsection













