<template>
    <div class="col-12 col-md-5 col-lg-4 col-xl-3 mt-3">
        <div class="card calendar" @click="$emit('redirect')">
            <div class="card-header">{{ calendar.title }}</div>
            <div class="card-body">

                <p class="short-multiline-text" style="--lines: 2;">{{ calendar.description }}</p>

                <p class="cursive-gray-text text-end m-0">{{ calendar.start_date }} - {{ calendar.end_date }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["calendar"],
};
</script>
