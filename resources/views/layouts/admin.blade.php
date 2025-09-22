<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','Admin Panel')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    @stack('css')
</head>
<body>
    <div class="dashboard">

        <!-- @if (!Request::is('admin/quotes/*'))
        @endif -->
        @include('partials.admin.sidebar')
        
        @yield('content')
        
    </div>
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script>
        @if(session('message'))
            toastr.success("{{ session('message') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
    @stack('scripts')
    
</body>
</html>