@extends('layouts.main')

@section('title', 'Calendar')

@section('content')
    {{ dd($calendar) }}
@endsection
