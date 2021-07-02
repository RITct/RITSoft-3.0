@extends("layouts.layout")

@section("head")
    <script>
        function deleteAttendance(url){
            if(confirm("Are you sure, this operation can't be undone"))
                fetch(url, {
                    "method": "DELETE",
                    "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                }).then((r) => {
                    if(r.ok)
                        location.reload();
                    else
                        alert("Failed, are you sure this is your class?");
                })
        }
    </script>
@endsection

@section("content")
    @foreach($attendance as $course_id => $course)
        <h2>
            {{ $course["subject"]["name"] }} -> {{ $course["subject"]["code"] }} -> Semester {{  $course["semester"] }}
        </h2>
        <ul>
            <li>Faculties</li>
            <ul>
                @foreach($course["faculties"] as $faculty)
                    <li>{{ $faculty["name"] }}</li>
                @endforeach
            </ul>
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
                        @if($course["editable"])
                            <li><a href="{{ route("attendance.edit", $period["id"]) }}">Edit</a></li>
                            <li><a href="#" onclick="deleteAttendance('{{ route("attendance.destroy", $period["id"]) }}')">Delete</a></li>
                        @endif
                    </ul>

                </li>

            @endforeach
        </ul>
    @endforeach
    <a href="{{ route("attendance.create") }}">Add</a>
@endsection

