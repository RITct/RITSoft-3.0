<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ URL::asset("app.css") }}"/> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student | @yield('title') </title>
</head>
<body>
    <nav>
        <div id="nav-auth">
            <a href={{route('students.dashboards')}}>DashBoard</a>
            <a href={{route('students.attendance')}}>Attendance</a>
            <a href={{route('students.facultyEvaluvations')}}>Faculty Evaluvation</a>
            <a href={{route('students.semRegistrations')}}>Semester Registration</a>
            <a href={{route('students.seriesMarks')}}>Series Marks</a>
            <a href={{route('students.sessionMarks')}}>Session Marks</a>
            <a href={{route('students.universityMarks')}}>University Marks</a>
            <a href={{route('students.photos')}}>Photo</a> 
            

            @auth
                <span>You're logged in as {{ Auth::user()->email }}, {!! link_to_route("logout", "Logout") !!}</span>
            @endauth
        </div>
    </nav>
   @yield('content')
</body>
</html>