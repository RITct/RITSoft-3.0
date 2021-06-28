@extends("layouts.layout")

@section("content")
    <form method="POST" action="/testrequest">
        <input type="text" name="name" placeholder="Name"/><br/>
        <input type="submit" value="Submit">
        {{ csrf_field() }}
    </form>
@endsection
