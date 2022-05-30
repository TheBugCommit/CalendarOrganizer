<template>
    <div class="col-12 col-md-5 col-lg-4 col-xl-3 mt-3">
        <div class="card calendar" @click="$emit('redirect')">
            <div class="card-header justify-content-between align-items-center d-flex">
                <span>{{ calendar.title }}</span>
                <button type="button" v-if="owner" @click.self="$emit('edit', calendar.id)" class="btn col-3 btn-edit" id="edit"><i class="fas fa-edit"></i></button>
            </div>
            <div class="card-body">
                <p class="short-multiline-text" style="--lines: 2;">{{ calendar.description }}</p>
                <div class="row align-items-center">
                    <button type="button" v-if="owner" @click.self="$emit('remove', calendar.id)" class="btn col-3 btn-delete" id="delete"><i class="fas fa-trash"></i></button>
                    <p class="cursive-gray-text col-9 text-end m-0">{{ calendar.start_date }} - {{ calendar.end_date }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["calendar", "owner"],
};
</script>
