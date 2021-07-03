@extends("layouts.layout")

@section("head")
    <script>
        const students = {!! json_encode($students) !!};
        let addNewStudentVisible = false;

        function patchAttendance(){
            let data = {"absentees": {}};
            for(const element of document.getElementsByClassName("form-element"))
                data.absentees[element.name] = element.value;

            fetch("{{ route("attendance.update", $attendance->id) }}", {
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
        const getStudentByAdmissionNo = (admission_id) => students.find((student) => student.admission_id === admission_id);

        function studentHTML(admission_id){
            let element = document.createElement("div");
            element.setAttribute("id", `student-${admission_id}`);
            element.innerHTML = `
                <p>Roll No: ${getStudentByAdmissionNo(admission_id).roll_no}</p>
                <p>Student: ${getStudentByAdmissionNo(admission_id).name}</p>
                <p>
                    Reason for absence: <select class=\"form-element\" name=\"${admission_id}\">
                        <option value=\"{{ \App\Enums\LeaveType::NO_EXCUSE }}\" selected>No Excuse</option>
                        <option value=\"{{ \App\Enums\LeaveType::MEDICAL_LEAVE }}\">Medical Leave</option>
                        <option value=\"{{ \App\Enums\LeaveType::DUTY_LEAVE }}\">Duty Leave</option>
                    </select>
        `;
        return element;
    }

        function handleNewStudentClick() {
            if(addNewStudentVisible){
                let student_admission_id = document.getElementById("new_student_create").value;
                if(student_admission_id && document.getElementById(`student-${student_admission_id}`) == null)
                    document.getElementById("absentees").append(studentHTML(student_admission_id));
                else if(document.getElementById(`student-${student_admission_id}`) != null)
                    alert("Student is already absent")
            }

            document.getElementById("new_student_create").classList.toggle("disabled");
            addNewStudentVisible = !addNewStudentVisible;
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
        <div id="absentees">
            @foreach($attendance->absentees as $absentee)
            <div id="student-{{ $absentee->student->admission_id }}">
                <p>Roll No: {{ $absentee->student->roll_no }}</p>
                <p>Student: {{ $absentee->student->name }}</p>
                <p>
                    Reason for absence: <select class="form-element" name="{{ $absentee->student->admission_id }}">
                        <option value="{{ \App\Enums\LeaveType::NO_EXCUSE }}"
                        @if($absentee->leave_excuse == \App\Enums\LeaveType::NO_EXCUSE)
                            selected
                        @endif
                        >No Excuse</option>
                        <option value="{{ \App\Enums\LeaveType::MEDICAL_LEAVE }}"
                        @if($absentee->leave_excuse == \App\Enums\LeaveType::MEDICAL_LEAVE)
                            selected
                        @endif
                        >Medical Leave</option>
                        <option value="{{ \App\Enums\LeaveType::DUTY_LEAVE }}"
                        @if($absentee->leave_excuse == \App\Enums\LeaveType::DUTY_LEAVE)
                            selected
                        @endif
                        >Duty Leave</option>
                    </select>
                </p>
                <p><button onclick="removeStudent('{{ $absentee->student->admission_id }}')">Remove Student</button></p>
                </div>
            @endforeach
        </div>
    <select id="new_student_create" class="disabled">
        @foreach($students as $student)
            <option value={{ $student["admission_id"] }}>{{ $student["name"] }} | {{ $student["admission_id"] }}</option>
        @endforeach
    </select>
    <p>
        <button onclick="handleNewStudentClick()">Add New Absentee</button>
        <button onclick="patchAttendance()">Confirm Changes</button>
    </p>
@endsection
