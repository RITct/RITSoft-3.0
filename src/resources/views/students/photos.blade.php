@extends('students.appLayout')

@section('title', 'Photo')
@section('content')
<h1>Upload Photo And Staff Advisor Approval Status</h1>
    
  <br><br>
  <form methode="post" action="{{ route('students.photos') }}">
    <input type="file" id="myFile" name="filename">
    <input type="submit">
  </form>
    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Molestiae quisquam quaerat fuga 
    doloremque mollitia! Impedit amet dicta, magnam illo omnis placeat corporis, asperiores repellendus 
    ipsa praesentium officia dolore sapiente iste?</p>
@endsection  