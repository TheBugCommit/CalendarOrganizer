require('./bootstrap');

window.Vue = require('vue').default;

import LoadingComponent from './components/LoadingComponent.vue'
import CalendarComponent from './components/CalendarComponent.vue'

Vue.component('loading-component', LoadingComponent);
Vue.component('calendar-component', CalendarComponent);

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

const app = new Vue({
    el: '#app',
    data: {
        show_loading: false,
        currentRoute: window.location.pathname,
        calendars: [],
        newCalendarForm: {
            title: "",
            start_date: "",
            end_date: ""
        }
    },

    mounted() {
        if(this.currentRoute == '/')
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
            }).done(function(response){
                _this.calendars.push(response)
                Object.keys(_this.newCalendarForm).forEach((elem) => {_this.newCalendarForm[elem] = ""})
            }).fail(function (error){
                console.error(error)
            })
        },

        redirect(url){
            window.location.href = url
        }
    },

    watch: {

    }
});
