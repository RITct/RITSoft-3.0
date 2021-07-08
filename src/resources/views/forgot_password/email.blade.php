@extends('layouts.layout')
@section('content')
    <div>
        <h2>Enter your Email id:</h2    >
            <form method="post" id="email-form" action="{{ route('password.request') }}">
                {!! csrf_field() !!}
                <input type="text" placeholder="Email" name="email" value="{{ old('email') }}"><br/>

                @error('email')
                 <div class="email-error">
                    {{ $message }}
                </div>   
                @enderror
                <button type="submit">Submit</button>
            </form>
            <div class="message">
                @if (session('status'))
                    <div>{{ session('status') }}</div>
                @endif 
            </div>
    </div>
@endsection