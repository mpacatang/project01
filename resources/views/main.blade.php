<!DOCTYPE html>
<html>
    <head>
        <title>Yuup | Photo sharing script</title>
        <meta charset="utf-8">
        <meta content="ie=edge" http-equiv="x-ua-compatible">
        <meta content="Yuup | Photo sharing script" name="keywords">
        <meta content="Yuup | Photo sharing script" name="author">
        <meta content="Yuup | Photo sharing script" name="description">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <link href="{{ asset('front/images/favicon.png') }}" rel="shortcut icon">
        <link href="{{ asset('front/images/favicon.png') }}" rel="apple-touch-icon">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="client-id" content="{{ env('CLIENT_ID') }}" />
        <meta name="client-secret" content="{{ env('CLIENT_SECRET') }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link rel="stylesheet" href="{{ mix('front/css/assets.css') }}"/>
    </head>
    <body>
        <div id="main">
            <router-view></router-view>
        </div>
        <script type="text/javascript" src="{{ mix('front/js/assets.js') }}"></script>
    </body>
</html>
