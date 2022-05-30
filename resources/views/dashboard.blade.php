@extends('layouts.main')

@section('title', 'Dashboard - ' . Auth::user()->name)

@section('card-header')
    <div class="card-header">
        <h1 class="title">My Calendars</h1>
    </div>
@endsection

@section('content')

    <div class="fullpage-loading" v-if="show_loading">
        <loading-component :loading="show_loading"></loading-component>
    </div>

    <ul class="nav d-flex flex-row justify-content-end nav-pills mb-3 d-flex" id="pills-tab" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link active btn-tab" id="v-pills-owner-tab" data-bs-toggle="pill" data-bs-target="#v-pills-owner"
                type="button" role="tab" aria-controls="v-pills-owner" aria-selected="true">Owner</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-tab" id="v-pills-helper-tab" data-bs-toggle="pill" data-bs-target="#v-pills-helper"
                type="button" role="tab" aria-controls="v-pills-helper" aria-selected="false">Helper</button>
        </li>
    </ul>
    <div class="tab-content" id="v-pills-tabContent">
        <div class="tab-pane fade show active" id="v-pills-owner" role="tabpanel" aria-labelledby="v-pills-owner-tab">
            <div class="container">
                <div class="row calendar-list">
                    <calendar-component v-for="calendar in calendars" :key="calendar.id" :calendar="calendar"
                        @redirect="redirect('/calendar_edit/' + calendar.id)" :owner="true" @remove="removeCalendar" @edit="editCalendar"></calendar-component>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="v-pills-helper" role="tabpanel" aria-labelledby="v-pills-helper-tab">
            <div class="row calendar-list">
                <calendar-component v-for="calendar in helper_calendars" :key="calendar.id" :calendar="calendar"
                    @redirect="redirect('/calendar_edit/' + calendar.id)" :owner="false"></calendar-component>
            </div>
        </div>
    </div>

    <div class="floating-container">
        <button class="floating-button" data-bs-toggle="modal" data-bs-target="#newCalendarModal"><i class="fas fa-plus"></i></button>
    </div>


    @if(session()->has('alreadyHelper'))
        <div class="d-none" id="alreadyHelper">{{ session()->get('alreadyHelper') }}</div>
    @endif
    @if (session()->has('becomeHelper'))
        <div class="d-none" id="becomeHelper">{{ session()->get('becomeHelper') }}</div>
    @endif

    @include('modals.new_calendar')
    @include('modals.edit_calendar')
@endsection
