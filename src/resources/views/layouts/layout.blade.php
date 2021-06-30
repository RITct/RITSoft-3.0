<html>
    <head>
        <link rel="stylesheet" href="{{ URL::asset("app.css") }}"/>
        <title>RITSoft | @yield('title')</title>
        @section("head")
        @show
    </head>
    <body>
        <nav>
            <div id="nav-auth">
                @guest
                    {{ route("getLogin") }}
                @endguest

                @auth
                    <span>You're logged in as {{ Auth::user()->username }}, {{ route("logout") }}</span>
                @endauth
            </div>
        </nav>
        <div>
            @section('content')
            @show
        </div>
    </body>
</html>
