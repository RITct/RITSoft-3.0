<html>
    <head>
        <link rel="stylesheet" href="{{ URL::asset("app.css") }}"/>
        <title>RITSoft | @yield('title')</title>
    </head>
    <body>
        <nav>
            <div id="nav-auth">
                @guest
                    <a href="/auth/login">Login</a>
                @endguest

                @auth
                    <span>You're logged in as {{ Auth::user()->email }}</span>
                @endauth
            </div>
        </nav>
        <div>
            @section('content')
            @show
        </div>
    </body>
</html>
