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

    <calendar-component v-for="calendar in calendars" :key="calendar.id" :calendar="calendar"></calendar-component>
    <loading-component :loading="show_loading"/>
@endsection
