<template>
    <div>
        <helper-component v-for="helper in helpers" :key="helper.id" :user="helper" @remove="removeHelper()">
        </helper-component>
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
            //recull tots els usuaris
        },

        addHelpers() {
            //afegeix els usuaris selecionats al calendari
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
        }
    },
    mounted() {
        this.getHelpers()
        this.getAllUsers()
    }
};
</script>
