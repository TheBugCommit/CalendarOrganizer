@extends('layouts.main')

@section('title','Helpers Edit')

@section('content')
    helpers edit

    <helpers-list></helpers-list>

@endsection

@section('js')
    <script>
        const route_helpers_get     = '{{ route('calendar.helpers.get') }}'
        const route_helpers_remove  = '{{ route('calendar.helpers.remove') }}'
    </script>
@endsection


