<template>
  <div id="eventEditTooltip">
    <div class="row">
      <div class="col-12" v-if="event != null">
        <button type="button" class="btn" id="edit">Edit</button>
        <button type="button" class="btn" id="delete">Delete</button>

        <label for="title">Title: </label>
        <p v-html="event.title"></p>

        <label for="start_time">Start: </label>
        <p v-html="event.start"></p>

        <label for="end_time">End: </label>
        <p v-bind="event.end"></p>

        Caegory:
        <p v-html="event.category_id"></p>
        description:
        <div v-html="event.description"></div>
        <label for="color">Color: </label>
        <p v-html="event.color"></p>

        <label for="location">Location: </label>
        <p v-html="event.location"></p>
      </div>
    </div>
  </div>
</template>

<script>
import tippy, { createSingleton, roundArrow } from "tippy.js";
import "tippy.js/dist/svg-arrow.css";
import "tippy.js/animations/perspective.css";

export default {
  props: ["event", "target"],
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

  watch: {
    event: function (value) {
      if (value == null) return;
      this.setInstance();
    },
  },
};
</script>
