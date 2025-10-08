<form id="form-section-f-2" class="case-premeim">
        <div class="mb-3 row">
            <label class="col-sm-6 col-form-label">Policy Type</label>
            <div class="col-sm-6">
                <select class="form-select form-control selct-common" name="type" > 
                    <option @if(isset($f2Data->policy_type) && $f2Data->policy_type == 'deferred_annuity') selected @endif value="deferred_annuity">Deferred Annuity</option>
                    <option @if(isset($f2Data->policy_type) && $f2Data->policy_type == 'whole_life') selected @endif value="whole_life">Whole Life</option>
                    <option @if(isset($f2Data->policy_type) && $f2Data->policy_type == 'term_life') selected @endif value="term_life">Term Life</option>
                    <option @if(isset($f2Data->policy_type) && $f2Data->policy_type == 'universal_life') selected @endif value="universal_life">Universal Life</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th>Description</th>
                        <th>Amount (USD or relevant currency)</th>
                        <th>Notes<br><small>(based on initial illustration basis, confirmed transfer)</small></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Proposed Premium  </td>
                        <td><input type="number" min="0" step="0.01" class="form-control" name="proposed_premium"  value="{{ $f2Data->proposed_premium_amount ?? 0 }}"></td>
                        <td><textarea class="form-control" rows="2" name="proposed_premium_note" >{{ $f2Data->proposed_premium_note ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        <td>Final Premium  </td>
                        <td><input type="number" min="0" step="0.01" class="form-control" name="final_premium"  value="{{ $f2Data->final_premium_amount ?? 0 }}"></td>
                        <td><textarea class="form-control" rows="2" name="final_premium_note" >{{ $f2Data->final_premium_note ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        <td>Premium Frequency  </td>
                        <td colspan="2">
                            <div class="row d-radio">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Recurring</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="monthly" value="monthly" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == 'monthly') checked @endif>
                                        <label class="form-check-label" for="monthly">Monthly</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="quarterly" value="quarterly" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == 'quarterly') checked @endif>
                                        <label class="form-check-label" for="quarterly">Quarterly</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="semi-annual" value="semi-annual" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == 'semi-annual') checked @endif>
                                        <label class="form-check-label" for="semi-annual">Semi-Annual</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="annual" value="annual" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == 'annual') checked @endif>
                                        <label class="form-check-label" for="annual">Annual</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Limited-Pay</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="2-pay" value="2-pay" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == '2-pay') checked @endif>
                                        <label class="form-check-label" for="2-pay">2-Pay (over 2 years)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="3-pay" value="3-pay" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == '3-pay') checked @endif>
                                        <label class="form-check-label" for="3-pay">3-Pay (over 3 years)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="4-pay" value="4-pay" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == '4-pay') checked @endif>
                                        <label class="form-check-label" for="4-pay">4-Pay (over 4 years)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="5-pay" value="5-pay" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == '5-pay') checked @endif>
                                        <label class="form-check-label" for="5-pay">5-Pay (over 5 years)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="6-pay" value="6-pay" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == '6-pay') checked @endif>
                                        <label class="form-check-label" for="6-pay">6-Pay (over 6 years)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="premium_frequency" id="7-pay" value="7-pay" @if(isset($f2Data->premium_frequency) && $f2Data->premium_frequency == '7-pay') checked @endif>
                                        <label class="form-check-label" for="7-pay">7-Pay (over 7 years)</label>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

<div class="mb-3 float-end">
    @if(request()->route()->getName() != 'cases.view')
    {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
    <button type="submit" data-type="next" data-next="section-f-3" class="btn btn-primary save-next">Save & Next</button>
    @endif
</div>
</form>