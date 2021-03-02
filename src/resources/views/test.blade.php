<!Doctype html>
<html>
    <head>
        <title>Log In Test</title>
    </head>
    <body>
        <h1>Only authenticated users can see this</h1>
        <p>
            Authenticated user's name: {{ Auth::user()->name }}
        </p>
    </body>
</html>
