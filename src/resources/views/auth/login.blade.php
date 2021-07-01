@extends('layouts.layout')
@section('content')
    <div>
        <h1>Login</h1>
            <form method="post" id="login-form" action="{{ route("login.post") }}">
                {!! csrf_field() !!}
                <input type="text" placeholder="Username" name="username"><br/>
                <input type="password" placeholder="Password" name="password"><br/>
                <button type="submit">Login</button>
            </form>
    </div>
@endsection
