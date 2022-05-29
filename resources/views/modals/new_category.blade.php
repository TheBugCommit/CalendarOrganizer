<div class="modal fade" id="newCategoryModal" tabindex="-1" aria-labelledby="newCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" @submit.prevent="storeCategory()">
            <div class="modal-header">
                <h5 class="modal-title" id="newCategoryModalLabel">New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input_group field">
                    <input type="text" class="input_field validate" placeholder="Name" id='name'
                        v-model="newCategory" required />
                    <label for="category" class="input_label">Name:</label>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <span class="input-error">@{{ error }}</span>
                <button type="submit" class="btn btn-save">Save</button>
            </div>
        </form>
    </div>
</div>
