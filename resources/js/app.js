require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue';
import LoadingComponent from './components/LoadingComponent.vue'
import CalendarListComponent from './components/CalendarListComponent.vue'
import Calendar from './components/Calendar/Calendar.vue'
import HelpersListComponent from './components/Calendar/Helpers/HelpersListComponent.vue'

Vue.component('calendar', Calendar);
Vue.component('loading-component', LoadingComponent);
Vue.component('calendar-component', CalendarListComponent);
Vue.component('helpers-list', HelpersListComponent);

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

const app = new Vue({
    el: '#app',
    data: {
        me: null,
        show_loading: false,
        currentRoute: window.location.pathname,
        calendars: [],
        selected_calendar: null,
        newCalendarForm: {
            title: "",
            start_date: "",
            end_date: ""
        },
    },

    mounted() {
        if(typeof route_user_me !== 'undefined')
            this.getMe()
        if (this.currentRoute == '/')
            this.getCalendars()
    },

    created() {

    },

    methods: {
        getCalendars() {
            let _this = this;
            this.show_loading = true;
            $.ajax({
                url: "/calendars",
                method: "GET",
                dataType: "JSON",
            }).done(function (response) {
                _this.calendars = response;
            }).fail(function (error) {
                console.error(error);
            }).always(() => {
                this.show_loading = false;
            });
        },

        storeCalendar() {
            let _this = this
            $.ajax({
                url: '/calendar_store',
                dataType: 'JSON',
                method: 'POST',
                data: _this.newCalendarForm
            }).done(function (response) {
                _this.calendars.push(response)
                Object.keys(_this.newCalendarForm).forEach((elem) => { _this.newCalendarForm[elem] = "" })
            }).fail(function (error) {
                console.error(error)
            })
        },

        getMe() {
            let _this = this
            $.ajax({
                url: route_user_me,
                dataType: 'JSON',
                method: 'GET',
            }).done(function (response) {
                _this.me = response
            }).fail(function (error) {
                console.error(error)
            })
        },

        redirect(url) {
            window.location.href = url
        },
    },

    watch: {

    }
});
