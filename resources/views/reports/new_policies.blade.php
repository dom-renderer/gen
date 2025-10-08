@extends('layouts.app', ['datatable' => true])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            
            <div class="sub-title ps-3 pe-3"> 
                <h2>New Policies Issued (YTD / QTD / MTD)</h2>
            </div>
            
            <div class="card-body">
                <!-- <div class="row mb-4 bg-matter">
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <div class="d-flex justify-content-between"><span>YTD</span><strong>{{ $ytd->count() }}</strong></div>
                            <small class="text-muted">{{ $startOfYear->format('Y-m-d') }} to {{ $now->format('Y-m-d') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <div class="d-flex justify-content-between"><span>QTD</span><strong>{{ $qtd->count() }}</strong></div>
                            <small class="text-muted">{{ $startOfQuarter->format('Y-m-d') }} to {{ $now->format('Y-m-d') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <div class="d-flex justify-content-between"><span>MTD</span><strong>{{ $mtd->count() }}</strong></div>
                            <small class="text-muted">{{ $startOfMonth->format('Y-m-d') }} to {{ $now->format('Y-m-d') }}</small>
                        </div>
                    </div>
                </div> --> 

                <ul class="nav nav-tabs new-plc" id="npTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="ytd-tab" data-bs-toggle="tab" data-bs-target="#ytd-pane" type="button" role="tab">YTD ({{ $ytd->count() }}) </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="qtd-tab" data-bs-toggle="tab" data-bs-target="#qtd-pane" type="button" role="tab">QTD ({{ $qtd->count() }}) </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mtd-tab" data-bs-toggle="tab" data-bs-target="#mtd-pane" type="button" role="tab">MTD ({{ $mtd->count() }}) </button>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="npTabsContent">
                    <div class="tab-pane fade show active" id="ytd-pane" role="tabpanel" aria-labelledby="ytd-tab">
                        <div class="d-flex justify-content-end mb-2">
                            <a href="{{ route('reports.new-policies.export', ['range' => 'ytd']) }}" class="btn btn-success">Export Excel</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="np-ytd">
                                <thead>
                                    <tr>
                                        <th>Likelihood</th>
                                        <th>Policy Number</th>
                                        <th>Issue Date</th>
                                        <th>Effective Date</th>
                                        <th>Policyholders</th>
                                        <th>Introducer</th>
                                        <th>IDF Manager</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="qtd-pane" role="tabpanel" aria-labelledby="qtd-tab">
                        <div class="d-flex justify-content-end mb-2">
                            <a href="{{ route('reports.new-policies.export', ['range' => 'qtd']) }}" class="btn btn-success">Export Excel</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="np-qtd">
                                <thead>
                                    <tr>
                                        <th>Likelihood</th>
                                        <th>Policy Number</th>
                                        <th>Issue Date</th>
                                        <th>Effective Date</th>
                                        <th>Policyholders</th>
                                        <th>Introducer</th>
                                        <th>IDF Manager</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="mtd-pane" role="tabpanel" aria-labelledby="mtd-tab">
                        <div class="d-flex justify-content-end mb-2">
                            <a href="{{ route('reports.new-policies.export', ['range' => 'mtd']) }}" class="btn btn-success">Export Excel</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="np-mtd">
                                <thead>
                                    <tr>
                                        <th>Likelihood</th>
                                        <th>Policy Number</th>
                                        <th>Issue Date</th>
                                        <th>Effective Date</th>
                                        <th>Policyholders</th>
                                        <th>Introducer</th>
                                        <th>IDF Manager</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function(){
    function initTable(sel, range){
        return $(sel).DataTable({
            pageLength: 25,
            searching: false,
            ordering: false,
            lengthChange: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.new-policies') }}",
                type: 'GET',
                data: { range: range }
            },
            columns: [
                { data: 'liklihood_c' },
                { data: 'policy_number' },
                { data: 'opening_date' },
                { data: 'opening_date' },
                { data: 'theholder' },
                { data: 'introducer' },
                { data: 'idfmgr' },
                { data: 'status' },
                { data: 'action' },
            ],
            drawCallback: function(settings) {

                $(settings.nTable).find('tbody tr').each(function() {

                    var liklihoodValue = $(this).find('td').eq(0).text().trim();
                    
                    if (liklihoodValue === 'Low') {
                        $(this).find('td').eq(0).css({
                            // 'background-color': '#c10000',
                            // 'color': 'white'
                        });
                    } else if (liklihoodValue === 'Moderate') {
                        $(this).find('td').eq(0).css({
                            // 'background-color': '#f5ac06',
                            // 'color': 'white'
                        });
                    } else if (liklihoodValue === 'High') {
                        $(this).find('td').eq(0).css({
                            // 'background-color': '#00950b',
                            // 'color': 'white'
                        });
                    }
                });
            }
        });
    }
    const ytd = initTable('#np-ytd', 'ytd');
    let qtd, mtd;
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('data-bs-target');
        if (target === '#qtd-pane' && !qtd) qtd = initTable('#np-qtd', 'qtd');
        if (target === '#mtd-pane' && !mtd) mtd = initTable('#np-mtd', 'mtd');
        if (target === '#qtd-pane' && qtd) { setTimeout(()=>qtd.columns.adjust().draw(false), 0); }
        if (target === '#mtd-pane' && mtd) { setTimeout(()=>mtd.columns.adjust().draw(false), 0); }
    });
});
</script>
@endpush
