@extends('tailwind.layouts.admin')

@section('content')
<ul class="grow text-gray-700 overflow-y-auto px-2 print:hidden grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <li class="font-semibold">
        <div class="p-3 border border-dashed bg-sky-300">
            <a href="{{ url('admin') }}"
                class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == 'admin' ? 'text-sky-700' : '' }}">
                <i class="w-5 text-center icon-home"></i>
                <span class="grow menu__title">Dashboard</span>
            </a>
        </div>
    </li>
    @role('Administrator')
    <li class="font-semibold">
        <div class="p-3 border border-dashed bg-sky-300">
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
        </div>
    </li>
    <li class="font-semibold">
        <div class="p-3 border border-dashed bg-sky-300">
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
        </div>
    </li>
    @endrole

    @foreach ($menus as $menu)
    @can($menu->permission)
    <li class="font-semibold">
        <div class="p-3 border border-dashed bg-sky-300">
            <a href="{{ count($menu->submenu) ? '#' : url($menu->url) }}" onclick="submenuToggle(this)"
                class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::is($menu->url . '*') ? 'text-sky-700' : '' }}">
                <i class="w-5 text-center {{ $menu->icon }}"></i>
                <span class="grow menu__title">{{ $menu->title }}</span>
                @if (count($menu->submenu))
                <i class="w-5 text-center icon-arrow-down"></i>
                @endif
            </a>
            @if (count($menu->submenu))
            <ul class="hidden pl-2 py-1">
                @foreach ($menu->submenu as $submenu)
                <li class="pl-2 font-semibold">
                    <a href="{{ count($submenu->thirdmenu) ? '#' : url($submenu->url) }}"
                        onclick="submenuToggle(this)"
                        class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == $submenu->url ? 'text-sky-700' : '' }}">
                        <i class="w-5 text-center {{ $submenu->icon }}"></i>
                        <span class="grow menu__title">{{ $submenu->title }}</span>
                        @if (count($submenu->thirdmenu))
                        <i class="w-5 text-center icon-arrow-down"></i>
                        @endif
                    </a>
                    @if (count($submenu->thirdmenu))
                    <ul class="hidden pl-2 py-1">
                        @foreach ($submenu->thirdmenu as $thirdmenu)
                        <li class="pl-2 font-semibold">
                            <a href="{{ url($thirdmenu->url) }}"
                                class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == $thirdmenu->url ? 'text-sky-700' : '' }}">
                                <i class="w-5 text-center {{ $submenu->icon }}"></i>
                                <span class="grow menu__title">{{ $thirdmenu->title }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </li>
    @endcan
    @endforeach

    @role('Administrator')
    <li class="font-semibold">
        <div class="p-3 border border-dashed bg-sky-300">
            <a href="{{ url('admin/menus') }}"
                class="h-8 flex items-center justify-between gap-2 cursor-pointer {{ Request::path() == 'admin/menus' ? 'text-sky-700' : '' }}">
                <i class="w-5 text-center fa fa-cog"></i>
                <span class="grow menu__title">Menu Settings</span>
            </a>
        </div>
    </li>
    @endrole

</ul>
@endsection