<div class="modal fade" id="newCalendarModal" tabindex="-2" aria-labelledby="newCalendarModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" @submit.prevent="storeCalendar()">
            <div class="modal-header">
                <h5 class="modal-title" id="newCalendarModalLabel">New Calendar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="input_group field">
                    <input type="text" class="input_field validate" placeholder="Title" id='title'
                        v-model="newCalendarForm.title" required />
                    <label for="title" class="input_label">Title</label>
                    <span class="input-error d-none caps-lock">Caps Lock activated</span>
                </div>

                <div class="input_group field">
                    <input type="text" class="input_field datepicker-years" placeholder="Start Date"
                        id='start-date' />
                    <label for="start_date" class="input_label">Start date</label>
                </div>

                <div class="input_group field">
                    <input type="text" class="input_field  datepicker-years" placeholder="End Date"
                        id='end-date' />
                    <label for="end_date" class="input_label">End date</label>
                </div>

                <div class="input_group field">
                    <input type="textarea" rows="3" class="input_field validate" v-model="newCalendarForm.description">
                    <label for="title" class="input_label">Description</label>
                    <span class="input-error d-none caps-lock">Caps Lock activated</span>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <span class="input-error">@{{ error }}</span>
                <button type="submit" class="btn btn-save">Save</button>
            </div>
        </form>
    </div>
</div>
