@extends("layouts.layout")

@section("head")
    <script>
        function patchAttendance(){
            let data = {"absentees": {}};//{"_token": "{{ csrf_token() }}"};
            for(const element of document.getElementsByClassName("form-element"))
                data.absentees[element.name] = element.value;
            fetch("/attendance/{{ $attendance->id }}/", {
                "method": "PATCH",
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                "body": JSON.stringify(data)
            }).then((r) => {
                if(r.ok)
                    alert("OK");
                else
                    alert("FAILED");
            })
        }

        function addNewStudent(){

        }

        const removeStudent = (student_id) => document.getElementById("student-" + student_id).remove();

    </script>
@endsection

@section("content")
    <div>
        Course: <input disabled type="text" value="{{ $attendance->course->subject->name }} | Semester {{ $attendance->course->semester }}">
    </div>
    <div>
        Date: <input disabled type="date" value="{{ $attendance->date }}">
    </div>
    <div>
        Hour: <input disabled type="number" value="{{ $attendance->hour }}">
    </div>
    <h3>Absentees</h3>
        @foreach($attendance->absentees as $absentee)
            <div id="student-{{ $absentee->student->admission_id }}">
                <p>Roll No: {{ $absentee->student->roll_no }}</p>
                <p>Student: {{ $absentee->student->name }}</p>
                <p>
                    Reason for absence: <select class="form-element" name="{{ $absentee->student->admission_id }}">
                        <option value=""
                        @if(!$absentee->duty_leave && !$absentee->medical_leave)
                            selected
                        @endif
                        >No Excuse</option>
                        <option value="medical_leave"
                        @if($absentee->medical_leave)
                            selected
                        @endif
                        >Medical Leave</option>
                        <option value="duty_leave"
                        @if($absentee->duty_leave)
                            selected
                        @endif
                        >Duty Leave</option>
                    </select>
                </p>
                <p><button onclick="removeStudent('{{ $absentee->student->admission_id }}')">Remove Student</button></p>
            </div>
        @endforeach
        <button onclick="patchAttendance()">Confirm Changes</button>
@endsection
