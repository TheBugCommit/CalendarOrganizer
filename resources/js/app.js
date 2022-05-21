require('./bootstrap');

import CalendarComponent from './components/CalendarComponent.vue'
import LoadingComponent from './components/LoadingComponent.vue'

window.Vue = require('vue').default;

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
    },

    watch: {

    }
});
