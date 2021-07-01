@extends('layouts.layout')

@section('content')
    <h1>Course Details</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>SEMESTER</th>
            <th>TYPE</th>
            <th>IS_ACTIVE</th>
            <th>FACULTY ID</th>
            <th>FACULTY NAME</th>
            <th>SUBJECT CODE</th>
            <th>SUBJECT NAME</th>
            <th>DEGREE</th>
            <th>SEMESTER</th>
            <th>OPTIONS</th>
        </tr>
        @foreach ($courses as $course)
            @if($course->Classroom->department_code==$department_code or $course->Subject->department_code==$department_code)
            <tr>
                <td>{{$course->id}}</td>
                <td>{{$course->semester}}</td>
                <td>{{$course->type}}</td>
                <td>{{$course->active}}</td>
                <td>{{$course->Faculty->id}}</td>
                <td>{{$course->Faculty->name}}</td>
                <td>{{$course->Subject->code}}</td>
                <td>{{$course->Subject->name}}</td>
                <td>{{$course->Classroom->degree_type}}</td>
                <td>{{$course->Classroom->semester}}</td>
                <td>
                    <form method="post" action="{{route('courses.destroy',['course'=>$course->id])}}">
                        @csrf
                        @method('DELETE')
                        <button >delete</button>
                    </form>
                    <form method="get" action="{{route('courses.edit',['course'=>$course->id])}}">
                        <button >edit</button>
                    </form>
                </td>
            </tr>
            @endif
        @endforeach
        <a href ="{{route('courses.create')}}">Create New Course</a>
        <br>
        <br>



