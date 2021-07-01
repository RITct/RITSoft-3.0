@extends('layouts.layout')

@section('content')
    <h1>Subject Allocation </h1>

    <table>
        <tr>
            <th>CODE</th>
            <th>NAME</th>
            <th>CREDIT</th>
            <th>IS_ACTIVE</th>
            <th>OPTIONS</th>
        </tr>
        @foreach ($subjects as $subject)
        <tr>
            <td>{{$subject->code}}</td>
            <td>{{$subject->name}}</td>
            <td>{{$subject->credits}}</td>
            <td>{{$subject->is_active}}</td>
            <td>
                <form method="post" action="{{route('subjects.destroy',['subject'=>$subject->code])}}">
                    @csrf
                    @method('DELETE')
                    <button >delete</button>
                </form>
                <form method="get" action="{{route('subjects.edit',['subject'=>$subject->code])}}">
                    <button >edit</button>
                </form>
            </td>

        </tr>
        @endforeach
        <a href ="{{route('subjects.create')}}">Add New Subject</a>
        <br>
        <br>


    </table>
@endsection
