@extends("layouts.layout")

@section("content")
    <a href="{{route("roles.index")}}">Roles</a><br/>
    <a href="{{route("users.index")}}">Users</a><br/>
    <a href="{{route('attendance.index')}}">Attendance</a>
    <a href="{{route('faculty.index')}}">Faculty</a>
@endsection
