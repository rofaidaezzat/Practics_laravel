<!-- Modal for Create Course -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="createCourseModalTitle">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Add New Course
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="createModalAlert" class="alert alert-danger d-none"></div>

                <form id="createCourseForm" onsubmit="handleCreateSubmit(event)">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="create_code" class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_code" required placeholder="e.g. CS101">
                        </div>
                        <div class="col-md-7 mb-3">
                            <label for="create_name" class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_name" required placeholder="e.g. Database Systems">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_credits" class="form-label fw-semibold">Credits</label>
                        <input type="number" class="form-control" id="create_credits" min="1" max="10" value="3" placeholder="3">
                    </div>

                    <div class="mb-3">
                        <label for="create_description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="create_description" rows="3" placeholder="Brief course overview..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="createSaveBtn">
                            <i class="bi bi-check2-circle me-1"></i> Save Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
