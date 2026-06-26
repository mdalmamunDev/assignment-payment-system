<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>FunTan | Admin</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="stylesheet" href="{{ asset('vue-app/css/tailwind.css') }}">
    </head>
    <body class="dark:bg-gray-900 dark:text-white">
        <div id="app">
            <App />
        </div>

        <script>
            window.baseUrl  = "{{ url('/') }}";
            window.authUser = @json(auth()->user());
        </script>
        <script src="{{ asset('vue-app/backend/js/app.js') }}"></script>
    </body>
</html>