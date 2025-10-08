@extends('layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
<style>
    .daterangepicker .ranges li.active {
        background-color: #ab182d!important;
    }

    .daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #ab182d!important;
    }

    .daterangepicker td.in-range {
        background-color: #f8ebeb!important;
    }
    
    .cards-container .col-sm-6.col-md-4 {
        margin-bottom: 60px;
    }
   
    
    @media (min-width:1750px) {
        .container-fluid {
            /* padding: 0; */
        }
        .cards-wrapper .access-report .access-report-detail {
            margin-right: 40px;
        }
         .footer-main {
            padding-right: 190px;
        }
    }
    @media (max-width:1499px) {
         .cards-wrapper .access-report .access-report-detail {
            margin-right: 0;
        }
         .footer-main {
            padding-right: 0;
        }
       
    }
     @media (min-width:1499px) and (max-width:1750px) {
        .cards-wrapper .access-report .access-report-detail {
            margin-right: 40px;
        }
         .footer-main {
            padding-right: 0;
        }
     }

      @media (min-width:992PX) and (max-width:1250PX) {
            .cards-wrapper .cards-main .card-block {
                height: 100%;
            }
      }

     
</style>
@endpush

@section('content')
<div class="col-xl-12 d-flex">
    <div class="cards-main w-100">
        <div class="cards-container ">
            <div class="row">
                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/folder.svg') }}" alt="folder" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $cases }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Active Policies</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/folder.svg') }}" alt="folder" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $surrender }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Surrendered Policies</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/folder.svg') }}" alt="folder" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $terminated }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Terminated Policies</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/folder.svg') }}" alt="folder" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $exchange }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>1035 Exchange Policies</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/folder.svg') }}" alt="folder" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $lapse }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Lapse Policies</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/folder.svg') }}" alt="folder" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $prospect }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Prospect Policies</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/folder.svg') }}" alt="folder" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $underreview }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Under Review Policies</h5>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/form.svg') }}" alt="form" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $idf }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Insurance Dedicated Funds</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/patnership.svg') }}" alt="patnership" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $introducers }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Introducers</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                    <div class="card-block">
                        <div class="icon">
                            <img src="{{ asset('assets/images/svg/patnership.svg') }}" alt="patnership" class="img-fluid">
                        </div>
                        <div class="heading">
                            <div class="title">
                                <h4> {{ $custodians }} </h4>
                            </div>
                            <div class="sub-title">
                                <h5>Custodians</h5>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="access-report">
            <div class="access-report-detail">
                <div class="title">
                    <h4>Quick Access Reports</h4>
                </div>
                <form class="search-form" role="search" method="GET">
                    <input class="form-control" type="search" name="date_range" placeholder="Search" id="task-date-range-picker" readonly />
                    <button type="submit" class="search-icon" style="background: none; border: none;">
                        <img src="{{ asset('assets/images/svg/search.svg') }}" alt="search" class="img-fluid">
                    </button>
                </form>
            </div>
            <div class="cards-container">
                <div class="row">
                    <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                        <div class="card-block">
                            <div class="icon">
                                <img src="{{ asset('assets/images/svg/arrow-patnership.svg') }}" alt="patnership" class="img-fluid">
                            </div>
                            <div class="heading">
                                <div class="sub-title">
                                    <h5>Top Introducers</h5>
                                </div>
                            </div>
                            <div class="download">
                                <a href="javascript:void(0);"><img src="{{ asset('assets/images/svg/down-arrow.svg') }}"
                                        alt="down-arrow" class="img-fluid"> Download</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                        <div class="card-block">
                            <div class="icon">
                                <img src="{{ asset('assets/images/svg/fomr-aarow.svg') }}" alt="form-arrow" class="img-fluid">
                            </div>
                            <div class="heading">
                                <div class="sub-title">
                                    <h5>Top Policyholders</h5>
                                </div>
                            </div>
                            <div class="download">
                                <a href="javascript:void(0);"><img src="{{ asset('assets/images/svg/down-arrow.svg') }}"
                                        alt="down-arrow" class="img-fluid"> Download</a>
                            </div>
                        </div>
                    </div>
                    <div  class="col-sm-6 col-md-4 col-lg-6  col-xl-4">
                        <div class="card-block">
                            <div class="icon">
                                <img src="{{ asset('assets/images/svg/user.svg') }}" alt="folder" class="img-fluid">
                            </div>
                            <div class="heading">
                                <div class="sub-title">
                                    <h5>Top Employees</h5>
                                </div>
                            </div>
                            <div class="download">
                                <a href="javascript:void(0);"><img src="{{ asset('assets/images/svg/down-arrow.svg') }}"
                                        alt="down-arrow" class="img-fluid"> Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
<script>
$(document).ready(function () {

    let startTaskDate = moment().startOf('month');
    let endTaskDate = moment().endOf('month');

    function cb(start, end) {
        $('#task-date-range-picker').val(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
    }

    $('#task-date-range-picker').daterangepicker({
        startDate: startTaskDate,
        endDate: endTaskDate,
        locale: {
            format: 'DD-MM-YYYY'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(startTaskDate, endTaskDate);

    $('#task-date-range-picker').on('apply.daterangepicker', function(ev, picker) {
        startTaskDate = picker.startDate;
        endTaskDate = picker.endDate
    });

});
</script>
@endpush