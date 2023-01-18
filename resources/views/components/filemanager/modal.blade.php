<div class="modal modal-xl fade" id="nxtUploaderModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
  aria-hidden="true">
  <div class="modal-dialog modal-adaptive" role="document">
    <div class="modal-content h-100">
      <div class="modal-header pb-0 bg-light">
        <div class="uppy-modal-nav">
          <ul class="nav nav-tabs border-0">
            <li class="nav-item uploader-nav-menu">
              <a class="nav-link active font-weight-medium text-dark uploader-nav-menu-link" data-toggle="tab"
                data-tab="#uploader--selectFiles">Select File</a>
            </li>
            <li class="nav-item uploader-nav-menu">
              <a class="nav-link font-weight-medium text-dark uploader-nav-menu-link" data-toggle="tab"
                data-tab="#uploader--uploadFiles">Upload New</a>
            </li>
          </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <span aria-hidden="true"></span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tab-content h-100">
          <div class="tab-pane active h-100 uploader-tab-pane" id="uploader--selectFiles">
            <div class="uploader-filter pt-1 pb-3 border-bottom mb-4">
              <form class="my-form text-black nxt-filter-form" data-url="{{ route('media.files.filter') }}">
                <div class="row">
                  <div class="col-lg-9 col-md-6 col-sm-12">
                    <div class="d-flex w-100 justify-content-lg-start justify-content-between">
                      <div class="form-group me-3">
                        <select class="form-select file-sort" name="file-sort">
                          <option value="newest" selected>Sort by Newest</option>
                          <option value="oldest">Sort by Oldest</option>
                          <option value="smallest">Sort by Smallest</option>
                          <option value="largest">Sort by Largest</option>
                        </select>
                      </div>

                      <div class="form-check mt-2 me-3">
                        <input class="form-check-input selected-file" type="checkbox" value="">
                        <label class="form-check-label text-dark float-start" for="flexCheckChecked">
                          Selected Only
                        </label>
                      </div>

                      <div class="file-preview-control d-none">
                        <a href="#!" class="btn btn-primary">Preview</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 col-sm-12 mr-0 ms-auto float-end">
                    <div class="uploader-search text-right">
                      <input type="text" class="form-control form-control-xs selected-uploaded-file file-search"
                        name="uploader-search" placeholder="Search your files">
                      <i class="search-icon d-md-none"><span></span></i>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="overflow-auto upload-content" style="height: calc(98vh - 303px)">
              <!-- attachments go here -->
            </div>
          </div>

          <div class="tab-pane h-100" id="uploader--uploadFiles">
            <div id="drop-area" class="d-flex flex-column align-items-center">
              <form class="text-center mb-3">
                <p>Drop File(s) Here</p>
                <input type="file" class="upload-files" name="files" multiple>
                <label class="button">Select File(s)</label>
              </form>
              <progress id="uploader--progressBar" max=100 value=0></progress>
            </div>
            <div id="uploadedFileGallery">

            </div>
          </div>
        </div>
        <input type="hidden" id="fileUploadUrl" value="{{ route('media.upload') }}" />
      </div>
      <div class="modal-footer justify-content-between bg-light uploader-footer">
        <div class="flex-grow-1 overflow-hidden d-flex">
          <div class="">
            <div class="text-dark"><span class="count-result">0</span> File selected</div>
            <button type="button" class="btn-link btn btn-sm p-0 float-start clear-selected">Clear</button>
          </div>
          <div class="mb-0 ms-3 mt-2">
            <button type="button" class="btn btn-sm btn-primary uploader-prev-btn">Prev</button>
            <button type="button" class="btn btn-sm btn-primary uploader-next-btn">Next</button>
          </div>
        </div>
        <button type="button" class="btn btn-sm btn-primary file-add">
          Add Files
        </button>
      </div>
    </div>
  </div>
</div>
