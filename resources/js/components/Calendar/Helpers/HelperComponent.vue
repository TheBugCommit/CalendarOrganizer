<template>
  <div class="card">
    <div class="card-header">{{ user.full_name }}</div>
    <div class="card-body">
      {{ user.email }}
      <button type="button" @click="$emit('remove', user.id)">Delete</button>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user"],
};
</script>
