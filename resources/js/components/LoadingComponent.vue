<template>
    <div v-if="loading" class="d-flex justify-content-center align-items-center">
        <div class="mosaic-loader">
            <span></span><span></span>
            <template v-for="(j, index_j) in 4">
                <div :class="'cell d-'+ (index_i+index_j)" v-for="(i, index_i) in 4"></div>
            </template>
        </div>
    </div>
</template>
<script>
export default {
  props: ["loading"],
};
</script>
