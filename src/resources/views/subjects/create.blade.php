@extends('layouts.layout')


@section('content')
<h1> New Subject here </h1>
<form action="{{route('subjects.store')}}" method="POST">
    @csrf
    <table>
    <tr>
        <td><label for="courseId">Course ID</label></td>
        <td><input name="courseId" id="courseId" value="CSE406"></td>
    </tr>
    <tr>
        <td><label for="name">Course Name</label></td>
        <td><input name="name" id="name" value="DBMS"></td>
    </tr>
    <tr>
        <td><label for="credit">Credit</label></td>
        <td><input name="credit" id="credit" value="4" type="number"></td>
    </tr>

    <div>
      <button>Add Subject</button>
    </div>
    </table>
  </form>
@endsection
