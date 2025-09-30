<form id="form-section-h-2" enctype="multipart/form-data" method="POST">
    
    @php
        $forms = \App\Models\DownloadableDocument::orderBy('ordering')->get();
        $uploadedByFormId = collect();
        if ($forms->count() > 0) {
            $uploadedByFormId = \App\Models\UploadableDocument::whereIn('downloadable_document_id', $forms->pluck('id'))->get()->keyBy('downloadable_document_id');
        }

    @endphp

    <div class="mb-3">
        @forelse ($forms as $form)
            <div class="mb-2 row check-kyc mn-nm">
                <div class="col-xl-6 col-lg-6">
                    <label>{{ $form->title }}</label>
                    <div class="d-flex pd-kyc"></div>
                </div>
                <div class="col-xl-6 col-lg-6 d-flex flex-row justify-content-end">
                    @php $uploadedDoc = $uploadedByFormId->get($form->id); @endphp
                    @if($uploadedDoc && !empty($uploadedDoc->file) && is_file(storage_path("app/public/kyc-docs/{$uploadedDoc->file}")))
                    <a href="{{ asset("storage/kyc-docs/{$uploadedDoc->file}") }}" class="btn red-btn me-2" download> <i class="fa fa-download"></i> Download </a>
                    @endif
                    <button type="button" class="btn red-btn me-2 open-upload-modal" data-doc-id="{{ $form->id }}" data-dt-type="downloadable-document"> <i class="fa fa-upload"></i> Upload </button>
                </div>
            </div>
            <hr>
        @empty
        @endforelse
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        @endif
    </div>

</form>