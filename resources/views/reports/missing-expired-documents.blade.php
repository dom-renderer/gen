@extends('layouts.app',['title' => $title, 'subTitle' => $subTitle,'datatable' => true, 'select2' => false, 'datepicker' => false])

@push('css')
<style>
.dataTables_paginate { float: right; margin-top: 10px; }
.custom-pagination { color: #a00; margin-right: 40px!important; }
.custom-pagination .nav-btn { cursor: pointer; margin: 0 8px; }
.custom-pagination .nav-btn:hover { color: #d00; }
.table-responsive { overflow-x: hidden!important; }
.case-management .case-management-block { max-width: 1140px; }
@media (min-width: 1750px) { 
    /* .container-fluid { max-width: 1466px; padding: 0; } */
 }
    
@media (max-width: 1500px) { .case-management .table tr td { padding:5px; } }
@media (max-width:1250px) {
    .case-management .case-mananement-table .table { min-width: 100%; }
    .case-management .case-mananement-table .row .col-sm-12 { overflow-x: scroll; width: 1200px; }
    .case-management .table th { font-size: 18px; line-height: 25px; padding: 5px; }
    .case-management .table tr td { padding: 5px; font-size: 18px; }
}
/* Traffic light dot */
.tl-dot { display: inline-block; width: 14px; height: 14px; border-radius: 50%; vertical-align: middle; }
.tl-red { background-color: #d9534f; }
.tl-amber { background-color: #f0ad4e; }
.tl-green { background-color: #5cb85c; }
</style>
@endpush

@section('content')

    <div class="case-management-main">
        <div class="reports-mng">
            <div class="sub-title">
                <h2>Missing/Expired Documents</h2>
            </div>
            <div class="case-management-block">
                <div class="form-button">
                    <a class="btn btn-success" href="{{ route('reports.missing-docs.export') }}">Export</a>
                </div>
            </div>
        </div>
        <div class="case-management-block">
            <div class="form-content"></div>
            <div class="form-button"></div>
        </div>
        <div class="case-mananement-table table-responsive">
            <table class="table table-main" id="datatables-reponsive">
                <thead>
                    <tr>
                        <th>Traffic</th>
                        <th>Policy Number</th>
                        <th>Role</th>
                        <th>User</th>
                        <th>Document</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection

@push('js')
<script>
    $(document).ready(function () {
        let dataTable = $('#datatables-reponsive').DataTable({
            pageLength : 50,
            searching: false,
            ordering: false,
            lengthChange: false,
            processing: true,
            serverSide: true,
            language: { paginate: { previous: "", next: "" } },
            ajax: { url: "{{ route(Request::route()->getName()) }}", type: "GET" },
            columns: [
                {data: 'traffic', orderable: false, searchable: false},
                {data: 'policy_number', name: 'policy_number'},
                {data: 'role', name: 'role'},
                {data: 'user', name: 'user'},
                {data: 'document', name: 'document'},
                {data: 'expdt'},
                {data: 'status', name: 'status'},
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var info = api.page.info();
                $('.dataTables_paginate').empty();
                var pagination = '<span class="custom-pagination">';
                if (info.page > 0) {
                    pagination += '<span class="nav-btn prev-btn"><img style="transform: scaleX(-1);" src="{{ asset('assets/images/svg/next-arrow.svg') }}" alt="prev-arrow" class="img-fluid"></span>';
                }
                pagination += (info.page + 1) + ' of ' + info.pages;
                if (info.page < info.pages - 1) {
                    pagination += '<span class="nav-btn next-btn"><img src="{{ asset('assets/images/svg/next-arrow.svg') }}" alt="next-arrow" class="img-fluid"></span>';
                }
                pagination += '</span>';
                $('.dataTables_paginate').append(pagination);
                $('.prev-btn').off('click').on('click', function() { api.page('previous').draw('page'); });
                $('.next-btn').off('click').on('click', function() { api.page('next').draw('page'); });
            }
        });
    });
</script>
@endpush


