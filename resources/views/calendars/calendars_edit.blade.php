@extends('layouts.main')

@section('title', 'Calendar')

@section('content')
    <calendar/>

    <a class="btn btn-primary" href="{{ route('calendar.helpers.index', ['calendar_id' => $calendar->id]) }}">Manage Helpers</a>

@endsection

@section('js')
    <script src="https://cdn.tiny.cloud/1/tvuvy4qagccnw5sdwnivs5705he7n3mgre9hd679x29l7r3m/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="/js/jscolor.min.js"></script>
    <script>
        const route_user_categories = '{{ route('user.categories') }}';
        const route_events = '{{ route('calendar.events') }}';
        const route_events_store = '{{ route('calendar.event.store') }}';
        const route_events_update = '{{ route('calendar.event.update') }}';
        const route_events_destroy = '{{ route('calendar.event.destroy') }}';
        const auth_user_id = @json(Auth::user()->id)

        const calendar = @json($calendar);
    </script>
@endsection
