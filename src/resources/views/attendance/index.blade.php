@extends("layouts.layout")

@section("content")
    @foreach($attendance as $course_id => $course)
        <h2>
            {{ $course["subject"]["name"] }} -> {{ $course["subject"]["code"] }} -> Semester {{  $course["semester"] }}
        </h2>
        <ul>
            <li>Faculty: {{ $course["faculty"]["name"] }}</li>
            @foreach($course["attendances"] as $period)
                <li>
                    <ul>
                        <li>Date: {{ $period["date"] }}</li>
                        <li>Hour: {{ $period["hour"] }}</li>
                        <li>Absentees
                            @if(count($period["absentees"]) == 0)
                                : None
                            @endif
                            @foreach($period["absentees"] as $absentee)
                                <ul>
                                    <li>Name: {{ $absentee["student"]["name"] }}</li>
                                    <li>Medical Leave: {{ $absentee["medical_leave"] ? "Yes" : "No" }}</li>
                                    <li>Duty Leave: {{ $absentee["duty_leave"] ? "Yes" : "No" }}</li>
                                </ul>
                            @endforeach
                        </li>
                    </ul>
                </li>
            @endforeach
            @if($course["editable"])
                <li><a href="{{ route("attendance.create") }}">Add</a></li>
            @endif
        </ul>
    @endforeach
@endsection

