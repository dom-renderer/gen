<form id="form-section-f-1">
    <div class="mb-3 row">
        <label class="col-sm-12 col-form-label mb-3">Purpose of the Policy and Structure</label>
        <div class="col-sm-12">
            <select class="form-select" name="purpose" id="s-f-1-purpose" multiple >
                <option @if(in_array('asset-protection-and-risk-management', $f1Data->purpose_of_policy_and_structure ?? [])) selected @endif  value="asset-protection-and-risk-management" >Asset protection and Risk Management</option>
                <option @if(in_array('business-countinuity-buy-sell-funcing', $f1Data->purpose_of_policy_and_structure ?? [])) selected @endif  value="business-countinuity-buy-sell-funcing" >Business Continuity / Buy-sell Funding</option>
                <option @if(in_array('estate-equalisation', $f1Data->purpose_of_policy_and_structure ?? [])) selected @endif  value="estate-equalisation" >Estate Equalisation</option>
                <option @if(in_array('generational-wealth-transfer', $f1Data->purpose_of_policy_and_structure ?? [])) selected @endif  value="generational-wealth-transfer" >Generational Wealth Transfer</option>
                <option @if(in_array('liquidity-for-estate-taxes-or-liabilities', $f1Data->purpose_of_policy_and_structure ?? [])) selected @endif  value="liquidity-for-estate-taxes-or-liabilities" >Liquidity for Estate Taxes or Liabilities</option>
                <option @if(in_array('long-term-investment-planning', $f1Data->purpose_of_policy_and_structure ?? [])) selected @endif  value="long-term-investment-planning" >Long-term Investment Planning</option>
                <option @if(in_array('migration', $f1Data->purpose_of_policy_and_structure ?? [])) selected @endif  value="migration" >Migration</option>
            </select>
        </div>
    </div>

    <div class="mb-3 row">
        <label class="col-sm-12 col-form-label mb-3">Additional Details</label>
        <div class="col-sm-12">
            <textarea class="form-control" rows="3" name="additional_details" > {{ $f1Data->additional_details ?? '' }} </textarea>
        </div>
    </div>

    <div class="mb-3 row">
        <label class="col-sm-12 mb-3 col-form-label">Estimated Net Worth of the Source of Premium Individual</label>
        <div class="col-sm-12">
            <input type="text" class="form-control" name="estimated_networth" value="{{ $f1Data->estimated_networth ?? '' }}" >
        </div>
    </div>

    <div class="mb-3 row">
        <label class="col-sm-12 mb-3 col-form-label">Source of Wealth for the Policy</label>
        <div class="col-sm-12">
            <textarea class="form-control" rows="2" name="source_of_wealth_for_policy" >{{ $f1Data->source_of_wealth_for_policy ?? '' }}</textarea>
        </div>
    </div>

    <div class="mb-3 row">
        <label class="col-sm-12 mb-3 col-form-label">Distribution Strategy (During Policy Lifetime)</label>
        <div class="col-sm-12">
            <textarea class="form-control mb-3" rows="2" name="distribution_strategy_during_policy_lifetime" >{{ $f1Data->distribution_strategy_during_policy_lifetime ?? '' }}</textarea>
            <small class="text-muted">
                E.g. During the insured's lifetime, policy loans or partial surrenders may be used to generate liquidity for the trust to meet the family's annual expenses, fund charitable donations, or support business investments.
            </small>
        </div>
    </div>

    <div class="mb-3 row">
        <label class="col-sm-12 mb-3 col-form-label">Distribution Strategy (Post-Death Payout)</label>
        <div class="col-sm-12">
            <textarea class="form-control mb-3" rows="2" name="distribution_strategy_post_death_payout" >{{ $f1Data->distribution_strategy_post_death_payout ?? '' }}</textarea>
            <small class="text-muted">
                E.g. Upon the death of the insured, the death benefit is intended to be paid to a discretionary trust, which will allocate funds to beneficiaries over time to support education, generational wealth planning, and tax-efficient distributions aligned with the family's long-term goals.
            </small>
        </div>
    </div>

    <div class="mb-3 row">
        <label class="col-sm-12 mb-3 col-form-label">Known Triggers for Policy Exit or Surrender</label>
        <div class="col-sm-12">
            <textarea class="form-control" rows="2" name="known_triggers_for_policy_exit_or_surrender">{{ $f1Data->known_triggers_for_policy_exit_or_surrender ?? '' }}</textarea>
        </div>
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-f-2" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>        
</form>