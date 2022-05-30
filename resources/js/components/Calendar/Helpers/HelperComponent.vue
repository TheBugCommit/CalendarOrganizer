<template>
    <li class="category list-unstyled">
        <div class="row align-items-center">
            <div class="col-8">
                <p class="mb-0 ms-3">{{ user.full_name }} - {{ user.email }}</p>
            </div>
            <div class="col-4">
                <div class="d-flex justify-content-end me-4">
                    <button type="button" @click="$emit('remove', user.id)" class="btn btn-delete"><i
                            class="fas fa-times"></i></button>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
export default {
    props: ["user"],
};
</script>
