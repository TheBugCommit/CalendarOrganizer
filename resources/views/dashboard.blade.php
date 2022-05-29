@extends('layouts.main')

@section('title', 'Dashboard - ' . Auth::user()->name)
@section('content')
    <h4 class="title">My Calendars</h4>

    <div class="fullpage-loading" v-if="show_loading">
        <loading-component :loading="show_loading"></loading-component>
    </div>
    <div class="container">
        <div class="row calendar-list">
            <calendar-component v-for="calendar in calendars" :key="calendar.id" :calendar="calendar"
                @redirect="redirect('/calendar_edit/' + calendar.id)"></calendar-component>
        </div>
    </div>

    <div class="floating-container">
        <button class="floating-button" data-bs-toggle="modal" data-bs-target="#newCalendarModal">+</button>
    </div>

    @include('modals.new_calendar')
@endsection
