<div class="modal fade" id="createEvent" tabindex="-1" aria-labelledby="createEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createEventLabel">Create Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="title">Title: </label>
                <input type="text" name="title" id="title">

                <label for="start_time">Start: </label>
                <input type="datetime-local" name="start_time" id="start_time">

                <label for="end_time">End: </label>
                <input type="datetime-local" name="end_time" id="end_time">

                <select name="category_id">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <textarea name="description" id="description"></textarea>

                <label for="color">Color: </label>
                <input type="text" id="color" class="color-field" name="color"  />

                <label for="location">Location: </label>
                <input type="text" name="location" id="location">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
