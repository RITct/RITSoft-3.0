@extends("layouts.layout")

@section("content")
    <a href="{{route("roles.index")}}">Roles</a><br/>
    <a href="{{route("users.index")}}">Users</a><br/>
@endsection
