<template>
  <div class="container">
    <div class="row">
      <div class="col-3" v-for="calendar in calendars" :key="calendar.id" @click="redirectToEdit(calendar.id)">
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
  data: function () {
    return {
      calendars: [],
    };
  },

  mounted() {
    this.getCalendars();
  },

  methods: {
    getCalendars() {
      let _this = this
      $.ajax({
        url: '/calendars',
        method: "GET",
        dataType: "JSON",
      }).done(function (response) {
          _this.calendars = response
      }).fail(function(error) {
          console.error(error)
      });
    },

    redirectToEdit(id){
        window.location.href = `/calendar_edit/${id}`
    }
  },
};
</script>
