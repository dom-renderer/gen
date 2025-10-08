<form id="form-section-f-7">
    
     <div class="mb-3 row">
        <div class="col-xl-6">
            <div class="row">
                <label class="col-sm-12 col-form-label mb-3">Date of Change  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control section-f-7-date-1" name="portfolio_change_date" placeholder="YY-MM-DD" value="{{ isset($f7Data['date_of_change_portfolio']) ? date('Y-m-d', strtotime($f7Data['date_of_change_portfolio'])) : '' }}"  readonly>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
             <div class="mb-3 row">
                <label class="col-sm-12 col-form-label">Portfolio Change  </label>
                <div class="col-sm-12 mt-2">
                    <textarea class="form-control section-f-7-portfolio" name="portfolio_change" rows="2" >{{ isset($f7Data['portfolio_change']) ? $f7Data['portfolio_change'] : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

    

    <div class="mb-3 row">
        <div class="col-xl-6">
            <div class="row">
                 <label class="col-sm-12 col-form-label mb-3">Date of Change  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control section-f-7-date-2" name="idf_change_date" value="{{ isset($f7Data['date_of_change_idf']) ? date('Y-m-d', strtotime($f7Data['date_of_change_idf'])) : '' }}" placeholder="YY-MM-DD"  readonly>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
             <div class="mb-3 row">
                <label class="col-sm-12 col-form-label">IDF or Investment Manager Change  </label>
                <div class="col-sm-12 mt-2">
                    <textarea class="form-control section-f-7-idf" name="idf_change" rows="2" >{{ isset($f7Data['idf_change']) ? $f7Data['idf_change'] : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

   
   

    <div class="mb-3 row">
        <div class="col-xl-6">
            <div class="row">
                <label class="col-sm-12 col-form-label mb-3">Date of Change  </label>
                <div class="col-sm-12">
                    <input type="text" class="form-control section-f-7-date-3" name="asset_transfer_date" value="{{ isset($f7Data['date_of_change_transfer']) ? date('Y-m-d', strtotime($f7Data['date_of_change_transfer'])) : '' }}" placeholder="YY-MM-DD"  readonly>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="mb-3 row">
                <label class="col-sm-12 col-form-label">Asset Transfer or Liquidity Events  </label>
                <div class="col-sm-12">
                    <textarea class="form-control section-f-7-transfer" name="asset_transfer_note" rows="4" >{{ isset($f7Data['transfer_change']) ? $f7Data['transfer_change'] : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

    

    <div class="mb-3 row">
        <label class="col-sm-12 col-form-label">Relevant Policyholder/ Board/ Trustee Decisions  </label>
        <div class="col-sm-12">
            <textarea class="form-control section-f-7-decisions" name="trustee_decisions" rows="4" >{{ isset($f7Data['decision']) ? $f7Data['decision'] : '' }}</textarea>
        </div>
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-h-1" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>

</form>