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
            end_date: "",
            description: null,
        },
        categories: [],
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
                $('#newCalendarModal').modal('hide')
                $('.modal-backdrop').hide();
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

    mounted() {
        if(typeof route_user_me !== 'undefined')
            this.getMe()

        if (this.currentRoute == '/')
            this.getCalendars()

        if(typeof route_user_categories !== 'undefined')
            this.getCategories();

        // Login
        document.querySelectorAll('input')?.forEach(input => {
            if(!input.classList.contains('validate'))
                return;
            input.addEventListener('focusout', (event) => {
                if (event.target.value.length < 1){
                    event.target.classList.add('invalid')
                    event.target.classList.remove('active')
                }else{
                    event.target.classList.add('active')
                    event.target.classList.remove('invalid')
                }

            })
        })

        $("#nation_id").selectize({
            create: false,
            sortField: "text",
        })

        document.querySelectorAll('input')?.forEach(input => {
            if(!input.classList.contains('validate'))
                return;
            input.addEventListener('focusin', (event) => {
                event.target.classList.remove('invalid')
                event.target.classList.add('active')
            })
        })


        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            startDate: moment().subtract(18, 'years').format("YYYY-MM-DD"),
            "maxDate": moment().subtract(18, 'years').format("YYYY-MM-DD"),
            "showDropdowns": true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('.datepicker-years').daterangepicker({
            singleDatePicker: true,
            startDate: moment().format("YYYY-MM-DD"),
            "maxDate": moment().format("YYYY-MM-DD"),
            "showDropdowns": true,
            autoUpdateInput: false,
            autoApply: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(startDate){
            console.log(startDate)
        });

        let _this = this
        $('.datepicker-years').on('apply.daterangepicker', function(ev, picker) {
            if($(this).attr('id') == 'start-date'){
                _this.newCalendarForm.start_date = picker.startDate.format('YYYY-MM-DD')
            }else if($(this).attr('id') == 'end-date'){
                _this.newCalendarForm.end_date = picker.startDate.format('YYYY-MM-DD')
            }
            $(this).val(picker.startDate.format('YYYY-MM-DD'))
        })

        document.querySelector('#terms')?.addEventListener('change', function(){
            if(this.checked){
                document.querySelector('#login').classList.add('btn-opacity-1')
            }else{
                document.querySelector('#login').classList.remove('btn-opacity-1')
            }
        })

        document.querySelector('#auth-form')?.addEventListener('submit', function(event){
            if(!document.querySelector('#terms').checked)
                event.preventDefault()
        })


        // Side Bar
        const showNavbar = (toggleId, navId, bodyId, headerId) => {
            const toggle = document.getElementById(toggleId),
                nav = document.getElementById(navId),
                bodypd = document.getElementById(bodyId),
                headerpd = document.getElementById(headerId)

                // Validate that all variables exist
            if (toggle && nav && bodypd && headerpd) {
                toggle.addEventListener('click', () => {
                    // show navbar
                    nav.classList.toggle('show')
                    // add padding to body
                    bodypd.classList.toggle('body-pd')
                    // add padding to header
                    headerpd.classList.toggle('body-pd')
                })
            }
        }
        showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')
    },
});
