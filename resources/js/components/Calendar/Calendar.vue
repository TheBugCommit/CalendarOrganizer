<template>
    <FullCalendar :options="calendarOptions" ref="fullCalendar" />
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
                initialView: "dayGridMonth",
                themeSystem: "bootstrap5",
                timeZone: "Europe/Madrid",
                editable: true,
                droppable: true,
                validRange: {
                    start: moment([moment().year()]).clone().format("YYYY-MM-DD"),
                    end: moment([moment().year()])
                        .clone()
                        .endOf("year")
                        .format("YYYY-MM-DD"),
                },
                plugins: [
                    dayGridPlugin,
                    interactionPlugin,
                    timegridPlugin,
                    bootstrap5Plugin,
                ],
                eventDrop: this.handelEventDrop,
                dateClick: this.handleDateClick,
                selectable: true,
                allDaySlot: false,
                initialDate: new Date(),
                nowIndicator: true,
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay",
                },
            },
            fullCalendar: null,
            calendar: {},
        };
    },

    methods: {
        handleDateClick(date) {
            $("#createEditEvent #start_time").val(moment());
            $("#createEditEvent #end_time").val(moment().add(1, "hours"));
            $("#createEditEvent").modal("toggle");
        },

        handelEventDrop(event) {
            this.updateCalendarEvent({
                id: event.event.id,
                category_id: event.event.category_id,
                calendar_id: event.event.calendar_id,
                title: event.event.title,
                description: event.event.description,
                location: event.event.location,
                color: event.event.color,
                start_time: moment(event.event.start).format("YYYY-MM-DD hh:mm:ss"),
                end_time: moment(event.event.end).format("YYYY-MM-DD hh:mm:ss"),
            });
        },

        async getCalendarEvents() {
            let _this = this;

            try {
                return await $.ajax({
                    url: route_events,
                    type: "GET",
                    data: { id: _this.calendar.id },
                });
            } catch (error) {
                console.error(error);
            }

            return [];
        },

        storeCalendarEvent() {
            let _this = this;

            $.ajax({
                url: route_events_store,
                data: this.getEventData(),
                dataType: "JSON",
                method: "POST",
            })
                .done((response) => {
                    _this.addEvent(response);
                })
                .fail((error) => {
                    console.error(error);
                });
        },

        updateCalendarEvent(event) {
            let _this = this;

            $.ajax({
                url: route_events_update,
                data: event,
                dataType: "JSON",
                method: "PATCH",
            })
                .done((response) => {
                    console.log(response);
                })
                .fail((error) => {
                    console.error(error);
                });
        },

        addEvent(event) {
            this.fullCalendar.addEvent(this.parseEvent(event));
        },

        getEventData() {
            let _this = this;
            let data = {
                calendar_id: _this.calendar.id,
                category_id: 1,
                description: "descripccio",
                location: "Barcelona",
                color: "red",
            };
            $("#createEditEvent input").each(function () {
                data[$(this).attr("name")] = $(this).val();
            });
            return data;
        },

        parseEvents(events) {
            return Array.isArray(events)
                ? events.map((elem) => this.parseEvent(elem))
                : [];
        },

        parseEvent(event) {
            let obj = { ...event };

            delete obj.start_time;
            delete obj.end_time;

            obj.start = event.start_time;
            obj.end = event.end_time;

            return obj;
        },
    },

    mounted() {
        let _this = this;

        this.fullCalendar = this.$refs.fullCalendar.getApi();

        $("#createEditEvent .modal-footer button:last-child").on(
            "click",
            _this.storeCalendarEvent
        );

        if (typeof calendar !== "undefined") {
            this.calendar = calendar;

            (async function () {
                let events = await _this.getCalendarEvents();
                _this.calendar.events = _this.parseEvents(events);
                _this.fullCalendar.addEventSource(_this.calendar.events);
            })(_this);
        }
    },
};
</script>
