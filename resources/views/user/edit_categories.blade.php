@extends('layouts.main')

@section('content')
    <h4 class="title">Categories</h4>
    <div class="fullpage-loading" v-if="show_loading">
        <loading-component :loading="show_loading"></loading-component>
    </div>

    <ul>
        <li v-for="category in categories" :key="category.id" class="category list-unstyled">
            <div class="row align-items-center">
                <div class="col-8">
                    <p class="mb-0 ms-3 short-text-200">@{{ category.name }}</p>
                </div>
                <div class="col-4">
                    <div class="d-flex justify-content-end me-4">
                        <a data-bs-toggle="modal" @click="selected_category = category.id; newCategory = category.name"
                            data-bs-target="#editCategoryModal" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                        <button type="button" @click="deleteCategory(category.id)" class="btn btn-delete"><i
                                class="fas fa-times"></i></button>
                    </div>
                </div>

        </li>
    </ul>

    <div class="floating-container">
        <button class="floating-button" @click="newCategory = ''" data-bs-toggle="modal" data-bs-target="#newCategoryModal">+</button>
    </div>

    @include('modals.new_category')
    @include('modals.edit_category')
@endsection

@section('js')
    <script>
        const route_user_categories = '{{ route('user.category.all') }}';
        const route_user_category_store = '{{ route('user.category.store') }}';
        const route_user_category_update = '{{ route('user.category.delete') }}';
        const route_user_category_delete = '{{ route('user.category.update') }}';
    </script>
@endsection
