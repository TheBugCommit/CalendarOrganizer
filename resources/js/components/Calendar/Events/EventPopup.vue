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
                </ul>
                <div class="d-flex justify-content-end gap-2">
                    <a :href="'/publish_event?id=' + event.id" class="btn btn-edit btn-edit-popup" v-if="canEditDelete && event.published == 0" id="upload">
                    <i class="fas fa-upload"></i></a>
                    <button type="button" class="btn btn-edit btn-edit-popup" v-if="canEditDelete" id="edit"><i
                            class="fas fa-edit"></i></button>
                    <button type="button" class="btn btn-delete btn-delete-popup" v-if="canEditDelete" id="delete"><i
                            class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import tippy, { roundArrow } from "tippy.js";
import "tippy.js/dist/svg-arrow.css";
import "tippy.js/animations/perspective.css";

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
    },

    computed: {
        canEditDelete() {
            return this.me.id == this.calendar.user_id || this.me.id == this.event.user_id
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
        }
    },

    watch: {
        event: function (value) {
            if (value == null) return;
            this.setInstance();
        },
    },
};
</script>
