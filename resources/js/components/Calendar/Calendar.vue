<template>
  <div class="container">
    <FullCalendar :options="calendarOptions" ref="fullCalendar" />
    <EventPopup
      :event="selected_event"
      :target="selected_target"
      :calendar="calendar"
      :categories="$root.categories"
      :me="$root.me"
      ref="eventPopup"
    />
    <Event :event="selected_event" :editing="event_editing" ref="eventManage" />
  </div>
</template>

<script>
import "@fullcalendar/core/vdom";
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timegridPlugin from "@fullcalendar/timegrid";
import bootstrap5Plugin from "@fullcalendar/bootstrap5";
import Swal from "sweetalert2";
import EventPopup from "./Events/EventPopup.vue";
import Event from "./Events/Event.vue";

export default {
  components: {
    FullCalendar,
    EventPopup,
    Event,
  },

  data() {
    return {
      calendarOptions: {
        initialView: "dayGridMonth",
        timeZone: "local",
        nextDayThreshold: "00:00",
        editable: true,
        droppable: true,
        plugins: [
          dayGridPlugin,
          interactionPlugin,
          timegridPlugin,
          bootstrap5Plugin,
        ],
        eventDrop: this.handelEventDrop,
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        selectable: true,
        allDaySlot: false,
        initialDate: new Date(),
        nowIndicator: true,
        expandRows: true,
        aspectRatio: 2,
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
      },
      fullCalendar: null,
      calendar: {},
      selected_event: {
        id: "",
        category_id: "",
        calendar_id: "",
        title: "",
        description: "",
        location: "",
        color: "",
        start: "",
        end: "",
        user_id: "",
        published: 0,
        user_email: ""
      },
      selected_target: null,
      event_editing: false,
      slotLabelFormat: {
        hour: "numeric",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
      },
    };
  },

  methods: {
    handleDateClick(date) {
      this.event_editing = false;
      this.selected_target = null;
      this.selected_event = {
        id: "",
        category_id: null,
        calendar_id: this.calendar.id,
        title: "",
        description: "",
        location: "",
        color: "",
        start: moment(date.date.getTime()),
        end: moment(date.date.getTime()).add(1, "hours"),
        user_id: "",
        published: 0,
        user_email: ""
      };
      $("#date-range").val(
        this.selected_event.start.format("YYYY-MM-DD HH:mm:ss") +
          " - " +
          this.selected_event.end.add(1, "hours").format("YYYY-MM-DD HH:mm:ss")
      );
      this.$refs.eventManage.toggle();
    },

    handelEventDrop(info) {
      if (
        (this.$root.me.id != this.calendar.user_id &&
          this.$root.me.id != info.event.extendedProps.user_id) ||
        (info.event.extendedProps.published == 1 &&
          this.$root.me.id != this.calendar.user_id)
      ) {
        info.revert();
        return;
      }

      this.updateCalendarEvent(this.extractEventData(info), info.event);
    },

    handleEventClick(eventClickInfo) {
      this.event_editing = true;
      this.selected_event = this.extractEventData(eventClickInfo);
      this.selected_target = this.getPath($(eventClickInfo.el));
    },

    async getCalendarEvents() {
      let _this = this;
      let events;
      try {
        events = await $.ajax({
          url: route_events,
          type: "GET",
          data: { id: _this.calendar.id },
          dataType: "JSON",
        });
      } catch (error) {
        Swal.fire(
          "Error!",
          "Something went wrong trying to get events",
          "error"
        );
      }

      this.$root.show_loading = false;

      return events || [];
    },

    storeCalendarEvent() {
      let _this = this;

      let event = { ..._this.selected_event };

      event.start =
        event.start instanceof moment
          ? event.start.format("YYYY-MM-DD HH:mm:ss")
          : event.start;
      event.end =
        event.end instanceof moment
          ? event.end.format("YYYY-MM-DD HH:mm:ss")
          : event.end;

      $.ajax({
        url: route_events_store,
        data: event,
        dataType: "JSON",
        method: "POST",
      })
        .done((response) => {
          _this.addEvent(response);
          _this.$refs.eventManage.toggle();
        })
        .fail((error) => {
          Swal.fire(
            "Error!",
            "Something went wrong trying to save event, check your inputs, all fields are required",
            "error"
          );
        });
    },

    updateCalendarEvent(event = null, originalEvent = null) {
      let _this = this;

      if (event == null) event = { ...this.selected_event };

      event.start =
        event.start instanceof moment
          ? event.start.format("YYYY-MM-DD HH:mm:ss")
          : event.start;
      event.end =
        event.end instanceof moment
          ? event.end.format("YYYY-MM-DD HH:mm:ss")
          : event.end;

      $.ajax({
        url: route_events_update,
        data: event,
        dataType: "JSON",
        method: "PATCH",
      })
        .done((response) => {
          if (originalEvent != null) originalEvent.remove();
          else _this.fullCalendar.getEventById(response.id).remove();

          _this.fullCalendar.addEvent(response);
          _this.$refs.eventManage.hide();
        })
        .fail((error) => {
          Swal.fire(
            "Error!",
            "Something went wrong trying to update event, check your inputs all fields are required",
            "error"
          );
        });
    },

    editEvent(id) {
      this.$refs.eventManage.toggle();
    },

    deleteEvent(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.isConfirmed) {
          this.destroyEventRequest(id);
          this.fullCalendar.getEventById(id).remove();
          Swal.fire("Deleted!", "Event has been deleted.", "success");
        }
      });
    },

    destroyEventRequest(id) {
      let _this = this;

      $.ajax({
        url: route_events_destroy,
        data: { id: id },
        dataType: "JSON",
        method: "DELETE",
      })
        .done((response) => {})
        .fail((error) => {
          Swal.fire(
            "Error!",
            "Something went wrong trying to delete event. It's your event?",
            "error"
          );
        });
    },

    addEvent(event) {
      this.fullCalendar.addEvent(event);
    },

    async getCalendar() {
      let calendar_id = window.location.href.split("/").pop();

      try {
        return await $.ajax({
          url: route_calendar_get,
          method: "GET",
          data: { id: calendar_id },
        });
      } catch (error) {
        Swal.fire(
          "Error!",
          "Something went wrong trying to load calendar",
          "error"
        );
      }

      return [];
    },

    extractEventData(info) {
      return {
        id: info.event.id,
        category_id: info.event.extendedProps.category_id,
        calendar_id: info.event.extendedProps.calendar_id,
        title: info.event.title,
        description: info.event.extendedProps.description,
        location: info.event.extendedProps.location,
        color: info.event.backgroundColor,
        start: moment(info.event.start).format("Y-MM-DD HH:mm:ss"),
        end: moment(info.event.end).format("Y-MM-DD HH:mm:ss"),
        published: info.event.extendedProps.published,
        user_id: info.event.extendedProps.user_id,
        user_email: info.event.extendedProps.user_email,
      };
    },

    getPath: function (node) {
      let path;

      while (node.length) {
        let realNode = node[0],
          name = realNode.localName;
        if (!name) break;
        name = name.toLowerCase();

        let parent = node.parent();

        let sameTagSiblings = parent.children(name);
        if (sameTagSiblings.length > 1) {
          let allSiblings = parent.children();
          let index = allSiblings.index(realNode) + 1;
          if (index > 1) {
            name += ":nth-child(" + index + ")";
          }
        }

        path = name + (path ? ">" + path : "");
        node = parent;
      }

      return path;
    },
  },

  mounted() {
    let _this = this;

    this.$root.show_loading = true;
    this.fullCalendar = this.$refs.fullCalendar.getApi();

    $("#createEvent .modal-footer button:last-child").on(
      "click",
      _this.storeCalendarEvent
    );

    (async function () {
      _this.calendar = await _this.getCalendar();
      _this.fullCalendar.setOption("validRange", {
        start: moment(_this.calendar.start_date).format("YYYY-MM-DD HH:mm:ss"),
        end: moment(_this.calendar.end_date).format("YYYY-MM-DD HH:mm:ss"),
      });
      if (_this.calendar != null && _this.calendar.length != 0) {
        let events = await _this.getCalendarEvents();
        _this.calendar.events = events;
        _this.fullCalendar.addEventSource(_this.calendar.events);
      }
    })(_this);
  },
};
</script>
