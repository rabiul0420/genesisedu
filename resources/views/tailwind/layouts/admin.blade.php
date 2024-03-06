<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Admin Panel')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.png') }}">

    <!-- Tailwind -->
    <link rel="stylesheet" href="{{ asset('tailwind/app.css') }}">

    <!-- CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    @yield('header')

    <style>
        [x-cloak] {
            display: none;
        }

    </style>
</head>

<body class="">
    <div class="w-screen h-screen flex" x-data="{ navigationTrigger: false }">
        <nav x-bind:class="navigationTrigger ? 'flex lg:hidden' : 'hidden lg:flex'"
            class="shrink-0 bg-gray-50 flex-col w-full lg:w-auto">
            <div class="shrink-0 grow-0 bg-sky-600 z-30 px-2 h-14 flex items-center gap-2 lg:px-3 print:hidden">
                <a href="/admin" class="w-10 h-10 bg-white rounded-full p-1">
                    <img src="{{ asset('logo.png') }}" class="w-full h-full mt-px" alt="Logo">
                </a>
                <input id="navigationSearch" class="px-3 py-1 rounded-md focus:outline-none border shadow grow"
                    type="text" placeholder="Search..." autocomplete="off">
                <div @click="navigationTrigger = !navigationTrigger"
                    class="lg:hidden w-8 border rounded text-center text-xl cursor-pointer bg-white text-sky-600">&#9776;
                </div>
            </div>
            <ul class="grow bg-white overflow-y-auto px-2 print:hidden border-r" id="navigationMenu">
                <li class="pl-2 font-semibold">
                    <a href="{{ url('admin') }}"
                        class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == 'admin' ? 'text-sky-700' : '' }}">
                        <i class="w-5 text-center icon-home"></i>
                        <span class="grow menu__title">Dashboard</span>
                    </a>
                </li>
                @role('Administrator')
                    <li class="pl-2 font-semibold">
                        <a onclick="submenuToggle(this)" class="h-8 flex items-center justify-between gap-2 cursor-pointer">
                            <i class="w-5 text-center icon-people"></i>
                            <span class="grow menu__title">Administrator</span>
                            <i class="w-5 text-center icon-arrow-down"></i>
                        </a>
                        <ul class="hidden pl-2 py-1">
                            <li class="pl-2 font-semibold">
                                <a href="{{ url('admin/administrator') }}"
                                    class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == 'admin/administrator' ? 'text-sky-700' : '' }}">
                                    <i class="w-5 text-center icon-people"></i>
                                    <span class="grow menu__title">Administrator List</span>
                                </a>
                            </li>
                            <li class="pl-2 font-semibold">
                                <a href="{{ action('Admin\AdministratorController@create') }}"
                                    class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == 'admin/administrator/create' ? 'text-sky-700' : '' }}">
                                    <i class="w-5 text-center icon-plus"></i>
                                    <span class="grow menu__title">Add Administrator</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="pl-2 font-semibold">
                        <a onclick="submenuToggle(this)" class="h-8 flex items-center justify-between gap-2 cursor-pointer">
                            <i class="w-5 text-center icon-people"></i>
                            <span class="grow menu__title">Roles</span>
                            <i class="w-5 text-center icon-arrow-down"></i>
                        </a>
                        <ul class="hidden pl-2 py-1">
                            <li class="pl-2 font-semibold">
                                <a href="{{ url('admin/roles') }}"
                                    class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == 'admin/administrator' ? 'text-sky-700' : '' }}">
                                    <i class="w-5 text-center icon-people"></i>
                                    <span class="grow menu__title">Role List</span>
                                </a>
                            </li>
                            <li class="pl-2 font-semibold">
                                <a href="{{ action('Admin\RolesController@create') }}"
                                    class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == 'admin/administrator/create' ? 'text-sky-700' : '' }}">
                                    <i class="w-5 text-center icon-plus"></i>
                                    <span class="grow menu__title">Add Role</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole


                @role('Mentor Controller')
                    <li class="pl-2 font-semibold">
                        <a href="{{ url('admin/mentors') }}"
                            class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::is('mentors.*') ? 'text-sky-700' : '' }}">
                            <i class="w-5 text-center fa fa-cog"></i>
                            <span class="grow menu__title">Mentors</span>
                        </a>
                    </li>
                @endif

                @foreach ($menus as $menu)
                    @can($menu->permission)
                        <li class="pl-2 font-semibold">
                            <a href="{{ count($menu->submenu) ? '#' : url($menu->url) }}" onclick="submenuToggle(this)"
                                class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::is($menu->url . '*') ? 'text-sky-700' : '' }}">
                                <i class="w-5 text-center {{ $menu->icon }}"></i>
                                <span class="grow menu__title">{{ $menu->title }}</span>
                                @if (count($menu->submenu))
                                    <i class="w-5 text-center icon-arrow-down"></i>
                                @endif
                            </a>
                            @if (count($menu->submenu))
                                <ul class="hidden pl-2 py-1 __child__container__{{ $menu->id }}">
                                    @foreach ($menu->submenu as $submenu)
                                        @if(Request::is($submenu->url . '*'))
                                        <script>
                                            document.querySelector('.__child__container__{{ $menu->id }}').classList.remove('hidden')
                                        </script>
                                        @endif
                                        <li class="pl-2 font-semibold">
                                            <a href="{{ count($submenu->thirdmenu) ? '#' : url($submenu->url) }}"
                                                onclick="submenuToggle(this)"
                                                class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::is($submenu->url . '*') ? 'text-sky-700' : '' }}">
                                                <i class="w-5 text-center {{ $submenu->icon }}"></i>
                                                <span class="grow menu__title">{{ $submenu->title }}</span>
                                                @if (count($submenu->thirdmenu))
                                                    <i class="w-5 text-center icon-arrow-down"></i>
                                                @endif
                                            </a>
                                            @if (count($submenu->thirdmenu))
                                                <ul class="hidden pl-2 py-1 __child__container__{{ $submenu->id }}">
                                                    @foreach ($submenu->thirdmenu as $thirdmenu)
                                                    @if(Request::is($thirdmenu->url . '*'))
                                                    <script>
                                                        document.querySelector('.__child__container__{{ $menu->id }}').classList.remove('hidden')
                                                    </script>
                                                    @endif
                                                    <li class="pl-2 font-semibold">
                                                        <a href="{{ url($thirdmenu->url) }}"
                                                            class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::is($thirdmenu->url . '*') ? 'text-sky-700' : '' }}">
                                                            <i class="w-5 text-center {{ $submenu->icon }}"></i>
                                                            <span
                                                                class="grow menu__title">{{ $thirdmenu->title }}</span>
                                                        </a>
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
                    <li class="pl-2 font-semibold">
                        <a href="{{ url('admin/menus') }}"
                            class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == 'admin/menus' ? 'text-sky-700' : '' }}">
                            <i class="w-5 text-center fa fa-cog"></i>
                            <span class="grow menu__title">Menu Settings</span>
                        </a>
                    </li>
                @endrole

            </ul>

            <div class="text-center bg-sky-600 text-white py-3 print:hidden">
                &copy; {{ date('Y') }} genesisedu.info
            </div>
        </nav>
        <div class="shrink grow overflow-auto print:overflow-visible">
            <header class="w-full z-30 px-4shadow h-14 flex items-center bg-sky-600 px-2 lg:px-3 print:hidden sticky top-0">
                <div class="w-full flex justify-between">
                    <div @click="navigationTrigger = !navigationTrigger"
                        x-bind:class="navigationTrigger ? '' : 'lg:-ml-2'"
                        class="w-8 border rounded text-center text-xl cursor-pointer z-40 bg-white text-sky-600">&#9776;
                    </div>
                    @if ($admin_name = auth()->user()->name ?? 'Admin')
                        <div>
                            <div onclick="submenuToggle(this)"
                                class="flex items-center gap-2 px-2 cursor-pointer text-white">
                                <div
                                    class="w-7 h-7 rounded-full flex items-center justify-center bg-white text-sky-600 text-lg">
                                    {{ mb_strtoupper(mb_substr($admin_name, 0, 1, 'utf-8'), 'utf-8') }}
                                </div>
                                <div class="hidden lg:block">
                                    {{ mb_convert_case(mb_strtolower($admin_name, 'utf-8'), MB_CASE_TITLE, 'UTF-8') }}
                                </div>
                                <i class="text-xs text-center icon-arrow-down"></i>
                            </div>
                            <div class="hidden relative z-40">
                                <ul
                                    class="absolute min-w-max top-2 right-2 bg-gray-100 shadow rounded px-4 py-3 grid gap-2">
                                    <a class="text-left flex gap-2 items-center" href="{{ url('admin/profile') }}">
                                        <i class="fa fa-user"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <hr>
                                    <button type="button" onclick="window.print()"
                                        class="text-left flex gap-2 items-center text-gray-700">
                                        <i class="fa fa-print"></i>
                                        <span>Print</span>
                                    </button>
                                    <hr>
                                    <form class="block" action="{{ url('admin/logout') }}" method="POST">
                                        {{ csrf_field() }}
                                        <button type="submit" class="text-left flex gap-2 items-center text-red-500">
                                            <i class="fa fa-key"></i>
                                            <span>Log Out</span>
                                        </button>
                                    </form>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </header>
            <main class="p-2 md:p-4 w-full print:block bg-gray-100">
                @if ($errors->any())
                    <div
                        class="w-full bg-red-200 p-4 text-red-600 mb-4 flex justify-between items-start gap-2 lg:gap-4">
                        <div onclick="this.parentElement.classList.add('hidden')"
                            class="-mt-1 grow-0 text-2xl cursor-pointer">&times;</div>
                        <ul class="grow text-left pt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(Session::has('message'))
                <div class="px-3 py-2 rounded bg-cyan-100 text-cyan-600 mb-4">
                    {{ Session::get('message') }}
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function submenuToggle(parrent) {
            let wrapper = parrent.nextElementSibling;

            if (wrapper) {
                wrapper.classList.toggle('hidden');
            }
        }

        document.getElementById("navigationSearch").addEventListener("keyup", function() {
            const value = this.value.trim().toLowerCase();

            function searchItem(selectors, reference) {
                Object.values(document.querySelectorAll(selectors)).forEach((item) => {
                    if (item.textContent.toLowerCase().indexOf(value) > reference) {
                        return item.classList.remove('hidden');
                    }
                    return item.classList.add('hidden');
                })
            }

            searchItem("#navigationMenu li", -1);

            searchItem("#navigationMenu ul", 1);
        });
    </script>

    @yield('footer')
</body>

</html>
