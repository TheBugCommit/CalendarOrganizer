require('./bootstrap');

window.Vue = require('vue').default;

import LoadingComponent from './components/LoadingComponent.vue'
import CalendarComponent from './components/CalendarComponent.vue'
import Calendar from './components/Calendar/Calendar.vue'

Vue.component('calendar', Calendar);
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
        },
    },

    mounted() {

        $("select").selectize({
            create: false,
            sortField: "text",
        })


        if (this.currentRoute == '/')
            this.getCalendars()

        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: 'textarea',
                menubar: false,
                resize: false,
                plugins: [
                    'lists emoticons'
                ],
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent ' +
                    'forecolor backcolor emoticons',
            });
        }

        if (typeof Coloris !== 'undefined') {
            Coloris({
                el: '.color-field',
                themeMode: 'auto',
                alpha: false,
                wrap: true,
                swatches: [
                    '#FF0000',
                    '#00FFFF',
                    '#800080',
                    '#FFFF00',
                    '#00FF00',
                    '#FF00FF',
                    '#FFC0CB',
                    '#808080',
                    '#FFA500',
                    '#008000',
                    '#7FFD4',
                    '#A52A2A'
                ],
            });
        }
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

        redirect(url) {
            window.location.href = url
        }
    },

    watch: {

    }
});
