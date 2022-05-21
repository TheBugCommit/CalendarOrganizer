require('./bootstrap');

import CalendarsComponent from './components/CalendarsComponent.vue'
import LoadingComponent from './components/LoadingComponent.vue'

window.Vue = require('vue').default;

Vue.component('loading-component', LoadingComponent);
Vue.component('calendars-component', CalendarsComponent);

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

const app = new Vue({
    el: '#app',
    data: {
    },

    mounted() {

    },

    created() {

    },

    methods: {

    },

    watch: {

    }
});
