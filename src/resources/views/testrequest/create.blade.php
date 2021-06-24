@extends("layouts.layout")

<form method="POST" action="/testrequest">
    <input type="text" name="name" >
    {!! csrf_field() !!}
    <input type="submit" value="Submit">
</form>
