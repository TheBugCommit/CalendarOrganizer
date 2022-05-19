@extends('layouts.main')

@section('title', 'CO - Login')

@section('content')
    <form action="{{ route('auth.authenticate') }}" method="post">
        @csrf
        <label for="email">Email: </label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label for="password">Password: </label>
        <input type="password" name="password">

        <input type="submit" value="Login">
    </form>

    @if ($errors->any())
        <div class="alert alert-danger">
            <p><strong>Opps Something went wrong</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
