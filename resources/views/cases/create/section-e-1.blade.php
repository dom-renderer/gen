<form id="form-section-e-1">

    @if($policyHolders->isEmpty())
    <div class="alert alert-warning">
        You must have to add at least a policyholder in order to upload document here.
    </div>
    @endif

    <div class="alert alert-info">
        Any two (2) of the IDs are required for each policyholder
    </div>    

    <div class="mb-3">
        <label class="form-label fw-bold mb-3"> Type</label>
        <select name="type" id="section-e-1-type">
            <option value=""></option>
            @foreach (['individual', 'trust', 'llc', 'corporation', 'other'] as $document)
                <option value="{{ $document }}" @if($loop->first) selected @endif> {{ $document == 'llc' ? 'LLC' : ucwords($document) }} </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3 all-cbox check-kyc" id="container-for-ommit-documents-e1-individual">
        @foreach(($policyHolders ?? collect()) as $idx => $ph)
            <div class="mb-3">
                <div class="fw-bold mb-2">Policyholder {{ $loop->iteration }} {{ $ph->name ? ' - ' . $ph->name : '' }}</div>
                @foreach (\App\Models\Document::where('status', 'individual')->get() as $status)
                    <div class="mb-2 row">
                        <div class="col-8">
                            <div class="d-flex pd-kyc">
                                <input type="checkbox" class="check-main" name="documents[{{ $ph->id }}][{{ $status->id }}]" data-type="policy-holder" data-record="{{ $ph->id }}" id="doc-{{ $ph->id }}-{{ $status->id }}" value="{{ $status->id }}" @if(isset($dArrByRecord[$ph->id][$status->id]) && isset($dArrByRecord[$ph->id][$status->id]['uploaded']) && $dArrByRecord[$ph->id][$status->id]['uploaded']) checked @endif> &nbsp;&nbsp;&nbsp;
                                <label for="doc-{{ $ph->id }}-{{ $status->id }}">{{ $status->title }}
                                    @php
                                        $docInfo = $dArrByRecord[$ph->id][$status->id] ?? [];
                                        $isUploaded = isset($docInfo['uploaded']) && $docInfo['uploaded'];
                                        $expiryTxt = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                                    @endphp
                                    @if($isUploaded && $expiryTxt)
                                        <span class="text-muted"> - Exp: {{ $expiryTxt }}</span>
                                    @endif
                                </label> @tooltip('e1-document-' . $status->id)
                            </div>
                        </div>
                        <div class="col-4 d-flex flex-row justify-content-end align-items-center gap-2">
                            @php
                                $docInfo = $dArrByRecord[$ph->id][$status->id] ?? [];
                                $hasExpiry = isset($docInfo['has_expiry_date']) ? (int)$docInfo['has_expiry_date'] : 1;
                                $expiryVal = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                            @endphp
                            @if(request()->route()->getName() != 'cases.view')
                            <button type="button" class="btn red-btn me-2 open-upload-modal" data-record-id="{{ $ph->id }}" data-doc-id="{{ $status->id }}" data-dt-type="policy-holder" data-has-expiry="{{ $hasExpiry }}" data-expiry="{{ $expiryVal }}">Upload</button>
                            @endif
                            <a target="_blank" href="{{ asset('storage/kyc-docs/' . ( isset($dArrByRecord[$ph->id][$status->id]['document']) ? $dArrByRecord[$ph->id][$status->id]['document'] : '' ) ) }}" id="view[{{ $ph->id }}][{{ $status->id }}]" class="btn btn-primary view-file @if(isset($dArrByRecord[$ph->id][$status->id]['document']) && is_file(public_path('storage/kyc-docs/' . $dArrByRecord[$ph->id][$status->id]['document'])) ) @else d-none  @endif "> View </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mb-3 all-cbox d-none" id="container-for-ommit-documents-e1-trust">
        @foreach(($policyHolders ?? collect()) as $idx => $ph)
            <div class="mb-3">
                <div class="fw-bold mb-2">Policyholder {{ $loop->iteration }} {{ $ph->name ? ' - ' . $ph->name : '' }}</div>
                @foreach (\App\Models\Document::where('status', 'trust')->get() as $status)
                <div class="mb-2 row">
                    <div class="col-8">
                        <input type="checkbox"  class="check-main" name="documents[{{ $ph->id }}][{{ $status->id }}]" data-type="policy-holder" data-record="{{ $ph->id }}" id="doc-{{ $ph->id }}-{{ $status->id }}" value="{{ $status->id }}" @if(isset($dArrByRecord[$ph->id][$status->id]) && isset($dArrByRecord[$ph->id][$status->id]['uploaded']) && $dArrByRecord[$ph->id][$status->id]['uploaded']) checked @endif> &nbsp;&nbsp;&nbsp;
                        <label for="doc-{{ $ph->id }}-{{ $status->id }}">{{ $status->title }}
                            @php
                                $docInfo = $dArrByRecord[$ph->id][$status->id] ?? [];
                                $isUploaded = isset($docInfo['uploaded']) && $docInfo['uploaded'];
                                $expiryTxt = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                            @endphp
                            @if($isUploaded && $expiryTxt)
                                <span class="text-muted"> - Exp: {{ $expiryTxt }}</span>
                            @endif
                        </label>  @tooltip('e1-document-' . $status->id)
                    </div>
                    <div class="col-4 d-flex flex-row justify-content-end align-items-center gap-2">
                        @php
                            $docInfo = $dArrByRecord[$ph->id][$status->id] ?? [];
                            $hasExpiry = isset($docInfo['has_expiry_date']) ? (int)$docInfo['has_expiry_date'] : 1;
                            $expiryVal = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                        @endphp
                        @if(request()->route()->getName() != 'cases.view')
                        <button type="button" class="btn red-btn me-2 open-upload-modal" data-record-id="{{ $ph->id }}" data-doc-id="{{ $status->id }}" data-dt-type="policy-holder" data-has-expiry="{{ $hasExpiry }}" data-expiry="{{ $expiryVal }}">Upload</button>
                        @endif
                        <a target="_blank" href="{{ asset('storage/kyc-docs/' . ( isset($dArrByRecord[$ph->id][$status->id]['document']) ? $dArrByRecord[$ph->id][$status->id]['document'] : '' ) ) }}" id="view[{{ $ph->id }}][{{ $status->id }}]" class="btn btn-primary view-file @if(isset($dArrByRecord[$ph->id][$status->id]['document']) && is_file(public_path('storage/kyc-docs/' . $dArrByRecord[$ph->id][$status->id]['document'])) ) @else d-none  @endif "> View </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mb-3 all-cbox d-none" id="container-for-ommit-documents-e1-llc">
        @foreach(($policyHolders ?? collect()) as $idx => $ph)
            <div class="mb-3">
                <div class="fw-bold mb-2">Policyholder {{ $loop->iteration }} {{ $ph->name ? ' - ' . $ph->name : '' }}</div>
                @foreach (\App\Models\Document::where('status', 'llc')->get() as $status)
                <div class="mb-2 row">
                    <div class="col-8">
                        <input type="checkbox" class="check-main" name="documents[{{ $ph->id }}][{{ $status->id }}]" data-type="policy-holder" data-record="{{ $ph->id }}" id="doc-{{ $ph->id }}-{{ $status->id }}" value="{{ $status->id }}" @if(isset($dArrByRecord[$ph->id][$status->id]) && isset($dArrByRecord[$ph->id][$status->id]['uploaded']) && $dArrByRecord[$ph->id][$status->id]['uploaded']) checked @endif> &nbsp;&nbsp;&nbsp;
                        <label for="doc-{{ $ph->id }}-{{ $status->id }}">{{ $status->title }}
                            @php
                                $docInfo = $dArrByRecord[$ph->id][$status->id] ?? [];
                                $isUploaded = isset($docInfo['uploaded']) && $docInfo['uploaded'];
                                $expiryTxt = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                            @endphp
                            @if($isUploaded && $expiryTxt)
                                <span class="text-muted"> - Exp: {{ $expiryTxt }}</span>
                            @endif
                        </label>  @tooltip('e1-document-' . $status->id)
                    </div>
                    <div class="col-4 d-flex flex-row justify-content-end align-items-center gap-2">
                        @php
                            $docInfo = $dArrByRecord[$ph->id][$status->id] ?? [];
                            $hasExpiry = isset($docInfo['has_expiry_date']) ? (int)$docInfo['has_expiry_date'] : 1;
                            $expiryVal = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                        @endphp
                        @if(request()->route()->getName() != 'cases.view')
                        <button type="button" class="btn red-btn me-2 open-upload-modal" data-record-id="{{ $ph->id }}" data-doc-id="{{ $status->id }}" data-dt-type="policy-holder" data-has-expiry="{{ $hasExpiry }}" data-expiry="{{ $expiryVal }}">Upload</button>
                        @endif
                        <a target="_blank" href="{{ asset('storage/kyc-docs/' . ( isset($dArrByRecord[$ph->id][$status->id]['document']) ? $dArrByRecord[$ph->id][$status->id]['document'] : '' ) ) }}" id="view[{{ $ph->id }}][{{ $status->id }}]" class="btn btn-primary view-file @if(isset($dArrByRecord[$ph->id][$status->id]['document']) && is_file(public_path('storage/kyc-docs/' . $dArrByRecord[$ph->id][$status->id]['document'])) ) @else d-none  @endif "> View </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mb-3 all-cbox d-none" id="container-for-ommit-documents-e1-corporation">
        @foreach(($policyHolders ?? collect()) as $idx => $ph)
            <div class="mb-3">
                <div class="fw-bold mb-2">Policyholder {{ $loop->iteration }} {{ $ph->name ? ' - ' . $ph->name : '' }}</div>
                @foreach (\App\Models\Document::where('status', 'corporation')->get() as $status)
                <div class="mb-2 row">
                    <div class="col-8">
                        <input type="checkbox" class="check-main" name="documents[{{ $ph->id }}][{{ $status->id }}]" data-type="policy-holder" data-record="{{ $ph->id }}" id="doc-{{ $ph->id }}-{{ $status->id }}" value="{{ $status->id }}" @if(isset($dArrByRecord[$ph->id][$status->id]) && isset($dArrByRecord[$ph->id][$status->id]['uploaded']) && $dArrByRecord[$ph->id][$status->id]['uploaded']) checked @endif> &nbsp;&nbsp;&nbsp;
                        <label for="doc-{{ $ph->id }}-{{ $status->id }}">{{ $status->title }}
                            @php
                                $docInfo = $dArrByRecord[$ph->id][$status->id] ?? [];
                                $isUploaded = isset($docInfo['uploaded']) && $docInfo['uploaded'];
                                $expiryTxt = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                            @endphp
                            @if($isUploaded && $expiryTxt)
                                <span class="text-muted"> - Exp: {{ $expiryTxt }}</span>
                            @endif
                        </label>  @tooltip('e1-document-' . $status->id)
                    </div>
                    <div class="col-4 d-flex flex-row justify-content-end align-items-center gap-2">
                        @php
                            $docInfo = $dArrByRecord[$ph->id][$status->id] ?? [];
                            $hasExpiry = isset($docInfo['has_expiry_date']) ? (int)$docInfo['has_expiry_date'] : 1;
                            $expiryVal = isset($docInfo['expiry_date']) && $docInfo['expiry_date'] ? date('Y-m-d', strtotime($docInfo['expiry_date'])) : '';
                        @endphp
                        @if(request()->route()->getName() != 'cases.view')
                        <button type="button" class="btn red-btn me-2 open-upload-modal" data-record-id="{{ $ph->id }}" data-doc-id="{{ $status->id }}" data-dt-type="policy-holder" data-has-expiry="{{ $hasExpiry }}" data-expiry="{{ $expiryVal }}">Upload</button>
                        @endif
                        <a target="_blank" href="{{ asset('storage/kyc-docs/' . ( isset($dArrByRecord[$ph->id][$status->id]['document']) ? $dArrByRecord[$ph->id][$status->id]['document'] : '' ) ) }}" id="view[{{ $ph->id }}][{{ $status->id }}]" class="btn btn-primary view-file @if(isset($dArrByRecord[$ph->id][$status->id]['document']) && is_file(public_path('storage/kyc-docs/' . $dArrByRecord[$ph->id][$status->id]['document'])) ) @else d-none  @endif "> View </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mb-3 all-cbox d-none" id="container-for-ommit-documents-e1-other">
        @foreach(($policyHolders ?? collect()) as $idx => $ph)
            <div class="mb-3">
                <div class="fw-bold mb-2">Policyholder {{ $loop->iteration }} {{ $ph->name ? ' - ' . $ph->name : '' }}</div>
                @foreach (\App\Models\Document::where('status', 'other')->get() as $status)
                <div class="mb-2 row">
                    <div class="col-8">
                        <input type="checkbox" class="check-main" name="documents[{{ $ph->id }}][{{ $status->id }}]" data-type="policy-holder" data-record="{{ $ph->id }}" id="doc-{{ $ph->id }}-{{ $status->id }}" value="{{ $status->id }}" @if(isset($dArrByRecord[$ph->id][$status->id]) && isset($dArrByRecord[$ph->id][$status->id]['uploaded']) && $dArrByRecord[$ph->id][$status->id]['uploaded']) checked @endif> &nbsp;&nbsp;&nbsp;
                        <label for="doc-{{ $ph->id }}-{{ $status->id }}">{{ $status->title }}</label>  @tooltip('e1-document-' . $status->id)
                    </div>
                    <div class="col-4 d-flex flex-row justify-content-end">
                        @if(request()->route()->getName() != 'cases.view')
                        <label for="file-{{ $ph->id }}-{{ $status->id }}" class="btn red-btn me-2">Upload</label>
                        <input id="file-{{ $ph->id }}-{{ $status->id }}" type="file" name="file[{{ $ph->id }}][{{ $status->id }}]" class="d-none doc-upl" data-record="{{ $ph->id }}" data-type="policy-holder">
                        @endif
                        <a target="_blank" href="{{ asset('storage/kyc-docs/' . ( isset($dArrByRecord[$ph->id][$status->id]['document']) ? $dArrByRecord[$ph->id][$status->id]['document'] : '' ) ) }}" id="view[{{ $ph->id }}][{{ $status->id }}]" class="btn btn-primary view-file @if(isset($dArrByRecord[$ph->id][$status->id]['document']) && is_file(public_path('storage/kyc-docs/' . $dArrByRecord[$ph->id][$status->id]['document'])) ) @else d-none  @endif "> View </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mb-3 float-end">
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        @if(request()->route()->getName() != 'cases.view')
        <button type="submit" data-type="next" data-next="section-e-2" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>
</form>