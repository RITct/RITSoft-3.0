@extends("layouts.layout")

@section("content")
    <form method="post" action="{{ route("faculty.store") }}">
        <input type="text" value="{{ old("id") }}" name="id" placeholder="KTU ID"><br/>
        <input type="text" value="{{ old("name") }}" name="name" placeholder="Name"><br/>
        <input type="text" value="{{ old("email") }}" name="email" placeholder="Email"><br/>
        <input type="text" value="{{ old("phone") }}" name="phone" placeholder="Phone"><br/>
        {!! csrf_field() !!}
        <input type="submit" value="Create">
    </form>
    @if (count($errors) > 0)
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
