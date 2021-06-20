@extends("layouts.layout")

@section("content")
    @foreach($data as $department => $faculties)
        <h2>{{ $department }} Faculties</h2>
        @foreach($faculties as $faculty)
            <h3>{{ $faculty["name"] }}</h3>
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
