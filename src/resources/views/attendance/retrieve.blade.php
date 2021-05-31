@extends("layouts.layout")

@section("content")
    <h1>Attendance View</h1>
    @foreach($attendance as $day)
        <div>
            <h3>{{$day->course->subject->name}} | {{$day->course->subject->code}}</h3>
            Date: {{ $day->date }}<br/>
            Hour: {{ $day->hour }}<br/>
            @if($day->absent)
                Absent
            @else
                Present
            @endif
        </div>
    @endforeach
@endsection
