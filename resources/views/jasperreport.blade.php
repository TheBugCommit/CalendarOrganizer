@extends('layouts.main')

@section('card-header')
    <div class="card-header">
        <h1 class="title">Jasper report</h1>
    </div>
@endsection

@section('content')
    <div class="container">
        <p class="cursive-gray-text">Generate report events by categories</p>
        <form action="{{ route('jasperreport.get') }}" method="post">
            @csrf
            <div class="input_group">
                <label for="category">My categories: </label>
                <select id="categories" multiple="multiple" class="mt-2" name="categories[]">
                    @foreach ($categories as $key => $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <input type="submit" class="btn btn-default" value="Get Report">
        </form>
    </div>
@endsection
