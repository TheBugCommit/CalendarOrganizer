<template>
    <div id="eventEditTooltip">
        <div class="row">
            <div class="col-12">
                <ul class="popup-event-info">
                    <li>
                        <label for="title">Title: </label>
                        <p v-html="event.title"></p>
                    </li>
                    <li>
                        <label for="start_time">Start: </label>
                        <p v-html="eventStart"></p>
                    </li>
                    <li>
                        <label for="end_time">End: </label>
                        <p v-html="eventEnd"></p>
                    </li>
                    <li>
                        <label for="category">Category: </label>
                        <p v-html="categoryName"></p>
                    </li>
                    <li>
                        <label for="description">Description: </label>
                        <div class="short-multiline-text" style="--lines: 2" v-html="event.description"></div>
                    </li>
                    <li>
                        <label for="location">Location: </label>
                        <p v-html="event.location"></p>
                    </li>
                    <li>
                        <label for="location">User: </label>
                        <p v-html="user_email"></p>
                    </li>
                </ul>

                <div class="align-items-center d-flex mb-2">
                    <i class="event-published fa-calendar-check far ms-2" v-show="event.published == 1"></i>
                    <div class="d-flex justify-content-end w-100">
                        <button type="button" class="btn btn-edit btn-edit-popup"
                            v-if="canEditDelete && event.published == 0 && isowner" :id="'publish-'+event.id">
                            <i class="fa-calendar-check far"></i></button>
                        <button type="button" id="edit" v-if="canEditDelete"
                            class="btn btn-edit btn-edit-popup"><i class="fas fa-edit"></i></button>
                        <button type="button" id="delete" v-if="canEditDelete"
                            class="btn btn-delete btn-delete-popup"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import tippy, { roundArrow } from "tippy.js";
import "tippy.js/dist/svg-arrow.css";
import "tippy.js/animations/perspective.css";
import Swal from "sweetalert2";

export default {
    props: ["event", "target", "me", "calendar", "categories"],
    data() {
        return {
            tippyInsance: null,
        };
    },

    methods: {
        show() {
            this.tippyInsance.show();
        },

        hide() {
            this.tippyInsance.hide();
        },

        setInstance() {
            if (this.target == null) return;
            let _this = this;
            if (this.tippyInsance != null) {
                this.tippyInsance?.destroy()
                this.tippyInsance = null
            }
            this.$nextTick(() => {
                this.tippyInsance = tippy(this.target, {
                    content: $("#eventEditTooltip").html(),
                    allowHTML: true,
                    hideOnClick: true,
                    trigger: "click",
                    interactive: true,
                    arrow: roundArrow,
                    animation: "perspective-extreme",
                    placement: "auto-end",
                    appendTo: () => document.body,
                    onMount(instance) {
                        $(".tippy-box button[id^='publish']").on("click", function (event) {
                            _this.hide();
                            setTimeout(() => {
                                _this.publishEvent($(this).attr('id').split('-')[1]);
                            }, 500);
                        });
                        $(".tippy-box #edit").on("click", function (event) {
                            _this.hide();
                            setTimeout(() => {
                                _this.editEvent();
                            }, 500);
                        });
                        $(".tippy-box #delete").on("click", function (event) {
                            _this.hide();
                            setTimeout(() => {
                                _this.destroyEvent();
                            }, 500);
                        });
                    },
                })[0];

                this.show();
            });
        },

        editEvent() {
            this.$parent.editEvent(this.event.id);
        },

        destroyEvent() {
            this.$parent.deleteEvent(this.event.id);
        },

        publishEvent(event_id) {
            Swal.fire({
                title: 'Are you sure to publish this event?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, publish it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = window.location.origin + '/publish_event?id=' + event_id;
                    window.location.href = url
                }
            })
        }
    },

    computed: {
        canEditDelete() {
            return  (this.me.id == this.calendar.user_id && this.event.published == 1) || this.me.id == this.calendar.user_id || (this.me.id == this.event.user_id && this.event.published == 0)
        },
        categoryName() {
            return this.categories.find(elem => elem.id == this.event.category_id)?.name
        },
        eventStart() {
            return typeof this.event.start != 'string' ? this.event.start.format('YYYY-MM-DD hh:mm:ss') :
                this.event.start == null ? '' : moment(this.event.start).format('YYYY-MM-DD hh:mm:ss');
        },
        eventEnd() {
            return typeof this.event.end != 'string' ? this.event.end.format('YYYY-MM-DD hh:mm:ss') :
                this.event.end == null ? '' : moment(this.event.end).format('YYYY-MM-DD hh:mm:ss');
        },
        isowner() {
            return this.me.id == this.calendar.user_id;
        },
        user_email(){
            return this.event.user_email || this.me.email;
        }
    },

    watch: {
        event: function (value) {
            if (value == null) return;
            this.setInstance();
            console.log(value)
        },
        target: function (value) {
            this.tippyInsance?.destroy()
            this.tippyInsance = null
        }
    },
};
</script>
