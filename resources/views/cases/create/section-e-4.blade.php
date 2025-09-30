<form id="form-section-e-4">

    @if($beneficiaries->isEmpty())
    <div class="alert alert-warning">
        You must have to add at least a beneficiary in order to upload document here.
    </div>
    @endif

    <div class="alert alert-info">
        Any two (2) of the IDs are required for each beneficiary
    </div>

    <div class="mb-3">
        @foreach(($beneficiaries ?? collect()) as $idx => $bf)
            <div class="mb-3">
                <div class="fw-bold mb-2">Beneficiary {{ $loop->iteration }} {{ $bf->name ? ' - ' . $bf->name : '' }}</div>
                @foreach (\App\Models\Document::where('type', 'beneficiary')->get() as $status)
                    <div class="mb-2 row check-kyc ">
                        <div class="col-8">
                            <input type="checkbox" class="check-main" name="documents[{{ $bf->id }}][{{ $status->id }}]" data-type="beneficiary" data-record="{{ $bf->id }}" id="doc-{{ $bf->id }}-{{ $status->id }}" value="{{ $status->id }}" @if(isset($dArrByRecord[$bf->id][$status->id]) && isset($dArrByRecord[$bf->id][$status->id]['uploaded']) && $dArrByRecord[$bf->id][$status->id]['uploaded']) checked @endif> &nbsp;&nbsp;&nbsp;
                                <label for="doc-{{ $bf->id }}-{{ $status->id }}">{{ $status->title }}
                                    @php
                                        $docInfo = $dArrByRecord[$bf->id][$status->id] ?? [];
                                        $isUploaded = isset($docInfo['uploaded']) && $docInfo['uploaded'];
                                        $expiryTxt = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                                    @endphp
                                    @if($isUploaded && $expiryTxt)
                                        <span class="text-muted"> - Exp: {{ $expiryTxt }}</span>
                                    @endif
                                </label> @tooltip('e4-document-' . $status->id)
                                <div class="d-flex pd-kyc">
                            </div>
                        </div>
                        <div class="col-4 d-flex flex-row justify-content-end align-items-center gap-2">
                            @php
                                $docInfo = $dArrByRecord[$bf->id][$status->id] ?? [];
                                $hasExpiry = isset($docInfo['has_expiry_date']) ? (int)$docInfo['has_expiry_date'] : 1;
                                $expiryVal = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                            @endphp
                            @if(request()->route()->getName() != 'cases.view')
                            <button type="button" class="btn red-btn me-2 open-upload-modal" data-record-id="{{ $bf->id }}" data-doc-id="{{ $status->id }}" data-dt-type="beneficiary" data-has-expiry="{{ $hasExpiry }}" data-expiry="{{ $expiryVal }}">Upload</button>
                            @endif
                            <a target="_blank" href="{{ asset('storage/kyc-docs/' . ( isset($dArrByRecord[$bf->id][$status->id]['document']) ? $dArrByRecord[$bf->id][$status->id]['document'] : '' ) ) }}" id="view[{{ $bf->id }}][{{ $status->id }}]" class="btn btn-primary view-file @if(isset($dArrByRecord[$bf->id][$status->id]['document']) && is_file(public_path('storage/kyc-docs/' . $dArrByRecord[$bf->id][$status->id]['document'])) ) @else d-none  @endif "> View </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-f-1" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>
</form>