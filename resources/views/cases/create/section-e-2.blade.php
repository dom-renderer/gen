<form id="form-section-e-2">

    <div class="alert alert-info">
        Any two (2) of the IDs are required
    </div>

    <div class="mb-3">
        @foreach (\App\Models\Document::where('type', 'controlling-person')->get() as $status)
        <div class="mb-2 row check-kyc ">
            <div class="col-8">
                <div class="d-flex pd-kyc">
                <input type="checkbox" class="check-main" name="documents[{{ $status->id }}]" data-type="controlling-person" id="doc-{{ $status->id }}" value="{{ $status->id }}" @if(isset($dArr[$status->id]) && isset($dArr[$status->id]['uploaded']) && $dArr[$status->id]['uploaded']) checked @endif> &nbsp;&nbsp;&nbsp;
                <label for="doc-{{ $status->id }}">{{ $status->title }}
                    @php
                        $docInfo = $dArr[$status->id] ?? [];
                        $isUploaded = isset($docInfo['uploaded']) && $docInfo['uploaded'];
                        $expiryTxt = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                    @endphp
                    @if($isUploaded && $expiryTxt)
                        <span class="text-muted"> - Exp: {{ $expiryTxt }}</span>
                    @endif
                </label> @tooltip('e2-document-' . $status->id)
                </div>
            </div>
            <div class="col-4 d-flex flex-row justify-content-end align-items-center gap-2">
                @php
                    $docInfo = $dArr[$status->id] ?? [];
                    $hasExpiry = isset($docInfo['has_expiry_date']) ? (int)$docInfo['has_expiry_date'] : 1;
                    $expiryVal = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                @endphp
                @if(request()->route()->getName() != 'cases.view')
                <button type="button" class="btn red-btn me-2 open-upload-modal" data-doc-id="{{ $status->id }}" data-dt-type="controlling-person" data-has-expiry="{{ $hasExpiry }}" data-expiry="{{ $expiryVal }}">Upload</button>
                @endif
                <a target="_blank" href="{{ asset('storage/kyc-docs/' . ( isset($dArr[$status->id]['document']) ? $dArr[$status->id]['document'] : '' ) ) }}" id="view[{{ $status->id }}]" class="btn btn-primary view-file @if(isset($dArr[$status->id]['document']) && is_file(public_path('storage/kyc-docs/' . $dArr[$status->id]['document'])) ) @else d-none  @endif "> View </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-e-3" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>
</form>