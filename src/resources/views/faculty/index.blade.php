@extends("faculty.faculty_layout")

@section("content")
    @foreach($data as $department => $faculties)
        <h2>{{ $department }} Faculties</h2>
        @foreach($faculties as $faculty)
            <h3>{{ $faculty["name"] }}</h3>
            @if($faculty->editable)
                <p><a href="/faculty/{{ $faculty->id }}/edit">Edit</a></p>
            @endif
            @if($faculty->deletable)
                <p><a href="#" onclick="deleteFaculty('{{ $faculty->id }}')">Delete</a></p>
            @endif
            <h5>Courses</h5>
                <ul>
                    @foreach($faculty["courses"] as $course)
                        <li>{{ $course["subject"]["name"] }} | {{ $course["semester"] }}</li>
                    @endforeach
                </ul>
                @if(count($faculty["courses"]) == 0)
                    Not currently taking courses(chumma irikkuaann)
                @endif
        @endforeach
    @endforeach
@endsection
