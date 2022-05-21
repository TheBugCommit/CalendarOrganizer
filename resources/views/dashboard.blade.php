@extends('layouts.main')

@section('title', 'Dashboard - ' . Auth::user()->name)

@section('content')
    <form action="{{ route('calendar.store') }}" method="post">
        @csrf
        <input type="text" name="title">
        <input type="date" name="start_date">
        <input type="date" name="end_date">
        <input type="submit" value="Add">
    </form>

    <calendars-component></calendars-component>

@endsection
