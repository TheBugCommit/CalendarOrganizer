@extends('layouts.main')

@section('title', 'Calendar')

@section('card-header')
    <div class="card-header">
        <h1 class="title">{{ $calendar->title }}</h1>
    </div>
@endsection

@section('content')
    <div class="fullpage-loading" v-if="show_loading">
        <loading-component :loading="show_loading"></loading-component>
    </div>

    <calendar></calendar>

    <div class="adminActions">
        <input type="checkbox" name="adminToggle" class="adminToggle" />
        <a class="adminButton" href="#!"><i class="fa fa-cog"></i></a>
        <div class="adminButtons">
            <a href="{{ route('calendar.helpers.index', ['id' => $calendar->id]) }}" title="Calendar Helpers"><i
                    class="fas fa-hands-helping"></i></a>
            <button type="button" @click="openUpload" title="Upload Targets"><i class="fas fa-upload"></i></button>
            @if ($calendar->google_calendar_id == null)
                <a href="{{ route('calendar.google.calendar.publish', ['calendar_id' => $calendar->id]) }}" title="Publish Calendar"><i
                        class="fas fa-hands-helping"></i></a>
            @endif
    </div>
</div>


<form action="{{ route('calendar.targets.upload') }}" method="post" class="d-none"
    enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ $calendar->id }}">
    <input type="file" name="file" id="targets">
    <input type="submit" value="Upload Targets" id="submit-targets-form" name="submit">


    @if (session()->has('success'))
        <div class="d-none" id="upload-success">{{ session()->get('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="d-none" id="upload-errors">
            <ul class="list-unstyled">
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
