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
        me: {},
        show_loading: false,
        currentRoute: window.location.pathname,
        calendars: [],
        selected_calendar: null,
        newCalendarForm: {
            title: "",
            start_date: "",
            end_date: ""
        },
        categories: [],
    },

    mounted() {
        if(typeof route_user_me !== 'undefined')
            this.getMe()

        if (this.currentRoute == '/')
            this.getCalendars()

        if(typeof route_user_categories !== 'undefined')
            this.getCategories();
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

        getCategories() {
            let _this = this;

            $.ajax({
                url: route_user_categories,
                dataType: "JSON",
                method: 'GET',
            }).done((response) => {
                _this.categories = response
            }).fail((response) => {
                _this.categories = _this.categories || []
            })
        },

        /*deleteCategory(id){
            let _this = this
            $.ajax({
                url: route_user_category_delete,
                dataType: "JSON",
                method: 'DELETE',
                data: {id: id}
            }).done((response) => {
                let index = _this.categories.findIndex(cat => cat.id == id)
                if(index != -1)
                    _this.categories.splice(index, 1)
            }).fail((response) => {
                _this.categories = _this.categories || []
            })
        },

        updateCategory(id){
            let _this = this
            $.ajax({
                url: route_user_category_update,
                dataType: "JSON",
                method: 'PATCH',
                data: {id: id, name: _this.}
            }).done((response) => {
                let index = _this.categories.findIndex(cat => cat.id == id)
                if(index != -1)
                    _this.categories.splice(index, 1)
            }).fail((response) => {
                _this.categories = _this.categories || []
            })
        },*/

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
