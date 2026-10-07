<!-- Modal for View / Show Course Details -->
<div class="modal fade" id="showCourseModal" tabindex="-1" aria-labelledby="showCourseModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="showCourseModalTitle">
                    <i class="bi bi-info-circle text-info me-2"></i>Course Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                    <div>
                        <span id="show_courseCode" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6"></span>
                        <h4 id="show_courseName" class="fw-bold text-dark mt-2 mb-0"></h4>
                    </div>
                    <span id="show_courseId" class="badge bg-light text-secondary border"></span>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted d-block small">Credits</span>
                            <span id="show_courseCredits" class="fw-bold fs-6 text-dark"></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted d-block small">Enrolled Students</span>
                            <span id="show_courseStudents" class="fw-bold fs-6 text-primary"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold text-secondary mb-1">Description</h6>
                    <div id="show_courseDescription" class="p-3 bg-light rounded-3 text-dark small"></div>
                </div>

                <div class="d-flex justify-content-between gap-2 mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="show_courseFullPageLink" href="#" class="btn btn-info text-white">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Full Page View
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
