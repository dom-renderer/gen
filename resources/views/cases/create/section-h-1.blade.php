<form id="form-section-h-1">
    <style>
        .sortable-placeholder {
            height: 48px;
            border: 2px dashed #ccc;
            background: #f8f9fa;
            margin-bottom: .5rem;
        }
        .check-kyc .btn.red-btn.me-2 {
            padding: 10px !important;
            margin-right: 20px !important;
            height: auto;
            /* width: 120px; */
            margin-top: 10px;
        }
    </style>

    <div class="row mb-3">
        <div class="col-12">
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addNewFormModal">
                <i class="fas fa-plus"></i> Add New Form
            </button>
            <button type="button" class="btn btn-secondary" id="toggleSortMode">
                <i class="fas fa-sort"></i> Sort Forms
            </button>
        </div>
    </div>

    <div class="row mb-3" id="sortable-forms-container">
        <div class="col-xl-12">
            @foreach ($h1Data as $form)
            <div class="mb-2 row check-kyc sortable-item mn-nm" data-id="{{ $form->id }}" style="border-bottom: 1px solid #0000004d;">
                <div class=" col-lg-6 col-xl-8">
                    <i class="fas fa-grip-vertical drag-handle" style="cursor: move; display: none;margin-right:20px;"></i>
                    <label> {{ $form->title }} @tooltip('custom_document_' . $form->title) </label> 
                    <div class="d-flex pd-kyc"></div>
                </div>
                <div class="col-lg-6 col-xl-4 d-flex justify-content-end flex-wrap">
                    <a href="{{ route('remove-upload-form-doc', ['id' => encrypt($form->id)]) }}" class="btn red-btn me-2 delete-doc-perm">  <i class="fa fa-trash"></i> Delete  </a>
                    @if(!empty($form->file) && is_file(storage_path("app/public/customized-form/{$form->file}")))
                    <a href="{{ asset("storage/customized-form/{$form->file}") }}" class="btn red-btn me-2" download>  <i class="fa fa-download"></i> Download  </a>
                    <button type="button" class="btn red-btn me-2 remove-uploaded-file" data-doc-id="{{ $form->id }}"> <i class="fa fa-times"></i> Remove </button>
                    @endif
                    <label for="h1-upload-{{ $form->id }}" class="btn red-btn me-2"> <i class="fa fa-upload"></i> Upload </label>
                    <input id="h1-upload-{{ $form->id }}" type="file" class="d-none h1-upload-existing" data-doc-id="{{ $form->id }}">
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        @endif
    </div>

 </form>