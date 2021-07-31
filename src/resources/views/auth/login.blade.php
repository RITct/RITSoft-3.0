@extends('layouts.layout')
@section('content')
    <div>
        <h1>Login</h1>
            <form method="post" id="login-form" action="{{ route("login.post") }}">
                {!! csrf_field() !!}
                <input type="text" placeholder="Username" name="username"><br/>
                <input type="password" placeholder="Password" name="password"><br/>
                <button type="submit">Login</button>
                <a href="{{ route('password.email') }}">Forgot Password?</a>
            </form> 
            <div class="message">
                @if (session('status'))
                    <div>{{ session('status') }}</div>
                @endif 
            </div>
            <div class="errors">
                @error('username')
                 <div class="email-error">
                    {{ $message }}
                </div>   
                @enderror
            </div>

    </div>
@endsection
