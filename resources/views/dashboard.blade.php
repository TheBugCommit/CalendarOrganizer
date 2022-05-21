@extends('layouts.main')

@section('title', 'Dashboard - ' . Auth::user()->name)

@section('content')
    <form @submit.prevent="storeCalendar()">
        <input type="text" id="title" v-model="newCalendarForm.title" required>
        <input type="date" id="start_date" v-model="newCalendarForm.start_date" required>
        <input type="date" id="end_date" v-model="newCalendarForm.end_date" required>
        <input type="submit" value="Add">
    </form>

    <loading-component :loading="show_loading"></loading-component>
    <div class="container">
        <div class="row">
            <calendar-component v-for="calendar in calendars" :key="calendar.id" :calendar="calendar" @redirect="redirect('/calendar_edit/' + calendar.id)"></calendar-component>
        </div>
    </div>
@endsection
