@extends("layouts.layout")

@section("content")
    <form method="post" action="/faculty">
        <input type="text" value="{{ old("id") }}" name="id" placeholder="KTU ID"><br/>
        <input type="text" value="{{ old("name") }}" name="name" placeholder="Name"><br/>
        <input type="text" value="{{ old("email") }}" name="email" placeholder="Email"><br/>
        <input type="text" value="{{ old("phone") }}" name="phone" placeholder="Phone"><br/>
        <input type="submit" value="Create">
    </form>
@endsection
