<div class="modal fade" id="kycUploadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">File</label>
            <input type="file" class="form-control" id="kyc-modal-file">
        </div>
        <div class="form-check mb-2 ps-0">
            <input class="form-check-input parent-checkbox check-main" type="checkbox" id="kyc-modal-has-expiry">
            <label class="form-check-label fnt-dbt" for="kyc-modal-has-expiry">Has expiry date</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Expiry date</label>
            <input type="text" class="form-control" id="kyc-modal-expiry" placeholder="YYYY-MM-DD" readonly>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="kyc-modal-upload-btn">Upload</button>
      </div>
    </div>
  </div>
</div>

