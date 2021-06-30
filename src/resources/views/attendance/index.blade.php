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
                        @if($course["editable"])
                            <li><a href="{{ route("editAttendance", $period["id"]) }}">Edit</a></li>
                            <li><a href="#" onclick="deleteAttendance('{{ route("destroyAttendance", $period["id"]) }}')">Delete</a></li>
                        @endif
                    </ul>

                </li>

            @endforeach
        </ul>
    @endforeach
    <a href="{{ route("createAttendance") }}">Add</a>
@endsection

