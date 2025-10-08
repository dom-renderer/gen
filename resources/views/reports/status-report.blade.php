@extends('layouts.app',['title' => $title, 'subTitle' => $subTitle,'datatable' => true, 'select2' => true, 'datepicker' => true])

@push('css')
<style>
.dataTables_paginate {
    float: right;
    margin-top: 10px;
}
.custom-pagination {
    color: #a00;
    margin-right: 40px!important;
}
.custom-pagination .nav-btn {
    cursor: pointer;
    margin: 0 8px;
}
.custom-pagination .nav-btn:hover {
    color: #d00;
}
.table-responsive {
    overflow-x: hidden!important;
}

.case-management .case-management-block {
	max-width: 1140px;
}
@media (min-width: 1750px) {
        .container-fluid {
        /* max-width: 1466px;
        padding: 0; */
    }
}   
@media (max-width: 1500px) {
    .case-management .table tr td {
        padding:5px;
    }
}

    @media (max-width:1250px) {
        .case-management .case-mananement-table .table {
            min-width: 100%;
        }
        .case-management .case-mananement-table .row .col-sm-12 {
            overflow-x: scroll;
            width: 1200px;
        }
        .case-management .table th {
            font-size: 18px;
            line-height: 25px;
            padding: 5px;
        }
        .case-management .table tr td {
            padding: 5px;
            font-size: 18px;
        }
      }
</style>
@endpush

@section('content')

    <div class="case-management-main">
        <div class="reports-mng">
            <div class="sub-title">
                <h2>Status Report</h2>
            </div>
            <div class="case-management-block">
                <div class="form-content">
                    <select class="form-control" id="filter-status" style="width:300px;">
                        <option selected value=""> All </option>
                        @foreach (Helper::$status as $status)
                            <option value="{{ $status }}"> {{ $status }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-button">
                    <button type="button" class="btn red-btn search-btn the-btn">Search</button>
                </div>
                <div class="form-button">
                    <button type="button" class="btn red-btn clear-btn d-none">Clear</button>
                </div>
                <div class="form-button">
                    <a class="btn btn-success" href="{{ route('reports.status-report.export') }}">Export</a>
                </div>
            </div>
        </div>
        <div class="case-mananement-table table-responsive">
            <table class="table table-main" id="datatables-reponsive">
                <thead>
                    <tr>
                        <th>Policy Number</th>
                        <th>Status</th>
                        <th>Effective Date</th>
                        <th>Issue Date</th>
                        <th>Anniversary Date</th>
                        <th>IDF Manager</th>
                        <th>Holders</th>
                        <th>Insureds</th>
                        <th>Introducers</th>
                    </tr>
                </thead>
                <tbody>   
                </tbody>
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
            "lengthChange": false,
            processing: true,
            serverSide: true,
            language: {
                paginate: {
                    previous: "",
                    next: ""
                }
            },
            ajax: {
                "url": "{{ route(Request::route()->getName()) }}",
                "type": "GET",
                "data" : {
                    filter_status: function () {
                        return $('#filter-status').val();
                    }
                }
            },
            columns: [
                {data: 'policy_number', name: 'policy_number'},
                {data: 'policy_status', name: 'policy_status'},
                {data: 'effective_date', name: 'effective_date'},
                {data: 'issue_date', name: 'issue_date'},
                {data: 'anniversary_date', name: 'anniversary_date'},
                {data: 'idf_manager', name: 'idf_manager'},
                {data: 'holders', name: 'holders', orderable: false, searchable: false},
                {data: 'insureds', name: 'insureds', orderable: false, searchable: false},
                {data: 'introducers', name: 'introducers', orderable: false, searchable: false},
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

                $('.prev-btn').off('click').on('click', function() {
                    api.page('previous').draw('page');
                });
                $('.next-btn').off('click').on('click', function() {
                    api.page('next').draw('page');
                });
            },
            initComplete: function () {
                $('.change-status').select2();
            }
        });
        $(document).on('click', '.search-btn', function () {
            dataTable.ajax.reload();

            let status = $('#filter-status').val();

            if (status) {
                $('.clear-btn').removeClass('d-none');
            } else {
                $('.clear-btn').addClass('d-none');
            }
        });

        $(document).on('click', '.clear-btn', function () {
            if (!$('.clear-btn').hasClass('d-none')) {
                $('.clear-btn').addClass('d-none');
            }

            $('#filter-status').val(null).trigger('change');

            dataTable.ajax.reload();
        });

    });
</script>
@endpush