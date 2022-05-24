<template>
    <div class="container">
        <FullCalendar :options="calendarOptions" ref="fullCalendar" />
        <EventPopup :event="selected_event" :target="selected_target" ref="eventPopup" />
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
import EventPopup from "./EventPopup.vue";
import Event from "./Event.vue";

import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-icons/font/bootstrap-icons.css";

export default {
    components: {
        FullCalendar,
        EventPopup,
        Event
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
                    start: moment([moment().year()]).clone().format("YYYY-MM-DD hh:mm:ss"),
                    end: moment([moment().year()])
                        .clone()
                        .endOf("year")
                        .format("YYYY-MM-DD hh:mm:ss"),
                },
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
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay",
                },
            },
            fullCalendar: null,
            calendar: {},
            selected_event: {
                id: '',
                category_id: '',
                calendar_id: '',
                title: '',
                description: '',
                location: '',
                color: '',
                start: '',
                end: ''
            },
            selected_target: null,
            user_id: auth_user_id,
            event_editing: false,
        };
    },

    methods: {
        handleDateClick(date) {
            this.event_editing = false
            this.selected_target = null
            this.selected_event = {
                id: '',
                category_id: '',
                calendar_id: '',
                title: '',
                description: '',
                location: '',
                color: '',
                start: '',
                end: ''
            }
            this.$refs.eventManage.toggle()
        },

        handelEventDrop(info) {
            this.updateCalendarEvent(this.extractEventData(info));
        },

        handleEventClick(eventClickInfo) {
            this.event_editing = true
            this.selected_event = this.extractEventData(eventClickInfo);
            this.selected_target = this.getPath($(eventClickInfo.el));
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
            //falta afegir calendar_id i calendar_category i modificar data a tostring
            $.ajax({
                url: route_events_store,
                data: _this.selected_event,
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


        updateCalendarEvent() {
            let _this = this;

            $.ajax({
                url: route_events_update,
                data: _this.selected_event,
                dataType: "JSON",
                method: "PATCH",
            })
                .done((response) => {
                    this.fullCalendar.getEventById(response.id).remove();
                    this.fullCalendar.addEvent(response);
                    console.log(response);
                })
                .fail((error) => {
                    console.error(error);
                });
        },

        editEvent(id) {
            this.$refs.eventManage.toggle()
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
                .done((response) => {
                    console.log(response);
                })
                .fail((error) => {
                    console.error(error);
                });
        },

        addEvent(event) {
            this.fullCalendar.addEvent(event);
        },

        getEventData() {
            let _this = this;
            let data = {
                calendar_id: _this.calendar.id,
                category_id: $('select[name="category_id"]').val(),
                description: tinymce.activeEditor.getContent(),
            };
            $("#createEvent input").each(function () {
                data[$(this).attr("name")] = $(this).val();
            });
            return data;
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
                start: moment(info.event.start).format("YYYY-MM-DD hh:mm:ss"),
                end: moment(info.event.end).format("YYYY-MM-DD hh:mm:ss")
            }
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

        this.fullCalendar = this.$refs.fullCalendar.getApi();

        $("#createEvent .modal-footer button:last-child").on(
            "click",
            _this.storeCalendarEvent
        );

        if (typeof calendar !== "undefined") {
            this.calendar = calendar;

            (async function () {
                let events = await _this.getCalendarEvents();
                _this.calendar.events = events;
                _this.fullCalendar.addEventSource(_this.calendar.events);
            })(_this);
        }
    },
};
</script>
