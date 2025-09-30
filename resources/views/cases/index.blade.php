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
 .select2-container .select2-selection--single .select2-selection__rendered {
	padding: 5px;
	height: 40px;
	line-height: 29px;
	border-radius: 25px;
	text-align: center;
	/* width: 140px; */
	text-transform: capitalize;
	font-size: 20px;
	background-color: #f5f5f5 !important;
	color: #000 !important;
}

    .cas-mnt-pill .case-management .table td ul li {

    }
.case-management .case-management-block {
	max-width: 1140px;
}
@media (max-width: 1400px) {
     .case-management .case-mananement-table .row .col-sm-12 {
            overflow-x: scroll;
            /* width: 1200px; */
        }
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
        <div class="sub-title">
            <h2>Case Management</h2>
        </div>
        <div class="case-management-block">
            <div class="form-content">
                <input type="text" class="form-control" placeholder="Case ID" id="filter-case">
            </div>
            <div class="form-content">
                <input type="text" class="form-control common-date-picker" placeholder="Date Opened - Renewal" id="filter-opened">
            </div>
            <div class="form-content">
                <input type="text" class="form-control" placeholder="Primary Holder" id="filter-holder">
            </div>
            <div class="form-content">
                <input type="text" class="form-control" placeholder="Introducer" id="filter-introducer">
            </div>
            <div class="form-content">
                <select class="form-control" id="filter-status">
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
        </div>
        <div class="case-mananement-table table-responsive cas-mnt-">
            <table class="table table-main" id="datatables-reponsive">
                <thead>
                    <tr>
                        <th scope="col">Likelihood</th>
                        <th scope="col">Policy Number</th>
                        <th scope="col">Issue Date</th>
                        <th scope="col">Effective Date</th>
                        <th scope="col">Policyholders</th>
                        <th scope="col">Introducer</th>
                        <th scope="col">IDF Manager</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
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
                    filter_case:function() {
                        return $("#filter-case").val();
                    },
                    filter_opened: function () {
                        return $('#filter-opened').val();
                    },
                    filter_holder: function () {
                        return $('#filter-holder').val();
                    },
                    filter_introducer: function () {
                        return $('#filter-introducer').val();
                    },
                    filter_status: function () {
                        return $('#filter-status').val();
                    }
                }
            },
            columns: [
                {
                    data: 'liklihood_c',
                },
                {
                    data: 'policy_number',
                },
                {
                    data: 'opening_date',
                },
                {
                    data: 'opening_date',
                },
                {
                    data: 'theholder',
                },
                {
                    data: 'introducer',
                },
                {
                    data: 'idfmgr',
                },
                {
                    data: 'status',
                },
                {
                    data: 'action',
                }
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

                $('.change-status').select2();
                $('.change-liklihood').select2();

                $('select.change-liklihood', this.api().table().body()).each(function() {
                    var selectedValue = String($('option:selected', this).text().trim());
                    

                    if (selectedValue === 'Low') {
                        $(this).next('.select2').find('.select2-selection__rendered').css({
                            'background-color' : '#c10000',
                            'color' : 'white'
                        });
                    } else if (selectedValue === 'Moderate') {
                        $(this).next('.select2').find('.select2-selection__rendered').css({
                            'background-color' : '#f5ac06',
                            'color' : 'white'
                        });
                    } else {
                        $(this).next('.select2').find('.select2-selection__rendered').css({
                            'background-color' : '#00950b',
                            'color' : 'white'
                        })
                    }
                });
            }
        });


        $(document).on('change', '.change-status', function () {
            let orderId = $(this).data('id');
            let status = $(this).val();
            let that = $(this);

            if (!confirm('Are you sure you want to change the status?')) {
                $(this).val($(this).data('last-selected'));
                return false;
            }

            $.ajax({
                url: "{{ route('case-status-change') }}",
                type: 'GET',
                data: {
                    id: orderId,
                    status: status
                },
                beforeSend: function () {
                    $('body').find('.LoaderSec').removeClass('d-none');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire('success', 'Status updated successfully.', 'success');
                        dataTable.ajax.reload();
                    } else {
                        $(that).val($(this).data('last-selected'));                            
                    }
                },
                complete: function (response) {
                    $('body').find('.LoaderSec').addClass('d-none');
                }
            });
        });

        $(document).on('change', '.change-liklihood', function () {
            let orderId = $(this).data('id');
            let status = $(this).val();
            let that = $(this);

            if (!confirm('Are you sure you want to change the liklihood probablity?')) {
                $(this).val($(this).data('last-selected'));
                return false;
            }

            $.ajax({
                url: "{{ route('case-liklihood-change') }}",
                type: 'GET',
                data: {
                    id: orderId,
                    status: status
                },
                beforeSend: function () {
                    $('body').find('.LoaderSec').removeClass('d-none');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire('success', 'Liklihood probability updated successfully.', 'success');
                        dataTable.ajax.reload();
                    } else {
                        $(that).val($(this).data('last-selected'));
                    }
                },
                complete: function (response) {
                    $('body').find('.LoaderSec').addClass('d-none');
                }
            });
        });

        $('.the-btn').on('click', function () {
            dataTable.ajax.reload();
        });

        $('.common-date-picker').datepicker({
            timepicker:false,
            format:'d/m/Y',
            className: 'common-datepicker-popup'
        });        

        $(document).on('click', '.search-btn', function () {
            dataTable.ajax.reload();

            let caseId = $('#filter-case').val();
            let date = $('#filter-opened').val();
            let primaryHolder = $('#filter-holder').val();
            let introducer = $('#filter-introducer').val();
            let status = $('#filter-status').val();

            if (caseId || date || primaryHolder || introducer || status) {
                $('.clear-btn').removeClass('d-none');
            } else {
                $('.clear-btn').addClass('d-none');
            }
        });

        $(document).on('click', '.clear-btn', function () {
            if (!$('.clear-btn').hasClass('d-none')) {
                $('.clear-btn').addClass('d-none');
            }

            $('#filter-case').val();
            $('#filter-opened').val();
            $('#filter-holder').val();
            $('#filter-introducer').val();
            $('#filter-status').val(null).trigger('change');

            dataTable.ajax.reload();
        });

    });
</script>
@endpush