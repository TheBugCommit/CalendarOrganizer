@extends('layouts.main')

@section('title', 'Calendar')

@section('css')
    <link rel="stylesheet" href="/css/coloris.min.css">
@endsection

@section('content')
    @include('modals.create_edit_event')
    <calendar/>
@endsection

@section('js')
    <script src="https://cdn.tiny.cloud/1/tvuvy4qagccnw5sdwnivs5705he7n3mgre9hd679x29l7r3m/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="/js/coloris.min.js"></script>
    <script>
        const route_events = '{{ route('calendar.events') }}';
        const route_events_store = '{{ route('calendar.event.store') }}';
        const route_events_update = '{{ route('calendar.event.update') }}';
        const route_events_destroy = '{{ route('calendar.event.destroy') }}';

        const calendar = @json($calendar);
    </script>
@endsection
