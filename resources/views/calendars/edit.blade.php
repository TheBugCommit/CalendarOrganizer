@extends('layouts.main')

@section('title', 'Calendar')

@section('content')
@endsection

@section('js')
    <script>
        const route_events_store = '';
        const route_events_update = '';
        const calendar = @json($calendar);
    </script>
@endsection
