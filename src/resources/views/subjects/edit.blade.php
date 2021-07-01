@extends('layouts.layout')


@section('content')
    <h1> Update Subject </h1>
    <form method="post" action="{{route('subjects.update',['subject'=>$subject->code])}}" >
        @csrf
        @method('PUT')
{{--        Confused about the method tos pecify, here updating the entire resource completely so PUT--}}
        <table>
            <tr>
                <td><label for="code">Code</label></td>
                <td><input name="code" id="code" value="{{$subject->code}}"></td>
            </tr>
            <tr>
                <td><label for="name">Subject Name</label></td>
                <td><input name="name" id="name" value="{{$subject->name}}"></td>
            </tr>
            <tr>
                <td><label for="credit">Credit</label></td>
                <td><input name="credit" id="credit" value="{{$subject->credits}}" type="number"></td>
            </tr>

            <div>
                <button>Update Subject</button>
            </div>
        </table>
    </form>

@endsection
