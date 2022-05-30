<template>
    <div class="row p-4">

        <div class="col-12 col-md-4 ">
            <div>
                <label for="users" class="grey-text">Registred Users</label>
                <select class="selectize" id="users">
                    <option value=""></option>
                    <option v-for="user in users" :key="user.email" :value="user.email">{{ user.email }}</option>
                </select>
            </div>
            <div class="input_group">
                <input type="email" id="custom-user" class="input_field validate" v-model="custom_user.email">
                <span class="input-error d-none caps-lock">Caps Lock activated</span>
                <label for="custom-user" class="input_label">Non existent user</label>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <table class="table" v-if="selected_users.length">
                <thead>
                    <tr>
                        <th>Users</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in selected_users" :key="index">
                        <td>{{ user }}
                        <button type="button" class="btn btn-delete" @click="removeSelected({ email: user })"><i
                                class="fas fa-times"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row justify-content-between mt-3 p-4">
            <button type="button" class="col-4 col-md-2 btn btn-default" @click="appendCustomUser">Append</button>
            <button type="button" class="col-4 col-md-2 btn btn-default" v-if="selected_users.length"
                @click="addHelpers()">Add selected Helpers</button>
        </div>

        <ul class="mt-3">
            <helper-component v-for="helper in helpers" :key="helper.id" :user="helper" @remove="removeHelper">
            </helper-component>
        </ul>
    </div>
</template>

<script>
import HelperComponent from './HelperComponent.vue';
import Swal from "sweetalert2";

export default {
    components: {
        HelperComponent
    },
    data() {
        return {
            helpers: [],
            users: [],
            selected_users: [],
            custom_user: { email: '' },
            selected_user: '',
        }
    },
    methods: {
        async getHelpers() {
            let calendar_id = window.location.href.split("/").pop();
            let data = await $.ajax({
                url: route_helpers_get,
                method: "GET",
                dataType: "JSON",
                data: { id: calendar_id }
            }).fail((error) => {
                Swal.fire('Oppss!', 'Can not get helpers', 'error')
            })

            this.helpers = data
        },

        getAllUsers() {
            let _this = this

            $.ajax({
                url: route_user_all,
                method: "GET",
                dataType: 'JSON',
            }).done((response) => {
                _this.users = response
                _this.users = _this.users.filter(user => {
                    return !_this.helpers.find(elem => elem.email == user.email) && _this.$root?.me?.email != user.email
                })
                _this.$nextTick(function () {
                    let select_users = $('#users').selectize({
                        create: false,
                        sortField: 'text',
                        onChange: function onChange(value) {
                            _this.selected_user = value;
                        }
                    })
                })

            }).fail((error) => {
                Swal.fire('Oppss!', 'Can not get users', 'error')
            })
        },

        addHelpers() {
            let _this = this
            let calendar_id = window.location.href.split("/").pop();

            if (this.selected_users.length == 0)
                Swal.fire('Oppss!', 'You doesn\'t select any helper', 'warning')

            $.ajax({
                url: route_helpers_add,
                method: "POST",
                dataType: "JSON",
                data: { users: _this.selected_users, calendar_id: calendar_id }
            }).done((response) => {
                _this.selected_users = []
                Swal.fire('Succes!', response?.responseJSON?.message, 'success')
            }).fail((error) => {
                Swal.fire('Oppss!', error?.responseJSON?.message, 'error')
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
                Swal.fire('Oppss!', error?.responseJSON?.message, 'error')
            })
        },

        appendCustomUser() {
            if (this.selected_user != '') {
                this.selected_users.push(this.selected_user)
                const index = this.users.findIndex(elem => elem.email == this.selected_user)
                if (index != -1)
                    this.users.splice(index, 1);
                this.selected_user = ''
                this.$nextTick(() => {
                    $('#users')[0].selectize.clearOptions()
                    $('#users')[0].selectize.addOption(this.users.map(elem => { return { text: elem.email, value: elem.email } }))
                })
            } else if (this.custom_user.email != '' && this.validEmail(this.custom_user.email)) {
                this.selected_users.push(this.custom_user.email)
                this.custom_user.email = ''
            } else if (this.custom_user.email != '' && !this.validEmail(this.custom_user.email)) {
                Swal.fire('Oppss!', 'Pleas enter a valid email address', 'warning')
            }
            $('#users')[0].selectize.setValue('')
        },

        removeSelected(user) {
            const index = this.selected_users.findIndex(elem => elem == user.email)
            if (index > -1) {
                this.selected_users.splice(index, 1);
                this.users.push(user)

                this.$nextTick(() => {
                    $('#users')[0].selectize.clearOptions()
                    $('#users')[0].selectize.addOption(this.users.map(elem => { return { text: elem.email, value: elem.email } }))
                })
            }
        },

        validEmail(email) {
            return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)
        }
    },

    mounted() {
        let _this = this;
        (async function () {
            await _this.getHelpers()
            _this.getAllUsers()
        })(_this)
    }
};
</script>
