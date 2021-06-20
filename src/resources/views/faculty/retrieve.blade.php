@extends("faculty.faculty_layout")

@section("content")
    <h1>{{ $faculty->name }}</h1>
    <p>KTU ID: {{ $faculty->id }}</p>
    <p>Email: {{ $faculty->user->email }}</p>
    <p>Phone: {{ $faculty->phone }}</p>
    @if($faculty->editable)
        <a href="/faculty/{{ $faculty->id }}/edit">Edit</a>
    @endif
    @if($faculty->deletable)
        <a href="#" onclick="deleteFaculty('{{ $faculty->id }}')">Delete</a>
    @endif
@endsection
