<form id="form-section-f-5" class="case-premeim">
    
        <div class="mt-4 overflow-auto">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>Asset Class</th>
                        <th>Included</th>
                        <th>Est. % of Portfolio</th>
                        <th>Valuation Support</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @php
                            $a = $f5Data?->where('asset_class', 'stocks')->first()?->toArray() ?? [];
                        @endphp
                        <td>Listed Equities (Stocks)</td>
                        <td class="text-center">
                            <input type="hidden" name="a[asset_class]" value="stocks">
                            <input type="checkbox" name="a[included]" id="f5c1" class="form-check-input parent-checkbox check-main" value="yes" @if(($a['included'] ?? '') == 'yes') checked @endif><label for="f5c1"> </label>
                            
                        </td>
                        <td><input type="text" class="form-control" name="a[est]" value="{{ $a['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="a[val]" value="{{ $a['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="a[note]">{{ $a['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $b = $f5Data?->where('asset_class', 'bonds')->first()?->toArray() ?? [];
                        @endphp
                        <td>Listed Equities (Bonds)</td>
                        <td class="text-center">
                            <input type="hidden" name="b[asset_class]" value="bonds">
                            <input type="checkbox" name="b[included]"  id="f5c2" class="form-check-input parent-checkbox check-main" value="yes" @if(($b['included'] ?? '') == 'yes') checked @endif>
                            <label for="f5c2"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="b[est]" value="{{ $b['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="b[val]" value="{{ $b['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="b[note]">{{ $b['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $c = $f5Data?->where('asset_class', 'etfs')->first()?->toArray() ?? [];
                        @endphp
                        <td>ETFs/Mutual Funds</td>
                        <td class="text-center">
                            <input type="hidden" name="c[asset_class]" value="etfs">
                            <input type="checkbox" name="c[included]" id="f5c3" class="form-check-input parent-checkbox check-main" value="yes" @if(($c['included'] ?? '') == 'yes') checked @endif>
                            <label for="f5c3"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="c[est]" value="{{ $c['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="c[val]" value="{{ $c['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="c[note]">{{ $c['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $d = $f5Data?->where('asset_class', 'private_equity')->first()?->toArray() ?? [];
                        @endphp
                        <td>Private Equity</td>
                        <td class="text-center">
                            <input type="hidden" name="d[asset_class]" value="private_equity">
                            <input type="checkbox" name="d[included]" id="f5c4" class="form-check-input parent-checkbox check-main" value="yes" @if(($d['included'] ?? '') == 'yes') checked @endif>
                             <label for="f5c4"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="d[est]" value="{{ $d['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="d[val]" value="{{ $d['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="d[note]">{{ $d['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $e = $f5Data?->where('asset_class', 'internal_prom_note')->first()?->toArray() ?? [];
                        @endphp
                        <td>Promissory Note (internal)</td>
                        <td class="text-center">
                            <input type="hidden" name="e[asset_class]" value="internal_prom_note">
                            <input type="checkbox" name="e[included]"  id="f5c5" class="form-check-input parent-checkbox check-main" value="yes" @if(($e['included'] ?? '') == 'yes') checked @endif>
                            <label for="f5c5"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="e[est]" value="{{ $e['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="e[val]" value="{{ $e['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="e[note]">{{ $e['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $f = $f5Data?->where('asset_class', 'external_prom_note')->first()?->toArray() ?? [];
                        @endphp
                        <td>Promissory Note (external)</td>
                        <td class="text-center">
                            <input type="hidden" name="f[asset_class]" value="external_prom_note">
                            <input type="checkbox" name="f[included]" id="f5c6"  class="form-check-input parent-checkbox check-main" value="yes" @if(($f['included'] ?? '') == 'yes') checked @endif>
                            <label for="f5c6"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="f[est]" value="{{ $f['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="f[val]" value="{{ $f['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="f[note]">{{ $f['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $g = $f5Data?->where('asset_class', 'loan_receivable')->first()?->toArray() ?? [];
                        @endphp
                        <td>Loans Receivable</td>
                        <td class="text-center">
                            <input type="hidden" name="g[asset_class]" value="loan_receivable">
                            <input type="checkbox" name="g[included]"  id="f5c6" class="form-check-input parent-checkbox check-main" value="yes" @if(($g['included'] ?? '') == 'yes') checked @endif>
                            <label for="f5c6"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="g[est]" value="{{ $g['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="g[val]" value="{{ $g['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="g[note]">{{ $g['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $h = $f5Data?->where('asset_class', 'real_estate')->first()?->toArray() ?? [];
                        @endphp
                        <td>Real Estate</td>
                        <td class="text-center">
                            <input type="hidden" name="h[asset_class]" value="real_estate">
                            <input type="checkbox" name="h[included]" id="f5c7" class="form-check-input parent-checkbox check-main" value="yes" @if(($h['included'] ?? '') == 'yes') checked @endif>
                            <label for="f5c7"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="h[est]" value="{{ $h['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="h[val]" value="{{ $h['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="h[note]">{{ $h['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $i = $f5Data?->where('asset_class', 'digital_assets')->first()?->toArray() ?? [];
                        @endphp
                        <td>Digital Assets</td>
                        <td class="text-center">
                            <input type="hidden" name="i[asset_class]" value="digital_assets">
                            <input type="checkbox" name="i[included]" id="f5c8" class="form-check-input parent-checkbox check-main" value="yes" @if(($i['included'] ?? '') == 'yes') checked @endif>
                            <label for="f5c8"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="i[est]" value="{{ $i['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="i[val]" value="{{ $i['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="i[note]">{{ $i['notes'] ?? '' }}</textarea></td>
                    </tr>
                    <tr>
                        @php
                            $j = $f5Data?->where('asset_class', 'other')->first()?->toArray() ?? [];
                        @endphp
                        <td>Other:</td>
                        <td class="text-center">
                            <input type="hidden" name="j[asset_class]" value="other">
                            <input type="checkbox" name="j[included]" id="f5c9" class="form-check-input parent-checkbox check-main" value="yes" @if(($j['included'] ?? '') == 'yes') checked @endif>
                            <label for="f5c9"> </label>
                        </td>
                        <td><input type="text" class="form-control" name="j[est]" value="{{ $j['est_of_portfolio'] ?? '' }}"></td>
                        <td><input type="text" class="form-control" name="j[val]" value="{{ $j['valuation_support'] ?? '' }}"></td>
                        <td><textarea class="form-control" rows="1" name="j[note]">{{ $j['notes'] ?? '' }}</textarea></td>
                    </tr>

                </tbody>
            </table>
        </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-f-6" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>

</form>