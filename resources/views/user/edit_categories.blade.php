@extends('layouts.main')

@section('content')
    Categories
    <ul>
        <li v-for="category in categories" :key="category.id">
            @{{ category.name }}
            <button type="button" class="btn">Delete</button>
        </li>
    </ul>
@endsection

@section('js')
    <script>
        const route_user_categories = '{{ route('user.category.all') }}';
        const route_user_category_store = '{{ route('user.category.store') }}';
        const route_user_category_update = '{{ route('user.category.delete') }}';
        const route_user_category_delete = '{{ route('user.category.update') }}';
    </script>
@endsection
