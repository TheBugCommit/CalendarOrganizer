<template>
    <div>
        <helper-component v-for="helper in helpers" :key="helper.id" :user="helper" @remove="removeHelper">
        </helper-component>

        <div>
            Selected
            ----
            <p v-for="(user, index) in selected_users" :key="index">{{ user }}</p>
        </div>

        <div>
            Coose
            ----
            <div v-for="user in users" :key="user.id">
                <label for="">{{ user.email }}</label>
                <input type="checkbox" v-model="selected_users" :value="user.email" />
            </div>
            custom user
            <input type="text" v-model="custom_user.email">
            <button type="button" @click="appendCustomUser()"></button>
        </div>

        <button type="button" @click="addHelpers()">AddHelpers</button>
    </div>
</template>

<script>
import HelperComponent from './HelperComponent.vue';
export default {
    components: {
        HelperComponent
    },
    data() {
        return {
            helpers: [],
            users: [],
            selected_users: [],
            custom_user: { email: ''},
        }
    },
    methods: {
        getHelpers() {
            let _this = this
            let calendar_id = window.location.href.split("/").pop();
            $.ajax({
                url: route_helpers_get,
                method: "GET",
                dataType: "JSON",
                data: { id: calendar_id }
            }).done((response) => {
                _this.helpers = response || []
            }).fail((error) => {
                console.error(error)
            })
        },

        getAllUsers() {
            let _this = this

            $.ajax({
                url: route_user_all,
                method: "GET",
                dataType: 'JSON',
            }).done((response) => {
                _this.users = response
            }).fail((error) => {
                console.error(error)
            })
        },

        addHelpers() {
            let _this = this
            let calendar_id = window.location.href.split("/").pop();

            $.ajax({
                url: route_helpers_add,
                method: "POST",
                dataType: "JSON",
                data: { users: _this.selected_users, calendar_id: calendar_id}
            }).done((response) => {
                _this.helpers.push(_this.selected_users)
                _this.selected_users = []
                _this.getHelpers()
            }).fail((error) => {
                console.error(error)
            })
        },

        removeHelper(id) {
            let _this = this;
            let calendar_id = window.location.href.split("/").pop();
            $.ajax({
                url: route_helpers_remove,
                method: "DELETE",
                data: { calendar_id: calendar_id, user_id: id },
                dataType: "JSON",
            }).done((response) => {
                let helper_id = _this.helpers.findIndex(helper => helper.id === id)
                _this.helpers.splice(helper_id, 1)
            }).fail((error) => {
                console.error(error)
            })
        },

        appendCustomUser(){
            if(this.custom_user.email != '')
                this.selected_users.push(this.custom_user.email)
            this.custom_user.email = ''
        }
    },
    mounted() {
        this.getHelpers()
        this.getAllUsers()
    }
};
</script>
