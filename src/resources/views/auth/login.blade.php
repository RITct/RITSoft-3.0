@extends('layouts.layout')

@section('content')
    <div>
        <h1>Login</h1>
        <form method="POST" action="/auth/login">
            {!! csrf_field() !!}
            <input type="text" placeholder="Username" name="username"><br/>
            <input type="password" placeholder="Password" name="password"><br/>
            <input type="submit" value="Login">
        </form>
    </div>
@endsection
