<template>
    <div class="modal fade" id="manageEvent" tabindex="-1" aria-labelledby="manageEventLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageEventLabel">{{ title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="title">Title: </label>
                    <input type="text" v-model="event.title" id="title">

                    <label for="start">Start: </label>
                    <input type="text" id="start">

                    <label for="end">End: </label>
                    <input type="text" id="end">

                    <select v-model="event.category_id" id="categories">
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>

                    <textarea id="description"></textarea>

                    <label for="color">Color: </label>
                    <button data-jscolor="{}" id="color"></button>
                    <!--Falta color-->

                    <label for="location">Location: </label>
                    <input type="text" v-model="event.location" id="location">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="saveEvent()">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["event", 'editing'],
    data() {
        return {
            categories: null,
        };
    },

    methods: {
        toggle() {
            $('#manageEvent').modal('toggle')
        },

        getUserCategories() {
            let _this = this;

            $.ajax({
                url: route_user_categories,
                dataType: "JSON",
                method: 'GET',
            }).done((response) => {
                _this.categories = response
                _this.$nextTick(function () {
                    $("select").selectize({
                        create: false,
                        sortField: "text",
                    })
                })
            }).fail((response) => {
                _this.categories = _this.categories || []
                console.log(response)
            })
        },

        saveEvent() {
            if (this.editing)
                this.$parent.updateCalendarEvent(this.event)
            else
                this.$parent.storeCalendarEvent(this.event)
        }

    },

    computed: {
        title() {
            return this.editing ? 'Editing Event' : 'Creating Event'
        }
    },

    watch: {
        event: function (value) {
            if (value != null)
                tinymce.get('description').setContent(value.description)
        }
    },

    mounted() {
        let _this = this

        this.getUserCategories()

        tinymce.init({
            selector: 'textarea',
            menubar: false,
            resize: false,
            plugins: ['lists emoticons'],
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent ' +
                'forecolor backcolor emoticons'
        }).then((instance) => {
            instance[0].on('keyup', function () {
                _this.event.description = tinymce.activeEditor.getContent()
            })
        });
        const config = {
            "singleDatePicker": true,
            "showDropdowns": true,
            "timePicker": true,
            "timePicker24Hour": true,
            "linkedCalendars": false,
            "showCustomRangeLabel": false,
            "autoUpdateInput": false,
            "minDate": moment([moment().year()]).clone().format("YYYY-MM-DD hh:mm:ss"),
            "maxDate": moment([moment().year()]).clone().endOf("year").format("YYYY-MM-DD hh:mm:ss"),
            //"buttonClasses": "btn btn-smd",
            //"applyButtonClasses": "btn-primaryd",
            //"cancelClass": "btn-defaulta"
        }

        $('#start').daterangepicker(config, function (start, end, label) {
            _this.event.start = start
        });

        $('#end').daterangepicker(config, function (start, end, label) {
            _this.event.end = start
        });

        $('#start,#end').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD hh:mm:ss'))
        });

        jscolor.presets.default = {
            format: 'hex',
            palette: [
                '#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
                '#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
                '#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
                '#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
            ],
            paletteCols:11,
            hideOnPaletteClick:true,
            onChange: function(){ _this.event.color = this.toHEXString() }
        };

        jscolor.install();
        document.querySelector('#color').jscolor.fromString(_this.event.color)
    }
};
</script>
