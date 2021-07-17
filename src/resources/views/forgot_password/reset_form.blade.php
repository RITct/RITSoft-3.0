@extends('layouts.layout')
@section('content')
    <div>
        <h1>Change password</h1>
            <form method="post" id="password-reset-form" action="/resetPassword">
                {!! csrf_field() !!}
                <input type="text" placeholder="Email" name="email"><br/>
                <input type="password" placeholder="Password" name="password"><br/>
                <input type="password" placeholder="Confirm Password" name="password_confirmation"><br/>
                <input type="hidden" name="token" value="{{$token}}">
                @error('email')
                 <div class="token-error">
                    {{ $message }}
                </div>   
                @enderror
                <button type="submit">Submit</button>
            </form>
    </div>
@endsection