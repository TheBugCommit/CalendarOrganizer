@extends('layouts.main')

@section('title', 'Helpers Edit')

@section('card-header')
    <div class="card-header">
        <h1 class="title">Helpers - {{ $calendar->title }}</h1>
    </div>
@endsection

@section('content')
    <div class="fullpage-loading" v-if="show_loading">
        <loading-component :loading="show_loading"></loading-component>
    </div>
    <helpers-list></helpers-list>
@endsection

@section('js')
    <script>
        const route_helpers_get = '{{ route('calendar.helpers.get') }}';
        const route_helpers_remove = '{{ route('calendar.helpers.remove') }}';
        const route_helpers_add = '{{ route('calendar.helpers.add') }}';
        const route_user_all = '{{ route('user.all') }}';
    </script>
@endsection
