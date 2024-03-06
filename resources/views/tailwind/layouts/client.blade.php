<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'GENESIS')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.png') }}">

    <!-- Icon Font CSS -->
    <link rel="stylesheet" href="/asset/css/plugins/icofont.min.css" />
    <link rel="stylesheet" href="/asset/css/plugins/flaticon.css" />
    <link rel="stylesheet" href="/asset/css/plugins/font-awesome.min.css" />

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('tailwind/app.css') }}">
    
    <!-- JS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
</head>
<body style="background: #F4F4F4;">
<nav class="sticky top-0 z-40">
    <div class="bg-white p-2">
      <div
        class="mx-auto flex max-w-6xl items-center justify-between rounded-2xl border border-green-600 py-2.5 md:py-4 px-5 md:px-6"
      >
        <a href="/" class="flex h-10 items-center justify-center gap-2">
          <img loading="lazy" class="h-full" src="/logo.png" alt="Logo" />
          <h1 class="font-brand text-3xl uppercase">Genesis</h1>
        </a>
        <div class="flex items-center justify-end gap-4">
          <a
            href="/dashboard"
            exact-active-class="active"
            class="rounded-xl border py-2 px-2 text-xl font-semibold uppercase"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </nav>
  <main>
    @yield('content')
  </main>
</body>
</html>