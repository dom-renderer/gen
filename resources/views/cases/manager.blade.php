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
        <div class="sub-title">
            <h2>Manage Case Managers
                <strong style="float: right;"> {{ $policy->policy_number }} </strong>
            </h2>
        </div>

        <div class="case-mananement-table table-responsive">
            <form action="{{ route('cases.case-manager', encrypt($policy->id)) }}" method="POST"> @csrf
                


                <div class="row">
                    <div class="mb-2">
                        <label for="managers" class="form-label"> Select Managers </label>
                        <select name="managers[]" id="managers" multiple>
                            @forelse ($policy->managers as $manager)
                                @if(isset($manager->user->id))
                                    <option value="{{ $manager->user->id }}" selected> {{ $manager->user->name }} </option>
                                @endif
                            @empty
                            @endforelse
                        </select>
                    </div>

                    <div class="mb-2">
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection


@push('js')
<script>
    $(document).ready(function () {
        $('#managers').select2({
            allowClear: true,
            placeholder: 'Select case managers',
            theme: 'classic',
            width: '100%',
            ajax: {
                url: "{{ route('user-list') }}",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchQuery: params.term,
                        page: params.page || 1,
                        roles: @json(['client-service-team']),
                        _token: "{{ csrf_token() }}"
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });
    });
</script>
@endpush