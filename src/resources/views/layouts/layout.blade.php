<html>
    <head>
        <link rel="stylesheet" href="{{ URL::asset("app.css") }}"/>
        <title>RITSoft | @yield('title')</title>
    </head>
    <body>
        <nav>
            <div id="nav-auth">
                @guest
                    {!! link_to_route("login", "Login") !!}
                @endguest

                @auth
                    <span>You're logged in as {{ Auth::user()->username }}, {!! link_to_route("logout", "Logout") !!}</span>
                @endauth
            </div>
        </nav>
        <div>
            @section('content')
            @show
        </div>
    </body>
</html>
