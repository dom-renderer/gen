<form id="form-section-f-3" class="case-premeim">
    <div class="row mb-3">
        <div class="col-xl-6">
            <div class="row">
                <label class="col-sm-12 col-form-label">Fee Provided By  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control" name="fee_provided_by" value="{{ $f3Data->fee_provided_by ?? '' }}" >
                </div>
            </div>
        </div>
       
        <div class="col-xl-6">
            <div class="row">
                <label class="col-sm-12 col-form-label">Date Fee Provided  </label>
                <div class="col-sm-12">
                    <input type="text" id="f3d1" name="fee_provided_by_date" class="form-control" value="{{ isset($f3Data->date_fee_provided) ? date('Y-m-d', strtotime($f3Data->date_fee_provided)) : '' }}" readonly >
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-xl-6">
            <div class="row">
                <label class="col-sm-12 col-form-label">Fee Approved By Policyholder  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control" name="fee_approved_by" value="{{ $f3Data->controlling_person_fee_approved_by ?? '' }}" >
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="row">
                <label class="col-sm-12 col-form-label">Date Fee Approved  </label>
                <div class="col-sm-12">
                    <input type="text" id="f3d2" name="fee_approved_by_date" class="form-control" value="{{ isset($f3Data->date_fee_approved) ? date('Y-m-d', strtotime($f3Data->date_fee_approved)) : '' }}" readonly >
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-xl-6">
            <div class="rwo">
                <label class="col-sm-12 col-form-label">GII Fee Approved By  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control" name="gii_fee_approved_by" value="{{ $f3Data->gii_fee_approved_by ?? '' }}" >
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="row">
                <label class="col-sm-12 col-form-label">Date Fee Approved  </label>
                <div class="col-sm-12">
                    <input type="text" id="f3d3" name="gii_fee_approved_by_date" class="form-control" value="{{ isset($f3Data->gii_date_fee_approved) ? date('Y-m-d', strtotime($f3Data->gii_date_fee_approved)) : '' }}" readonly >
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-12 col-form-label">Fee Approval Notes  </label>
        <div class="col-sm-12">
            <textarea class="form-control" rows="2" name="approval_notes" >{{ $f3Data->fee_approval_notes ?? '' }}</textarea>
            <small class="text-muted">
                Notes including communication channel (email, Teams/Zoom/Remote call, telephone call), date, time and any other details.
            </small>
        </div>
    </div>

	<div class="row mb-3">
		<div class="col-12">
			<div class="accordion" id="adminFeeAccordion">
				<div class="accordion-item">
					<h2 class="accordion-header" id="headingAdminFee">
						<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdminFee" aria-expanded="false" aria-controls="collapseAdminFee">
							Administration Fee Details
						</button>
					</h2>
					<div id="collapseAdminFee" class="accordion-collapse collapse" aria-labelledby="headingAdminFee" data-bs-parent="#adminFeeAccordion">
						<div class="accordion-body">
							<div class="row mb-3">
								<div class="col-xl-6">
									<label class="col-form-label">Administration Type</label>
                                    <select class="form-select" id="admin-fee-type" name="admin_fee[type]">
                                        <option @if($f3Data?->admin_fee_type == 'single') selected @endif value="single">Single Fee</option>
                                        <option @if($f3Data?->admin_fee_type == 'flat') selected @endif value="flat">Flat Fee</option>
                                        <option @if($f3Data?->admin_fee_type == 'step-amount') selected @endif value="step-amount">Step Fee by Amount</option>
                                        <option @if($f3Data?->admin_fee_type == 'step-year') selected @endif value="step-year">Step Fee by Year</option>
                                        <option @if($f3Data?->admin_fee_type == 'step-flat') selected @endif value="step-flat">Step Flat Fee</option>
                                        <option @if($f3Data?->admin_fee_type == 'layered') selected @endif value="layered">Layered Fee</option>
									</select>
								</div>
							</div>
                            @php $showAdminSteps = in_array($f3Data?->admin_fee_type, ['step-amount','step-year','step-flat','layered']); @endphp
                            <div id="admin-fee-steps-container" class="mb-3 @if(!$showAdminSteps) d-none @endif">
								<div class="table-responsive">
									<table class="table table-bordered align-middle">
										<thead class="table-light text-center">
											<tr>
												<th>Tier/Step</th>
												<th>From Amount/Year</th>
												<th>To Amount/Year</th>
												<th>Rate/Amount</th>
												<th style="width: 100px;">Action</th>
											</tr>
										</thead>
                                        <tbody id="admin-fee-steps-body">
                                            @php $steps = $f3Data?->adminSteps ?? collect(); @endphp
                                            @if($steps->count() > 0)
                                                @foreach($steps as $idx => $step)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][{{ $idx }}][from]" value="{{ $step->from_value }}"></td>
                                                        <td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][{{ $idx }}][to]" value="{{ $step->to_value }}"></td>
                                                        <td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][{{ $idx }}][rate]" value="{{ $step->rate_or_amount }}"></td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-success btn-sm admin-add-row">+</button>
                                                            <button type="button" class="btn btn-danger btn-sm admin-remove-row">-</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][0][from]"></td>
                                                    <td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][0][to]"></td>
                                                    <td><input type="number" step="0.01" min="0" class="form-control" name="admin_fee[steps][0][rate]"></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-success btn-sm admin-add-row">+</button>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
									</table>
								</div>
							</div>

							<div class="row mb-3">
								<label class="col-sm-12 col-form-label">Administration Value to be Applied to</label>
								<div class="col-sm-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="admin_fee[applied_to]" id="admin-applied-nav" value="nav" @if($f3Data?->admin_fee_applied_to=='nav') checked @endif>
										<label class="form-check-label" for="admin-applied-nav">Net Asset Value</label>
									</div>
									<div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="admin_fee[applied_to]" id="admin-applied-gpv" value="gpv" @if($f3Data?->admin_fee_applied_to=='gpv') checked @endif>
										<label class="form-check-label" for="admin-applied-gpv">Gross Policy Value</label>
									</div>
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-xl-6">
									<label class="col-form-label">Administration Fee Limits</label>
                                    <select class="form-select" name="admin_fee[limit]" id="admin-fee-limit">
                                        <option @if($f3Data?->admin_fee_limit=='none') selected @endif value="none">None</option>
                                        <option @if($f3Data?->admin_fee_limit=='>5000') selected @endif value=">5000">&gt; $5,000 p.a.</option>
                                        <option @if($f3Data?->admin_fee_limit=='>7500') selected @endif value=">7500">&gt; $7,500 p.a.</option>
                                        <option @if($f3Data?->admin_fee_limit=='>10000') selected @endif value=">10000">&gt; $10,000 p.a.</option>
                                        <option @if($f3Data?->admin_fee_limit=='>12500') selected @endif value=">12500">&gt; $12,500 p.a.</option>
                                        <option @if($f3Data?->admin_fee_limit=='>15000') selected @endif value=">15000">&gt; $15,000 p.a.</option>
                                        <option @if($f3Data?->admin_fee_limit=='>20000') selected @endif value=">20000">&gt; $20,000 p.a.</option>
                                        <option @if($f3Data?->admin_fee_limit=='>25000') selected @endif value=">25000">&gt; $25,000 p.a.</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="overflow-auto">
        <table class="table table-bordered align-middle mt-3">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th>Fee Type</th>
                    <th>Frequency</th>
                    <th>Amount</th>
                    <th>Rate</th>
                    @forelse (\App\Models\PolicyIntroducer::whereHas('intro', fn ($builder) => $builder->withTrashed())->where('policy_id', $policy_id)->get() as $row)
                        <th>
                            {{ $row->intro->type == 'individual' ? ($row->intro->name . ' ' . $row->intro->middle_name . ' ' . $row->intro->last_name) : ($row->intro->name) }} <br/>
                            (Introducer {{ $loop->iteration }})
                        </th>
                    @empty
                    @endforelse
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @php
                        $a = $f3Data?->items()?->where('type', 'setup_fee')->count() > 0 ? $f3Data?->items()?->where('type', 'setup_fee')->first()->toArray() : [];
                    @endphp
                    <td>
                        <div>Set Up Fee</div>
                        <div class="mt-1">
                            @php
                                $setupSelected = old('fee_option.set_up_fee', $a['fee_option'] ?? '');
                            @endphp
                            <select class="form-select" name="fee_option[set_up_fee]">
                                <option value="" @if($setupSelected==='') selected @endif>None</option>
                                <option value="$5,000 p.a." @if($setupSelected==='$5,000 p.a.') selected @endif>$5,000 p.a.</option>
                                <option value="$10,000 p.a." @if($setupSelected==='$10,000 p.a.') selected @endif>$10,000 p.a.</option>
                                <option value="$12,500 p.a." @if($setupSelected==='$12,500 p.a.') selected @endif>$12,500 p.a.</option>
                                <option value="$25,000 p.a." @if($setupSelected==='$25,000 p.a.') selected @endif>$25,000 p.a.</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <input type="hidden" name="a[type]" value="setup_fee">
                        <select class="form-select" name="a[frequency]">
                            <option @if(isset($a['frequency']) && $a['frequency'] == 'one-time-payment') selected @endif value="one-time-payment">One-time payment</option>
                            <option @if(isset($a['frequency']) && $a['frequency'] == 'flat-fee') selected @endif value="flat-fee">Flat Fee</option>
                            <option @if(isset($a['frequency']) && $a['frequency'] == 'single-fee-on-all-premium-paid') selected @endif value="single-fee-on-all-premium-paid">Single Fee (on all premiums paid)</option>
                        </select>
                    </td>
                    <td><input type="number" min="0" name="a[amount]" step="0.01" class="form-control" value="{{ isset($a['amount']) ? $a['amount'] : null }}"></td>
                    <td><input type="number" min="0" name="a[rate]" step="0.01" class="form-control" value="{{ isset($a['rate']) ? $a['rate'] : null }}"></td>
                    @forelse (\App\Models\PolicyIntroducer::select('id')->where('policy_id', $policy_id)->get() as $row)
                        <td><input type="number" min="0" name="a[commission_split][{{ $row->id }}]" step="0.01" max="100" class="form-control" value="{{ \App\Models\PolicyFeeSummaryCommissionSplit::select('commission')->where('policy_id', $policy_id)->where('policy_introducer_id', $row->id)->where('policy_fee_summary_internal_fee_id', $a['id'] ?? '')->first()->commission ?? 0 }}"></td>
                    @empty
                    @endforelse
                    <td><textarea class="form-control" name="a[note]" rows="1">{{ isset($a['notes']) ? $a['notes'] : null }}</textarea></td>
                </tr>

                <tr>
                    @php
                        $b = $f3Data?->items()?->where('type', 'me_fee')->count() > 0 ? $f3Data?->items()?->where('type', 'me_fee')->first()->toArray() : [];
                    @endphp
                    <td>Administration or M&E Fee</td>
                    <td>
                        <input type="hidden" name="b[type]" value="me_fee">
                        <select class="form-select" name="b[frequency]">
                            <option @if(isset($b['frequency']) && $b['frequency'] == 'monthly') selected @endif value="monthly">Monthly</option>
                            <option @if(isset($b['frequency']) && $b['frequency'] == 'bi-monthly') selected @endif value="bi-monthly">Bi-Monthly</option>
                            <option @if(isset($b['frequency']) && $b['frequency'] == 'quarterly') selected @endif value="quarterly">Quarterly</option>
                            <option @if(isset($b['frequency']) && $b['frequency'] == 'semi-annually') selected @endif value="semi-annually">Semi-Annually</option>
                            <option @if(isset($b['frequency']) && $b['frequency'] == 'anually') selected @endif value="anually">Annually</option>
                        </select>
                    </td>
                    <td><input type="number" min="0" name="b[amount]" step="0.01" class="form-control" value="{{ isset($b['amount']) ? $b['amount'] : null }}"></td>
                    <td><input type="number" min="0" name="b[rate]" step="0.01" class="form-control" value="{{ isset($b['rate']) ? $b['rate'] : null }}"></td>
                    @forelse (\App\Models\PolicyIntroducer::select('id')->where('policy_id', $policy_id)->get() as $row)
                        <td><input type="number" min="0" name="b[commission_split][{{ $row->id }}]" step="0.01" max="100" class="form-control" value="{{ \App\Models\PolicyFeeSummaryCommissionSplit::select('commission')->where('policy_id', $policy_id)->where('policy_introducer_id', $row->id)->where('policy_fee_summary_internal_fee_id', $b['id'] ?? '')->first()->commission ?? 0 }}"></td>
                    @empty
                    @endforelse
                    <td><textarea class="form-control" name="b[note]" rows="1">{{ isset($b['notes']) ? $b['notes'] : null }}</textarea></td>
                </tr>

                <tr>
                    @php
                        $c = $f3Data?->items()?->where('type', 'coi')->count() > 0 ? $f3Data?->items()?->where('type', 'coi')->first()->toArray() : [];
                    @endphp
                    <td>
                        <div>COI</div>
                        <div class="mt-1">
                            @php
                                $coiSelected = old('fee_option.coi', $c['fee_option'] ?? '');
                                $coiOther = old('fee_option_other.coi', ($c['fee_option'] ?? '') && !in_array(($c['fee_option'] ?? ''), ['Minimum','YRT per 1,000','Premium from Policy Assignment','Other (specify)']) ? ($c['fee_option'] ?? '') : '');
                                $isOther = !in_array(($c['fee_option'] ?? ''), ['Minimum','YRT per 1,000','Premium from Policy Assignment','Other (specify)']);
                            @endphp
                            <select class="form-select coi-fee-option-select" name="fee_option[coi]">
                                <option value="Minimum" @if($coiSelected==='Minimum') selected @endif>Minimum</option>
                                <option value="YRT per 1,000" @if($coiSelected==='YRT per 1,000') selected @endif>YRT per 1,000</option>
                                <option value="Premium from Policy Assignment" @if($coiSelected==='Premium from Policy Assignment') selected @endif>Premium from Policy Assignment</option>
                                <option value="Other (specify)" @if($isOther) selected @endif>Other (specify)</option>
                            </select>
                            <input type="text" class="form-control mt-2 coi-fee-option-other @if(!$isOther) d-none @endif" name="fee_option_other[coi]" value="{{ $coiOther }}" placeholder="Specify custom option">
                        </div>
                    </td>
                    <td>
                        <input type="hidden" name="c[type]" value="coi">
                        <select class="form-select" name="c[frequency]">
                            <option @if(isset($c['frequency']) && $c['frequency'] == 'qurterly') selected @endif value="qurterly">Quarterly</option>
                            <option @if(isset($c['frequency']) && $c['frequency'] == 'semi-annually') selected @endif value="semi-annually">Semi-Annually</option>
                            <option @if(isset($c['frequency']) && $c['frequency'] == 'annually') selected @endif value="annually">Anually</option>
                        </select>
                    </td>
                    <td><input type="number" min="0" name="c[amount]" step="0.01" class="form-control" value="{{ isset($c['amount']) ? $c['amount'] : null }}"></td>
                    <td><input type="number" min="0" name="c[rate]" step="0.01" class="form-control" value="{{ isset($c['amount']) ? $c['rate'] : null }}"></td>
                    @forelse (\App\Models\PolicyIntroducer::select('id')->where('policy_id', $policy_id)->get() as $row)
                        <td><input type="number" min="0" name="c[commission_split][{{ $row->id }}]" step="0.01" max="100" class="form-control" value="{{ \App\Models\PolicyFeeSummaryCommissionSplit::select('commission')->where('policy_id', $policy_id)->where('policy_introducer_id', $row->id)->where('policy_fee_summary_internal_fee_id', $c['id'] ?? '')->first()->commission ?? 0 }}"></td>
                    @empty
                    @endforelse
                    <td><textarea class="form-control" name="c[note]" rows="1">{{ isset($c['notes']) ? $c['notes'] : null }}</textarea></td>
                </tr>

                <tr>
                    @php
                        $d = $f3Data?->items()?->where('type', 'dac_fee')->count() > 0 ? $f3Data?->items()?->where('type', 'dac_fee')->first()->toArray() : [];
                    @endphp
                    <td>DAC Fee</td>
                    <td>
                        <input type="hidden" name="d[type]" value="dac_fee">
                        <select class="form-select" name="d[frequency]">
                            <option @if(isset($d['frequency']) && $d['frequency'] == 'one-time-payment') selected @endif value="one-time-payment">One-time payment</option>
                            <option @if(isset($d['frequency']) && $d['frequency'] == 'flat-fee') selected @endif value="flat-fee">Flat Fee</option>
                            <option @if(isset($d['frequency']) && $d['frequency'] == 'single-fee-on-all-premium-paid') selected @endif value="single-fee-on-all-premium-paid">Single Fee (on all premiums paid)</option>
                        </select>
                    </td>
                    <td><input type="number" min="0" name="d[amount]" step="0.01" class="form-control" value="{{ isset($d['amount']) ? $d['amount'] : null }}"></td>
                    <td><input type="number" min="0" name="d[rate]" step="0.01" class="form-control" value="{{ isset($d['rate']) ? $d['rate'] : null }}"></td>
                    @forelse (\App\Models\PolicyIntroducer::select('id')->where('policy_id', $policy_id)->get() as $row)
                        <td><input type="number" min="0" name="d[commission_split][{{ $row->id }}]" step="0.01" max="100" class="form-control" value="{{ \App\Models\PolicyFeeSummaryCommissionSplit::select('commission')->where('policy_id', $policy_id)->where('policy_introducer_id', $row->id)->where('policy_fee_summary_internal_fee_id', $d['id'] ?? '')->first()->commission ?? 0 }}"></td>
                    @empty
                    @endforelse
                    <td><textarea class="form-control" name="d[note]" rows="1">{{ isset($d['notes']) ? $d['notes'] : null }}</textarea></td>
                </tr>

                <tr>
                    @php
                        $e = $f3Data?->items()?->where('type', 'crs_fee')->count() > 0 ? $f3Data?->items()?->where('type', 'crs_fee')->first()->toArray() : [];
                    @endphp
                    <td>FATCA/CRS Fee</td>
                    <td>
                        <input type="hidden" name="e[type]" value="crs_fee">
                        <select class="form-select" name="e[frequency]">
                            <option @if(isset($e['frequency']) && $e['frequency'] == 'monthly') selected @endif value="monthly">Monthly</option>
                            <option @if(isset($e['frequency']) && $e['frequency'] == 'bi-monthly') selected @endif value="bi-monthly">Bi-Monthly</option>
                            <option @if(isset($e['frequency']) && $e['frequency'] == 'quarterly') selected @endif value="quarterly">Quarterly</option>
                            <option @if(isset($e['frequency']) && $e['frequency'] == 'semi-annually') selected @endif value="semi-annually">Semi-Annually</option>
                            <option @if(isset($e['frequency']) && $e['frequency'] == 'anually') selected @endif value="anually">Annually</option>
                        </select>
                    </td>
                    <td><input type="number" min="0" name="e[amount]" step="0.01" class="form-control" value="{{ isset($e['amount']) ? $e['amount'] : null }}"></td>
                    <td><input type="number" min="0" name="e[rate]" step="0.01" class="form-control" value="{{ isset($e['rate']) ? $e['rate'] : null }}"></td>
                    @forelse (\App\Models\PolicyIntroducer::select('id')->where('policy_id', $policy_id)->get() as $row)
                        <td><input type="number" min="0" name="e[commission_split][{{ $row->id }}]" step="0.01" max="100" class="form-control" value="{{ \App\Models\PolicyFeeSummaryCommissionSplit::select('commission')->where('policy_id', $policy_id)->where('policy_introducer_id', $row->id)->where('policy_fee_summary_internal_fee_id', $e['id'] ?? '')->first()->commission ?? 0 }}"></td>
                    @empty
                    @endforelse
                    <td><textarea class="form-control" name="e[note]" rows="1">{{ isset($e['notes']) ? $e['notes'] : null }}</textarea></td>
                </tr>

                <tr>
                    @php
                        $f = $f3Data?->items()?->where('type', 'surrender_fee')->count() > 0 ? $f3Data?->items()?->where('type', 'surrender_fee')->first()->toArray() : [];
                    @endphp
                    <td>Surrender Fee</td>
                    <td>
                        <input type="hidden" name="f[type]" value="surrender_fee">
                        <select class="form-select" name="f[frequency]">
                            <option @if(isset($f['frequency']) && $f['frequency'] == 'one-time-payment') selected @endif value="monthly">One-time payment</option>
                        </select>
                    </td>
                    <td><input type="number" min="0" name="f[amount]" step="0.01" class="form-control" value="{{ isset($f['amount']) ? $f['amount'] : null }}"></td>
                    <td><input type="number" min="0" name="f[rate]" step="0.01" class="form-control" value="{{ isset($f['rate']) ? $f['rate'] : null }}"></td>
                    @forelse (\App\Models\PolicyIntroducer::select('id')->where('policy_id', $policy_id)->get() as $row)
                        <td><input type="number" min="0" name="f[commission_split][{{ $row->id }}]" step="0.01" max="100" class="form-control" value="{{ \App\Models\PolicyFeeSummaryCommissionSplit::select('commission')->where('policy_id', $policy_id)->where('policy_introducer_id', $row->id)->where('policy_fee_summary_internal_fee_id', $f['id'] ?? '')->first()->commission ?? 0 }}"></td>
                    @empty
                    @endforelse
                    <td><textarea class="form-control" name="f[note]" rows="1">{{ isset($f['notes']) ? $f['notes'] : null }}</textarea></td>
                </tr>

                <tr>
                    @php
                        $g = $f3Data?->items()?->where('type', 'loan_interest_rate')->count() > 0 ? $f3Data?->items()?->where('type', 'loan_interest_rate')->first()->toArray() : [];
                    @endphp
                    <td>Loan Interest Rate</td>
                    <td>
                        <input type="hidden" name="g[type]" value="loan_interest_rate">
                        <select class="form-select" name="g[frequency]">
                            <option @if(isset($g['frequency']) && $g['frequency'] == 'monthly') selected @endif value="monthly">Monthly</option>
                            <option @if(isset($g['frequency']) && $g['frequency'] == 'bi-monthly') selected @endif value="bi-monthly">Bi-Monthly</option>
                            <option @if(isset($g['frequency']) && $g['frequency'] == 'quarterly') selected @endif value="quarterly">Quarterly</option>
                            <option @if(isset($g['frequency']) && $g['frequency'] == 'semi-annually') selected @endif value="semi-annually">Semi-Annually</option>
                            <option @if(isset($g['frequency']) && $g['frequency'] == 'anually') selected @endif value="anually">Annually</option>
                        </select>
                    </td>
                    <td><input type="number" min="0" name="g[amount]" step="0.01" class="form-control" value="{{ isset($g['amount']) ? $g['amount'] : null }}"></td>
                    <td><input type="number" min="0" name="g[rate]" step="0.01" class="form-control" value="{{ isset($g['rate']) ? $g['rate'] : null }}"></td>

                    @forelse (\App\Models\PolicyIntroducer::select('id')->where('policy_id', $policy_id)->get() as $row)
                        <td><input type="number" min="0" name="g[commission_split][{{ $row->id }}]" step="0.01" max="100" class="form-control" value="{{ \App\Models\PolicyFeeSummaryCommissionSplit::select('commission')->where('policy_id', $policy_id)->where('policy_introducer_id', $row->id)->where('policy_fee_summary_internal_fee_id', $g['id'] ?? '')->first()->commission ?? 0 }}"></td>
                    @empty
                    @endforelse
                    
                    <td><textarea class="form-control" name="g[note]" rows="1">{{ isset($g['notes']) ? $g['notes'] : null }}</textarea></td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-f-4" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>

</form>