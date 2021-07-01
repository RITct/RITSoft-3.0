@extends('layouts.layout')


@section('content')
<h1> Create New Course </h1>
<form action="{{route('courses.store')}}" method="POST">
    @csrf
    <div>
        <label for="subject">Select a subject :</label>
        <select name="subject" id="subject">
            @foreach ($subjects as $subject)
                <option value="{{$subject->code}}">{{$subject->code}} {{$subject->name}}</option>
            @endforeach
        </select>
        <a href ="{{route('subjects.create')}}">Add New Subject</a>
    </div>
    <br>
    <div>
        <label for="faculty">Select a faculty :</label>
        <select name="faculty" id="faculty">
            @foreach ($faculties as $faculty)
                <option value="{{$faculty->id}}">{{$faculty->id}} {{$faculty->name}}</option>
            @endforeach
        </select>
    </div>
    <br>
    <div>
        <label for="classroom">Select a classroom :</label>
        <select name="classroom" id="classroom">
            @foreach ($classrooms as $classroom)
                <option value="{{$classroom->id}}">{{$classroom->degree_type}} Semester {{$classroom->semester}} {{$classroom->department_code}}</option>
            @endforeach
        </select>
    </div>
    <br>
    <div>
        <label for="type">Select Couse Type :</label>
        <select name="type" id="type">
            @foreach ($types as $type)
                <option value="{{$type}}">{{$type}} </option>
            @endforeach
        </select>
    </div>
    <br>
    <div>
      <button>Create Course</button>
    </div>
  </form>
@endsection
