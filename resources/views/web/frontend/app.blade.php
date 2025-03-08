<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        @include('web.frontend.partials.style')
    </head>
    <body>

        @yield('content')

        @include('web.frontend.partials.script')
    </body>
</html>