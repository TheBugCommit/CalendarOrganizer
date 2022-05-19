@extends('layouts.main')

@section('title', 'CO - Signin')

@section('content')
    <form action="{{ route('auth.register') }}" method="post">
        @csrf

        <label for="name">Name: </label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" />

        <label for="email">Email: </label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" />

        <label for="phone">Phone: </label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" />


        <label for="surname1">First surname: </label>
        <input type="text" id="surname1" name="surname1" value="{{ old('surname1') }}" />

        <label for="surname1">Second surname: </label>
        <input type="text" id="surname2" name="surname2" value="{{ old('surname2') }}" />

        <label for="birth_date">Birth date:</label>
        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" />

        <label>Gender:</label><br>
        @foreach (\App\Models\User::GENDERS as $key => $gender)
            <input {{ old('gender') == $key ? 'checked' : '' }} type="radio" id="{{ $key }}" name="gender"
                value="{{ $key }}">
            <label for="{{ $key }}">{{ $gender }}</label><br>
        @endforeach

        <select name="nation_id">
            @foreach ($nations as $key => $nation)
                <option {{ old('nation_id') == $nation->id ? 'selected' : '' }} value="{{ $nation->id }}">
                    {{ $nation->code }} - {{ $nation->name }}</option>
            @endforeach
        </select>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" />
        <label for="password_confirm">Password Confirmation</label>
        <input type="password" id="password_confirm" name="password_confirmation" />

        <input type="submit" value="Signin" />
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
