<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" @submit.prevent="updateCategory()">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input_group field">
                    <input type="text" class="input_field validate" placeholder="Name" id='name-edit'
                        v-model="newCategory" required />
                    <label for="category" class="input_label">Name:</label>
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
