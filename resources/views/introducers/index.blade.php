@extends('layouts.app',['title' => $title, 'subTitle' => $subTitle,'datatable' => true, 'select2' => true, 'datepicker' => true])

@section('content')

<div class="row">
    <div class="col-12 user-main">
        <div class="row">
            @include('filters.user-management')
        </div>
        <div class="card mt-2">
            <div class="card-header">
                {{-- Filters --}}
                @if(auth()->user()->can('introducers.create'))
                <a href="{{ route('introducers.create') }}" class="btn btn-primary float-end"> 
                    <i class="fa fa-plus"></i> Add New 
                </a>
                @endif
                {{-- Filters --}}

                {{-- <button class="btn btn-outline-secondary me-2 btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="false" aria-controls="filterPanel">
                    <i class="fa fa-filter"></i> Filter
                </button> --}}

            </div>
            <div class="card-body">
                <table id="datatables-reponsive" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection


@push('js')
<script>

    $(document).ready(function () {

        let dataTable = $('#datatables-reponsive').DataTable({
            pageLength : 10,
            searching: false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ route(Request::route()->getName()) }}",
                "type": "GET",
                "data" : {
                    filter_status:function() {
                        return $("#filter-status").val();
                    },
                    filter_name: function () {
                        return $('#filter-name').val();
                    }
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'name',
                },
                {
                    data: 'type',
                },
                {
                    data: 'email',
                },
                {
                    data: 'contact_number',
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                }
            ],
        });

        $('#filter-status').select2({
            placeholder: 'Select status',
            theme: 'classic',
            width: '100%'
        });

        $('#btn-search').on('click', function () {
            dataTable.ajax.reload();
        });

        $('#btn-clear').on('click', function () {
            $('#filter-status').val(null).trigger('change');
            $('#filter-name').val(null);

            dataTable.ajax.reload();
        });

    });

</script>
@endpush