<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8" />
    <title>@php echo (isset($title))?$title:'GENESIS Admin' @endphp</title>
    <link rel="shortcut icon" href="{{ asset('logo.png') }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="MobileOptimized" content="320">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
        type="text/css" />
    <link href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <!-- END GLOBAL MANDATORY STYLES-->
    <!-- BEGIN PAGE LEVEL STYLES -->

    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/plugins/bootstrap-datepicker/css/datepicker.css') }}" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style-conquer.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style-responsive.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/themes/default.css') }}" rel="stylesheet" type="text/css" id="style_color" />
    <link rel="stylesheet" href="{{ asset('assets/css/jasny-bootstrap.min.css') }}">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('js/axios.min.js') }}"></script>

    @yield('styles')

    <style>
        .select2-selection__rendered {
            line-height: 32px !important;
            padding-left: 16px !important;
        }

        .select2-container .select2-selection--single {
            height: 35px !important;
        }

        .select2-selection__arrow {
            height: 34px !important;
        }

        .select2-container--default .select2-selection--single {
            border-color: #e5e5e5;
        }

    </style>

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="page-header-fixed">
    <!-- BEGIN HEADER -->
    <div class="header navbar  navbar-fixed-top">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="header-inner">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="{{ url('admin') }}">

                    <img src="{{ url('logo.png') }}" width="45" height="45">
                    <!--
                <svg width="45px" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 620 621.89"><defs><style>.cls-1{fill:#fff;}.cls-2{fill:none;stroke:#fff;stroke-miterlimit:10;stroke-width:16px;}</style></defs><title>temporeLogoWhite</title><path class="cls-1" d="M675,259.49A280.23,280.23,0,0,0,445.92,655L400,678l-45.92-23a277.15,277.15,0,0,0,25.37-116.61A280.15,280.15,0,0,0,125,259.49L400,122Z" transform="translate(-90 -88.98)"/><polygon class="cls-2" points="612 461.94 310 612.94 8 461.94 8 159.94 310 8.94 612 159.94 612 461.94"/></svg>
                -->
                </a>
            </div>

            <ul class="nav navbar-nav pull-right">

                <li class="devider">
                    &nbsp;
                </li>
                <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                        data-close-others="true">
                        <i class="fa fa-user"></i>
                        <span class="username">Admin </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ url('admin/profile') }}"><i class="fa fa-user"></i> My Profile</a>
                        </li>

                        <li class="divider">
                        </li>
                        <li>
                            <a href="" title="Sign Out"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                    class="fa fa-key"></i> Log Out</a>
                            <form id="logout-form" action="{{ url('admin/logout') }}" method="POST"
                                style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
            </ul>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END TOP NAVIGATION BAR -->
    </div>
    <!-- END HEADER -->
    <div class="clearfix">
    </div>

    <div class="page-container">
        <!-- BEGIN SIDEBAR -->

        <div class="page-sidebar-wrapper">

            <div class="page-sidebar navbar-collapse">
                <!-- BEGIN SIDEBAR MENU -->

                <ul class="page-sidebar-menu" id="myTable" style="max-height: 100vh; overflow-y: auto;">
                    <li class="sidebar-toggler-wrapper">
                        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                        <div class="sidebar-toggler">
                        </div>
                        <div class="clearfix">
                        </div>
                        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->

                    </li>
                    <div class="form-group  " style="margin-left:   10px;margin-right:   10px; margin-top: 8px">
                        <input id="myInput" class="form-control  " style="height: 2.5rem;  " type="text"
                            placeholder="Search..">
                    </div>

                    <li class="start @php echo (Request::segment(2)=='' )?'active':'' @endphp">
                        <a href="{{ url('admin') }}">
                            <i class="icon-home"></i>
                            <span class="title">Dashboard</span>
                        </a>
                    </li>

                    @role('Administrator')
                        <li class="@php echo (Request::segment(2)=='administrator')?'active':''  @endphp">
                            <a href="javascript:;">
                                <i class="icon-users"></i><span class="title">Administrator</span><span
                                    class="arrow "></span>
                            </a>
                            <ul class="sub-menu">

                                <li
                                    class="@php echo (Request::segment(2)=='administrator' && Request::segment(3)=='')?'active':''  @endphp">
                                    <a href="{{ url('admin/administrator') }}">Administrator List</a>
                                </li>

                                <li
                                    class="@php echo (Request::segment(2)=='administrator' && Request::segment(3)=='create')?'active':''  @endphp">
                                    <a href="{{ action('Admin\AdministratorController@create') }}">Add Administrator</a>
                                </li>

                            </ul>
                        </li>

                        <li class="@php echo (Request::segment(2)=='roles')?'active':''  @endphp">
                            <a href="javascript:;">
                                <i class="fas fa-genderless"></i><span class="title">Roles</span><span
                                    class="arrow "></span>
                            </a>
                            <ul class="sub-menu">
                                <li
                                    class="@php echo (Request::segment(2)=='roles' && Request::segment(3)=='')?'active':''  @endphp">
                                    <a href="{{ url('admin/roles') }}">Roles List</a>
                                </li>
                                <li
                                    class="@php echo (Request::segment(2)=='roles' && Request::segment(3)=='create')?'active':''  @endphp">
                                    <a href="{{ action('Admin\RolesController@create') }}">Add Role</a>
                                </li>
                            </ul>
                        </li>
                    @endrole

                    @role('Mentor Controller')
                        <li class="start @php echo (Request::segment(2)=='' )?'active':'' @endphp">
                            <a href="{{ url('admin/mentors') }}">
                                <i class="fa fa-cog"></i>
                                <span class="title">Mentors</span>
                            </a>
                        </li>
                    @endif

                    @foreach ($menus as $menu)
                        @can($menu->permission)
                            <li class="@php echo (Request::path()==$menu->url )?'active':''  @endphp">
                                <a href="{{ url($menu->url) }}">
                                    <i class="{{ $menu->icon }}"></i><span
                                        class="title">{{ $menu->title }}</span><span
                                        class={{ count($menu->submenu) != 0 ? 'arrow ' : '' }}></span>
                                </a>
                                @if (count($menu->submenu))
                                    <ul class="sub-menu">
                                        @foreach ($menu->submenu as $submenu)
                                            <li class="@php echo (Request::path()==$submenu->url)?'active':''  @endphp">
                                                <a href="{{ url($submenu->url) }}"><i
                                                        class="{{ $submenu->icon }}"></i>
                                                    {{ $submenu->title }}</a>
                                                @if (count($submenu->thirdmenu) > 0)
                                                    <ul class="sub-menu">
                                                        @foreach ($submenu->thirdmenu as $thirdmenu)
                                                            <li class="@if (request()->path() == $thirdmenu->url) active @endif">
                                                                <a class="title"
                                                                    style="font-size: 12px; color:#fff3cd"
                                                                    href="{{ url($thirdmenu->url) }}"><i
                                                                        class="fa fa-institution"></i>
                                                                    {{ $thirdmenu->title }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endcan
                    @endforeach

                    @role('Administrator')
                        <li class="start @php echo (Request::segment(2)=='' )?'active':'' @endphp">
                            <a href="{{ url('admin/menus') }}">
                                <i class="fa fa-cog"></i>
                                <span class="title">Menu Settings</span>
                            </a>
                        </li>
                    @endrole

                </ul>
                <!-- END SIDEBAR MENU -->
            </div>
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
                <div class="modal fade" id="portlet-config" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-hidden="true"></button>
                                <h4 class="modal-title">Modal title</h4>
                            </div>
                            <div class="modal-body">
                                Widget settings form goes here
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success">Save changes</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>



                @yield('content')


            </div>

        </div>
        <!-- END CONTENT -->
    </div>


    <div class="footer">
        <div class="footer-inner">
            {{ date('Y') }} &copy; GENESIS.
        </div>
        <div class="footer-tools">
            <span class="go-top">
                <i class="fa fa-angle-up"></i>
            </span>
        </div>
    </div>
    <!-- END FOOTER -->
    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <script src="{{ asset('assets/plugins/jquery-1.11.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jquery-migrate-1.2.1.min.js') }}" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->

    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript">
    </script>

    <script type="text/javascript" src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript"
        src="{{ asset('assets/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('assets/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('assets/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/scripts/app.js') }}"></script>


    @yield('js')
    <script>
        $(document).ready(function() {
            // initiate layout and plugins
            App.init();
        });
    </script>
    <script>
        $(document).ready(function() {

            $('.sub-menu').find('.active').parent().parent().addClass('active');

            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable li").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });

                $("#myTable ul").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > 1)
                });
            });
        });
    </script>
</body>
<!-- END BODY -->

</html>
