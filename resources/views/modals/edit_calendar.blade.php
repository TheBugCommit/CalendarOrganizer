<div class="modal fade" id="editCalendarModal" tabindex="-2" aria-labelledby="editCalendarModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" @submit.prevent="updateCalendar()">
            <div class="modal-header">
                <h5 class="modal-title" id="editCalendarModalLabel">Edit Calendar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="input_group field">
                    <input type="text" class="input_field validate" placeholder="Title" id='title'
                        v-model="editCalendarForm.title" required />
                    <label for="title" class="input_label">Title</label>
                </div>

                <div class="input_group field">
                    <input type="text" class="input_field " placeholder="Start Date"
                        id='start-date-edit' />
                    <label for="start-date-edit" class="input_label">Start date</label>
                </div>

                <div class="input_group field">
                    <input type="text" class="input_field " placeholder="End Date"
                        id='end-date-edit' />
                    <label for="end-date-edit" class="input_label">End date</label>
                </div>

                <div class="input_group field">
                    <input type="textarea" rows="3" v-model="editCalendarForm.description">
                    <label for="title" class="input_label">Description</label>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <span class="input-error">@{{ error }}</span>
                <button type="submit" class="btn btn-save">Save</button>
            </div>
        </form>
    </div>
</div>
