@extends('layouts.layout')


@section('content')
    <h1> Edit Course </h1>
    <form action="{{route('courses.update',['course'=>$course->id])}}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="subject">Select a subject :</label>
            <select name="subject" id="subject">
                @foreach ($subjects as $subject)
                    @if($subject->code == $course->subject_code)
                        <option value="{{$subject->code}}" selected>{{$subject->code}} {{$subject->name}}</option>
                    @else
                    <option value="{{$subject->code}}">{{$subject->code}} {{$subject->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <br>
        <div>
            <label for="faculty">Select a faculty :</label>
            <select name="faculty" id="faculty">
                @foreach ($faculties as $faculty)
                    @if($faculty->id == $course->faculty_id)
                        <option value="{{$faculty->id}}" selected >{{$faculty->id}} {{$faculty->name}}</option>
                    @else
                    <option value="{{$faculty->id}}">{{$faculty->id}} {{$faculty->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <br>
        <div>
            <label for="classroom">Select a classroom :</label>
            <select name="classroom" id="classroom">
                @foreach ($classrooms as $classroom)
                    @if($classroom->id == $course->classroom_id)
                        <option value="{{$classroom->id}}" selected >{{$classroom->degree_type}} Semester {{$classroom->semester}} {{$classroom->department_code}}</option>
                    @else
                    <option value="{{$classroom->id}}">{{$classroom->degree_type}} Semester {{$classroom->semester}} {{$classroom->department_code}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <br>
        <div>
            <label for="type">Select Course Type :</label>
            <select name="type" id="type">
                @foreach ($types as $type)
                    @if($type == $courseType)
                        <option value="{{$type}}" selected>{{$type}} </option>
                    @else
                    <option value="{{$type}}">{{$type}} </option>
                    @endif
                @endforeach
            </select>
        </div>
        <br>
        <div>
            <button>Create Course</button>
        </div>
    </form>
@endsection
