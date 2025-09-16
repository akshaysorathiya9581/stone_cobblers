<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','Frontend')</title>
    @include('partials.frontend.header')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
    @stack('css')
</head>
<body>
    @yield('content')
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>