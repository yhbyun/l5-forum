<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>
        @section('title')
            Laravel 5 Forum
        @show
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description"
          content="@section('description') Forum built with Laravel 5.1 @show"/>

    <link rel="stylesheet" href="{{ elixir('css/app.css') }}">
    <link rel="shortcut icon" href="/favicon.ico">

    <script>
        Config = {
            'user_id': {{ auth()->check() ? auth()->id() : 0 }},
            'routes': {
                'notificationsCount': '{{ route('notifications.count') }}',
                'upload_image': '{{ route('upload_image') }}'
            },
            'token': '{{ csrf_token() }}',
        };
    </script>

    @yield('styles')

</head>
<body id="body">

    <div id="wrap">
        @include('layouts.partials.nav')
        <div class="container">
            @include('flash::message')
            @yield('content')
        </div>
    </div>

    <div id="footer" class="footer">
        <div class="container small">
            <p class="pull-left">
                <i class="fa fa-heart-o"></i> Made With Love By <a
                        href="http://est-group.org/" style="color:#989898;">The EST
                    Group</a>. <br>
                &nbsp;<i class="fa fa-lightbulb-o"></i> Inspired by v2ex &
                ruby-china.
            </p>

            <p class="pull-right">
            </p>
        </div>
    </div>

    <script src="{{ elixir('js/bundle.js') }}"></script>

    @yield('scripts')

    @if (app()->environment() === 'production')
        @include('layouts.partials.ga')
    @endif
</body>
</html>
