<template>
  <div class="container">
    <div class="row">
      <div class="col-3">
        <div class="card">
          <div class="card-header">{{ calendar.title }}</div>
          <div class="card-body">
            {{ calendar.start_date }} - {{ calendar.end_date }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["calendar"],
};
</script>
