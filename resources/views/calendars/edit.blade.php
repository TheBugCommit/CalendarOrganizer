@extends('layouts.main')

@section('title', 'Calendar')

@section('content')
    @include('modals.create_edit_event')
    <calendar/>
@endsection

@section('js')
    <script>
        const route_events = '{{ route('calendar.events') }}';
        const route_events_store = '{{ route('calendar.event.store') }}';
        const route_events_update = '{{ route('calendar.event.update') }}';
        const calendar = @json($calendar);
    </script>
@endsection
