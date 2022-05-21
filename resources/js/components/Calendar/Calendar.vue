<template>
  <FullCalendar :options="calendarOptions" />
</template>

<script>
import "@fullcalendar/core/vdom";
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timegridPlugin from "@fullcalendar/timegrid";
import bootstrap5Plugin from "@fullcalendar/bootstrap5";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-icons/font/bootstrap-icons.css";

export default {
  components: {
    FullCalendar,
  },

  data() {
    return {
      calendarOptions: {
        initialView: "timeGridWeek",
        themeSystem: "bootstrap5",
        validRange: {
          start: moment([moment().year()]).clone().format('YYYY-MM-DD'),
          end: moment([moment().year()]).clone().endOf('year').format('YYYY-MM-DD')
        },
        plugins: [
          dayGridPlugin,
          interactionPlugin,
          timegridPlugin,
          bootstrap5Plugin,
        ],
        events: [
          { title: "event 1", date: "2022-05-01" },
          { title: "event 2", date: "2022-05-21" },
        ],
        dateClick: this.handleDateClick,
        allDaySlot: false,
        initialDate: new Date(),
        headerToolbar: {
          left: "today prev,next",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
      },
    };
  },

  methods: {
    handleDateClick: function (arg) {
      alert("date click! " + arg.dateStr);
    },
  },
};
</script>
