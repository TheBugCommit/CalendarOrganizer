<template>
  <div class="card" @click="$emit('redirect')">
    <div class="card-header">{{ calendar.title }}</div>
    <div class="card-body">
      {{ calendar.start_date }} - {{ calendar.end_date }}
    </div>
  </div>
</template>

<script>
export default {
  props: ["calendar"],
};
</script>
