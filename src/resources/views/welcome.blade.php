@extends("layouts.layout")

@section("content")
    <a href="{{route("roles.index")}}">Roles</a><br/>
    <a href="{{route("users.index")}}">Users</a><br/>
    <a href="{{route('attendance.index')}}">Attendance</a><br>
    <a href="{{route('subjects.index')}}">Subjects (Login as HOD to see this work)</a><br>
    <a href="{{route('courses.index')}}">Courses (Login as HOD to see this work)</a>
@endsection
