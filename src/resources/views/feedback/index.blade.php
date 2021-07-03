@extends("layouts.layout")

@section("content")
    @foreach($feedbacks as $feedback)
        <p>{{ json_encode($feedback) }}</p>
    @endforeach
@endsection
