<!DOCTYPE html>
<html lang="nl">
    <head>
        <title>Hezelaer</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="David van der Tuijn">
        @yield('metatags')
        @yield('stylesheets')
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        @yield('scripts')
    </head>
    <body>
        @yield('content')
    </body>
</html>