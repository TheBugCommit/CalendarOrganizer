@extends('layouts.main')

@section('content')
    <h1 class="title">Export Events</h1>

    <div class="fullpage-loading" v-if="show_loading">
        <loading-component :loading="show_loading"></loading-component>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="input_group">
                    <input type="text" class="input_field datepickerrange" autocomplete="off" id="export_range">
                    <label for="export_range" class="input_label">Range:</label>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <label for="export_range_calendar" class="grey-text">Calendar:</label>
                <select class="selectize" id="export_range_calendar">
                    <option value="-1"></option>
                    <option v-for="calendar in allCalendars" :key="calendar.id" :value="calendar.id">
                        @{{ calendar.title }}</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <div class="input_group">
                    <input type="password" class="input_field validate" v-model="user_password" id="password">
                    <label for="passowrd" class="input_label">Passoword:</label>
                </div>
            </div>
            <button type="button" class="btn btn-save" @click="getExportEvents">Filter</button>
            <span class="input-error">@{{ error }}</span>
        </div>
        <hr />
        <div class="row mt-3">

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Summary</th>
                        <th scope="col">Description</th>
                        <th scope="col">Location</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="event in export_events"  :style="'--color: ' + event.color">
                        <td class="short-text-200">@{{ event.summary }}</td>
                        <td class="short-text-200">@{{ event.description }}</td>
                        <td class="short-text-200">@{{ event.location }}</td>
                        <td class="short-text-200">@{{ event.start.dateTime }}</td>
                        <td class="short-text-200">@{{ event.end.dateTime }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="floating-container" v-if="export_events.length > 0">
            <button class="floating-button" @click="generateDownload('events', export_events)"><i class="fas fa-download"
                    style="font-size: 25px"></i></button>
        </div>
    </div>
@endsection
