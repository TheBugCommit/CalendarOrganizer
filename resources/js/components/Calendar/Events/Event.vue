<template>
    <div class="modal fade" id="manageEvent" tabindex="-1" aria-labelledby="manageEventLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageEventLabel">{{ title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="input_group">
                                <input type="text" class="input_field" v-model="event.title" id="title">
                                <label for="title" class="input_label">Title</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input_group">
                                <input type="text" class="input_field" autocomplete="off" id="date-range">
                                <label for="date-range" class="input_label">Date Range</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input_group">
                                <label for="date-range" class="input_label">Category</label>
                                <select id="categories" class="mt-2">
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row justify-content-center">
                        <label for="date-range" class="grey-text text-start">Description</label>
                        <textarea class="col-10" id="description"></textarea>
                    </div>

                    <label for="color">Color: </label>
                    <button data-jscolor="{}" id="color"></button>

                    <label for="location">Location: </label>
                    <input type="text" v-model="event.location" id="location">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-save btn-primary" @click="saveEvent()">Save</button>
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

        hide() {
            $('#manageEvent').modal('hide')
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
                    $("#categories").selectize({
                        create: false,
                        sortField: "text",
                        onChange: function (value) {
                            _this.event.category_id = value
                        }
                    })
                })
            }).fail((response) => {
                _this.categories = _this.categories || []
                console.log(response)
            })
        },

        saveEvent() {
            if (this.editing)
                this.$parent.updateCalendarEvent()
            else
                this.$parent.storeCalendarEvent()
        }

    },

    computed: {
        title() {
            return this.editing ? 'Editing Event' : 'Creating Event'
        }
    },

    watch: {
        event: function (value) {
            let _this = this
            if (value != null) {
                tinymce.get('description').setContent(value.description)
                $("#categories")[0].selectize.setValue(_this.editing ? value.category_id : null)
                $('#date-range').data('daterangepicker').setStartDate(_this.editing ? value.start : moment())
                $('#date-range').data('daterangepicker').setEndDate(_this.editing ? value.end : moment())
                if (_this.editing) {
                    _this.event.start = $('#date-range').data('daterangepicker').startDate
                    _this.event.end = $('#date-range').data('daterangepicker').endDate
                    $('#date-range').val($('#date-range').data('daterangepicker').startDate.format('YYYY-MM-DD hh:mm:ss') + ' - ' + $('#date-range').data('daterangepicker').endDate.format('YYYY-MM-DD hh:mm:ss'));
                    document.querySelector('#color').jscolor.fromString(_this.event.color)
                } else {
                    document.querySelector('#color').jscolor.fromRGBA(255, 255, 255, 0)
                }
            }
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
            timePicker: true,
            "timePicker24Hour": true,
            "autoUpdateInput": false,
            "minDate": moment([moment().year()]).clone().format("YYYY-MM-DD hh:mm:ss"),
            "maxDate": moment([moment().year()]).clone().endOf("year").format("YYYY-MM-DD hh:mm:ss"),
            "showDropdowns": true,
            locale: {
                format: 'YYYY-MM-DD hh:mm:ss'
            }
        }

        $('#date-range').daterangepicker(config);
        $('#date-range').on('apply.daterangepicker', function (ev, picker) {
            if (!picker.startDate.isSame(picker.endDate)) {

                if (picker.startDate.format('HH:mm') == '00:00') {
                    picker.endDate.add('days', 1);
                }

                $('#date-range').val($('#date-range').data('daterangepicker').startDate.format('YYYY-MM-DD hh:mm:ss') + ' - ' + $('#date-range').data('daterangepicker').endDate.format('YYYY-MM-DD hh:mm:ss'));
                _this.event.start = picker.startDate
                _this.event.end = picker.endDate
            } else {
                console.log('this dates are the same')
            }
        })

        jscolor.presets.default = {
            format: 'hex',
            palette: [
                '#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
                '#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
                '#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
                '#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
            ],
            paletteCols: 11,
            hideOnPaletteClick: true,
            onChange: function () { _this.event.color = this.toHEXString() }
        };

        $('#manageEvent').on('hidden.bs.modal', function () {
            Object.keys(_this.event).forEach(key => {
                _this.event[key] = ''
            })
        })
    }
};
</script>
