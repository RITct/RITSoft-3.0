@extends("layouts.layout")

@section("content")
    <h1>Attendance View</h1>
    @foreach($attendance as $period)
        <div>
            <h3>{{$period->course->subject->name}} | {{$period->course->subject->code}}</h3>
            Date: {{ $period->date }}<br/>
            Hour: {{ $period->hour }}<br/>
            Faculties:
                <ul>
                    @foreach($period->course->faculties as $faculty)
                        <li>{{ $faculty->name }}</li>
                    @endforeach
                </ul>
            Status: @if($period->absent)
                Absent
                <br/>Medical Leave: {{ ($period->medical_leave) ? "True" : "False" }}
                <br/>Duty Leave: {{ $period->duty_leave ? "True" : "False"}}<br/>
            @else
                Present<br/>

            @endif

            @if($period->editable)
                <a href="#" onclick="alert('Ithu implement cheyyanm mister')">Edit</a>
            @endif
        </div>
    @endforeach
@endsection
