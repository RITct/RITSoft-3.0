@extends("layouts.layout")

@section("head")
@endsection

@section("content")
    <form enctype="multipart/form-data" action="{{ route("uploadPhoto.store") }}" method="POST">
        <input type="file" name="image">
        <input type="submit">
        {!! csrf_field() !!}
    </form>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
