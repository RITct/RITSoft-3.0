@extends("layouts.layout")

@section("content")
    <form method="post" action="/attendance">
        <select name="course_id">
            <option>Choose Course</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->subject->name }} | Sem {{ $course->semester }}</option>
            @endforeach
        </select>
        {!! csrf_field() !!}
        <br/><br/>Date: <input type="date" name="date" value="{{ old('date') }}">
        <br/><br/><input type="number" name="hour" placeholder="Hour" value="{{ old('hour') }}">
        <br/><br/><input type="text" name="absentee_admission_nums" value="{{ old('absentee_admission_nums') }}" placeholder="Absentee Admission No(CSV)">
        <br/><br/><input type="submit" value="Submit">
    </form>
@endsection
