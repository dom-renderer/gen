<form id="form-section-f-4" class="case-premeim">
    
    <div class="mt-4 overflow-auto">
        <table class="table table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>Fee Type</th>
                    <th>Frequency</th>
                    <th>Amount/Rate</th>
                    <th>Recipient</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @php
                        $a = $f4Data?->where('type', 'management_fee')->first()?->toArray() ?? [];
                    @endphp
                    <td>Investment Management Fee</td>
                    <td><input type="hidden" name="a[type]" value="management_fee"> <input type="text" name="a[frequency]" class="form-control" value="{{ $a['frequency'] ?? '' }}"></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="a[amount]" value="{{ $a['amount'] ?? '' }}"></td>
                    <td><input type="text" class="form-control" name="a[recipient]" value="{{ $a['recipient'] ?? '' }}"></td>
                    <td><textarea class="form-control" rows="1" name="a[note]">{{ $a['notes'] ?? '' }}</textarea></td>
                </tr>

                @php $b = $f4Data?->where('type', 'idf_manager_fee')->first()?->toArray() ?? []; @endphp
                <tr>
                    <td>IDF Manager Fee</td>
                    <td><input type="hidden" name="b[type]" value="idf_manager_fee"> <input type="text" name="b[frequency]" class="form-control" value="{{ $b['frequency'] ?? '' }}"></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="b[amount]" value="{{ $b['amount'] ?? '' }}"></td>
                    <td><input type="text" class="form-control" name="b[recipient]" value="{{ $b['recipient'] ?? '' }}"></td>
                    <td><textarea class="form-control" rows="1" name="b[note]">{{ $b['notes'] ?? '' }}</textarea></td>
                </tr>

                @php $c = $f4Data?->where('type', 'custody_fee')->first()?->toArray() ?? []; @endphp
                <tr>
                    <td>Custody Fee</td>
                    <td><input type="hidden" name="c[type]" value="custody_fee"> <input type="text" name="c[frequency]" class="form-control" value="{{ $c['frequency'] ?? '' }}"></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="c[amount]" value="{{ $c['amount'] ?? '' }}"></td>
                    <td><input type="text" class="form-control" name="c[recipient]" value="{{ $c['recipient'] ?? '' }}"></td>
                    <td><textarea class="form-control" rows="1" name="c[note]">{{ $c['notes'] ?? '' }}</textarea></td>
                </tr>

                @php $d = $f4Data?->where('type', 'legal_sturcturing_fee')->first()?->toArray() ?? []; @endphp
                <tr>
                    <td>Legal/Structuring Fee</td>
                    <td><input type="hidden" name="d[type]" value="legal_sturcturing_fee"> <input type="text" name="d[frequency]" class="form-control" value="{{ $d['frequency'] ?? '' }}"></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="d[amount]" value="{{ $d['amount'] ?? '' }}"></td>
                    <td><input type="text" class="form-control" name="d[recipient]" value="{{ $d['recipient'] ?? '' }}"></td>
                    <td><textarea class="form-control" rows="1" name="d[note]">{{ $d['notes'] ?? '' }}</textarea></td>
                </tr>

                @php $e = $f4Data?->where('type', 'trustee_fee')->first()?->toArray() ?? []; @endphp
                <tr>
                    <td>Trustee Fee</td>
                    <td><input type="hidden" name="e[type]" value="trustee_fee"> <input type="text" name="e[frequency]" class="form-control" value="{{ $e['frequency'] ?? '' }}"></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="e[amount]" value="{{ $e['amount'] ?? '' }}"></td>
                    <td><input type="text" class="form-control" name="e[recipient]" value="{{ $e['recipient'] ?? '' }}"></td>
                    <td><textarea class="form-control" rows="1" name="e[note]">{{ $e['notes'] ?? '' }}</textarea></td>
                </tr>

                @php $f = $f4Data?->where('type', 'other')->first()?->toArray() ?? []; @endphp
                <tr>
                    <td>Other</td>
                    <td><input type="hidden" name="f[type]" value="other"> <input type="text" name="f[frequency]" class="form-control" value="{{ $f['frequency'] ?? '' }}"></td>
                    <td><input type="number" min="0" step="0.01" class="form-control" name="f[amount]" value="{{ $f['amount'] ?? '' }}"></td>
                    <td><input type="text" class="form-control" name="f[recipient]" value="{{ $f['recipient'] ?? '' }}"></td>
                    <td><textarea class="form-control" rows="1" name="f[note]">{{ $f['notes'] ?? '' }}</textarea></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mb-3 float-end">
        @if(request()->route()->getName() != 'cases.view')
        {{-- <button type="button" data-type="draft" class="btn btn-primary save-draft">Save Draft</button> --}}
        <button type="submit" data-type="next" data-next="section-f-5" class="btn btn-primary save-next">Save & Next</button>
        @endif
    </div>

</form>