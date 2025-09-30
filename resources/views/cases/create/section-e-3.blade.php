<form id="form-section-e-3">

    @if($insuredLives->isEmpty())
    <div class="alert alert-warning">
        You must have to add at least a insuref life in order to upload document here.
    </div>
    @endif

    <div class="alert alert-info">
        Any two (2) of the IDs are required for each insured life 
    </div>    

    <div class="mb-3">
        @foreach(($insuredLives ?? collect()) as $idx => $il)
            <div class="mb-3">
                <div class="fw-bold mb-2">Insured Life {{ $loop->iteration }} {{ $il->name ? ' - ' . $il->name : '' }}</div>
                @foreach (\App\Models\Document::where('type', 'insured-life')->get() as $status)
                    <div class="mb-2 row check-kyc ">
                        <div class="col-8">
                            <div class="d-flex pd-kyc">
                                <input type="checkbox" class="check-main" name="documents[{{ $il->id }}][{{ $status->id }}]" data-type="insured-life" data-record="{{ $il->id }}" id="doc-{{ $il->id }}-{{ $status->id }}" value="{{ $status->id }}" @if(isset($dArrByRecord[$il->id][$status->id]) && isset($dArrByRecord[$il->id][$status->id]['uploaded']) && $dArrByRecord[$il->id][$status->id]['uploaded']) checked @endif> &nbsp;&nbsp;&nbsp;
                                <label for="doc-{{ $il->id }}-{{ $status->id }}">{{ $status->title }}
                                    @php
                                        $docInfo = $dArrByRecord[$il->id][$status->id] ?? [];
                                        $isUploaded = isset($docInfo['uploaded']) && $docInfo['uploaded'];
                                        $expiryTxt = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                                    @endphp
                                    @if($isUploaded && $expiryTxt)
                                        <span class="text-muted"> - Exp: {{ $expiryTxt }}</span>
                                    @endif
                                </label> @tooltip('e3-document-' . $status->id)
                            </div>
                        </div>
                        <div class="col-4 d-flex flex-row justify-content-end align-items-center gap-2">
                            @php
                                $docInfo = $dArrByRecord[$il->id][$status->id] ?? [];
                                $hasExpiry = isset($docInfo['has_expiry_date']) ? (int)$docInfo['has_expiry_date'] : 1;
                                $expiryVal = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                            @endphp
                            @if(request()->route()->getName() != 'cases.view')
                            <button type="button" class="btn red-btn me-2 open-upload-modal" data-record-id="{{ $il->id }}" data-doc-id="{{ $status->id }}" data-dt-type="insured-life" data-has-expiry="{{ $hasExpiry }}" data-expiry="{{ $expiryVal }}">Upload</button>
                            @endif
                            <a target="_blank" href="{{ asset('storage/kyc-docs/' . ( isset($dArrByRecord[$il->id][$status->id]['document']) ? $dArrByRecord[$il->id][$status->id]['document'] : '' ) ) }}" id="view[{{ $il->id }}][{{ $status->id }}]" class="btn btn-primary view-file @if(isset($dArrByRecord[$il->id][$status->id]['document']) && is_file(public_path('storage/kyc-docs/' . $dArrByRecord[$il->id][$status->id]['document'])) ) @else d-none  @endif "> View </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-e-4" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>
</form>