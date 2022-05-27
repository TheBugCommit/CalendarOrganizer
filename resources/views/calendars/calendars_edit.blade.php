@extends('layouts.main')

@section('title', 'Calendar')

@section('content')
    <calendar></calendar>
    <a class="btn btn-primary" href="{{ route('calendar.helpers.index', ['id' => $calendar_id]) }}">Manage Helpers</a>
    <a class="btn btn-primary" href="{{ route('calendar.publish', ['id' => $calendar_id]) }}">Publish Calendar</a>

    <form action="{{ route('calendar.targets.upload') }}" method="post" enctype="multipart/form-data">
        @csrf
        Select file to upload:
        <input type="hidden" name="id" value="{{ $calendar_id }}">
        <input type="file" name="file" id="targets">
        <input type="submit" value="Upload Targets" name="submit">

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>
@endsection

@section('js')
    <script src="https://cdn.tiny.cloud/1/tvuvy4qagccnw5sdwnivs5705he7n3mgre9hd679x29l7r3m/tinymce/5/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script src="/js/jscolor.min.js"></script>
    <script>
        const route_user_categories = '{{ route('user.category.all') }}';
        const route_events = '{{ route('calendar.events') }}';
        const route_events_store = '{{ route('calendar.event.store') }}';
        const route_events_update = '{{ route('calendar.event.update') }}';
        const route_events_destroy = '{{ route('calendar.event.destroy') }}';
        const route_calendar_get = '{{ route('calendar.get') }}'
        const route_user_me = '{{ route('user.me') }}'
    </script>
@endsection
