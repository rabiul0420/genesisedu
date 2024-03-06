<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="description" content="Medical Post Graduation Orientation Center">
    <meta name="keywords" content="Genesis, Medical, Post Graduation, Residency, FCPS, BCPS">
    <meta name="author" content="GENESIS">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('meta')

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-161924666-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-161924666-1');
    </script>
    
    <meta name="facebook-domain-verification" content="6183um9h01jbpu5bsym66yffhf6woq" />
    
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '638085847786628');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=638085847786628&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GENESIS')</title>

    <!-- Google Font Embed -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/venobox/1.9.0/venobox.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <!-- Main CSS & Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('css/msi-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/msi-responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('ck-editor-5/style.css') }}">

    <!-- icon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png" sizes="16x16" />

    @yield('css')

    <style>
        .highlight-filter {
            text-decoration: underline;
            background-color: #f9dada;
        }
    </style>

    <!-- Bootstrap Navbar toggle button script -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
    </script>

    <script src="{{ asset('js/axios.min.js') }}"></script>
</head>

<body class="d-flex flex-column" style="min-height: 100vh;background:#EBEEEF;">

    @if (Session::has('login_access_token'))
        <div onclick="this.style.display='none'"
            style="position: fixed; inset: 0; width:100vw; height:100vh; display: flex; justify-content: center; align-items: center; z-index:9999999998; background: #000000cc">
            <div class="bg-danger py-lg-5 py-4 px-lg-5 px-3 rounded-lg" role="alert">
                <h3 class="text-white"> {{ Session::get('login_access_token') }}</h3>
            </div>
        </div>
    @endif
    <!-- ================= Menu Part Start ================== -->
    <nav id="menu_part" class="navbar navbar-expand-lg p-0 main_menu shadow-sm sticky-top">
        <div class="container-lg px-0">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="logo">
                <span id="logo_typing" class="brand_text">GENESIS</span>
                <h5>Post Graduation Medical Orientation Centre</h5>
            </a>
            @guest
            @else
                <a class="nav-link menu_button_phone" href="{{ url('dashboard') }}">
                    <i class="fa fa-user"></i>
                </a>
            @endguest
            <button class="navbar-toggler mix_solve" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fa fa-bars menu_bar"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::segment(1) == '' || Request::segment(1) == 'login' || Request::segment(1) == 'register' ? 'active' : '' }}"
                            href="{{ url('/') }}">Home</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ Request::segment(1) == 'aboutus' ? 'active' : '' }}"
                            href="{{ url('aboutus') }}">About</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link {{ Request::segment(1) == 'course' ? 'active' : '' }}"
                            href="{{ url('course') }}">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::segment(1) == 'batch' ? 'active' : '' }}"
                            href="{{ url('batch') }}">Batch</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ Request::segment(1) == 'gallery' ? 'active' : '' }}"
                            href="{{ url('gallery') }}">Gallery</a>
                    </li> --}}
                    {{-- <li class="nav-item">

                        <a class="nav-link" type="button" data-toggle="modal" data-target="#exampleModal7">Success
                            List</a>

                        <a class="nav-link {{Request::segment(1)=='success-list'?'active':''}}" href="{{ url('success-list') }}">Success List</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link {{ Request::segment(1) == 'complain' ? 'active' : '' }}"
                            href="{{ url('complain') }}">Complain Box</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::segment(1) == 'faq' ? 'active' : '' }}"
                            href="{{ url('faq') }}">FAQS</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ Request::segment(1) == 'contactus' ? 'active' : '' }}"
                            href="{{ url('contactus') }}">Contact</a>
                    </li> --}}

                    @if (Auth::guard('doctor')->check())
                        <li class="nav-item">
                            <a class="nav-link menu_button" href="{{ url('dashboard') }}">Profile</a>

                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link menu_button" href="{{ url('/') }}">Log In</a>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal -->

    <div class="modal fade" id="exampleModal7" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="display: block;text-align:center;">
                    <h5 class="modal-title" style="text-align: center" id="exampleModalLabel">WELCOME</h5>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>Dear Doctor,</h6>
                    <p style="">Congratulations for passing FCPS Part-1 examination in this July 2021 session.
                    </p><br>
                    <p>Finally you have made it.</p><br>
                    <p>GENESIS is so happy & proud with your outstanding success.</p>
                </div>
                <div class="modal-footer">
                    <a type="submit"
                        class="btn btn-primary {{ Request::segment(1) == 'success-list' ? 'active' : '' }}"
                        href="{{ url('success-list') }} ">Get Started</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= Menu Part End ================= -->

    <main>
        @yield('content')
    </main>


    <!-- ================= Footer Start ================= -->
    <footer class="mt-auto py-md-4 py-1">
        <div class="social_link py-md-5 py-2">
            <a target="_blank" href="https://www.facebook.com/genesispgentrance/"><i
                    class="fa fa-facebook-f"></i></a>
            <!-- <a target="_blank" href=""><i class="fab fa-youtube"></i></a> -->
            <a target="_blank" href="https://www.facebook.com/groups/genesis.pg/"><i
                    class="fa fa-facebook-f"></i></a>
            <!-- <a target="_blank" href=""><i class="fab fa-youtube"></i></a> -->
        </div>
        <div class="container">
            <div class="footer-blog">
                <div class="row">
                    <div class="col-4 col-sm-4">
                        <p class="about1 text-white ">ABOUT</p>
                        <ul class="list">
                            <li>
                                <a href="{{ url('/') }}"
                                    class="text-white {{ Request::segment(1) == '' || Request::segment(1) == 'login' || Request::segment(1) == 'register' ? 'active' : '' }} ">Home</a>
                            </li>
                            <li>
                                <a href="{{ url('aboutus') }}"
                                    class="text-white {{ Request::segment(1) == 'aboutus' ? 'active' : '' }}">About
                                    Us</a>
                            </li>

                            <li>
                                <a href="" class="text-white">Testimonial</a>
                            </li>

                            <li>
                                <a href="" class="text-white">Blog</a>
                            </li>

                            <li>
                                <a href="{{ url('gallery') }}"
                                    class="text-white {{ Request::segment(1) == 'gallery' ? 'active' : '' }}">Gallary</a>
                            </li>

                            <li>
                                <a href="{{ url('faq') }}"
                                    class="text-white {{ Request::segment(1) == 'faq' ? 'active' : '' }}">FAQS</a>
                            </li>


                        </ul>
                    </div>
                    <div class="col-4 col-sm-4">

                        <p class="about1 text-white">QUICK LINKS</p>

                        <ul class="list">
                            <li><a href="https://gcpsc.info/" class="text-white">Counselling</a></li>
                            
                            <li><a href="" class="text-white">Our Services</a></li>

                            <li>
                                <a href="{{ url('course') }}"
                                    class="text-white {{ Request::segment(1) == 'course' ? 'active' : '' }}">Courses</a>
                            </li>
                            <li>
                                <a href="{{ url('batch') }}"
                                    class="text-white {{ Request::segment(1) == 'batch' ? 'active' : '' }}">Batch</a>
                            </li>

                            <li>
                                <a href="{{ route('terms-condition') }}" class="text-white">terms and
                                    conditions</a>
                            </li>
                            <li>
                                <a href="{{ route('refund-policy') }}" class="text-white">refund policy</a>
                            </li>
                            <li>
                                <a href="{{ url('privacy-policy') }}" class="text-white">Privacy Policy</a>
                            </li>

                        </ul>
                    </div>
                    <div class="col-4 col-sm-4">
                        <p class="about1 text-white ">CONTACT</p>
                        <ul class="text-white">
                            <li class="list">
                                <a href="{{ url('contactus') }}"
                                    class="text-white text-sm-left{{ Request::segment(1) == 'contactus' ? 'active' : '' }}">Contact
                                    </a>
                            </li>
                            <li class="list3">230, New Elephant Road (4th floor)
                                West Side of Katabon More <br>
                                Dhaka-1205
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container border-top pt-md-4 pt-2">
            <div class="row">
                <div class="col-lg-6">
                    <p>&copy; 2012 - {{ date('Y') }} <a href="{{ url('/') }}">GenesisEdu.info</a></p>
                </div>
                <div class="col-lg-6">
                    <p>Developed by <a href="http://www.medigeneit.com">MedigeneIT.com</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- ================= Footer End ================= -->

    {{-- ok --}}
    <!-- ================= JavaScript =================
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <!-- Popper first then Bootstrap JS V5 -->
    {{-- <script src="{{ asset('js/popper.min.js') }}"></script> --}}
    {{-- <script src="{{asset('js/bootstrap.min.js')}}"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>

    <!-- jQuery & Waypoints -->
    <script src="{{ asset('js/jquery-2.1.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.waypoints.min.js') }}"></script>


    <!-- PlugIns & Others Script -->
    <script src="{{ asset('js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('js/msi-typing.min.js') }}"></script>
    <script src="{{ asset('js/slick.min.js') }}"></script>

    <!-- Main Script -->
    <script defer src="{{ asset('js/script.js') }}"></script>
    <!-- ~~~~~~~~~~~~~~~~ JS End ~~~~~~~~~~~~~~~~ -->

    @yield('js')

    @guest('doctor')
    <script>
        localStorage.setItem('read_modal_add', '')
    </script>
    @endguest

</body>

</html>
