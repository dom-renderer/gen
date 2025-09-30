@extends('layouts.app',['title' => $title, 'subTitle' => $subTitle,'datatable' => true, 'select2' => true, 'datepicker' => true])

@section('content')

    <div class="case-management-main">
        <div class="sub-title">
            <h2>Policyholders</h2>
        </div>
        <div class="case-management-block casemange">
            <div class="form-content">
                <select class="form-select select2" id="filter-type">
                    <option value="">All Types</option>
                    <option value="individual">Individual</option>
                    <option value="entity">Entity</option>
                </select>
            </div>
            <div class="form-content">
                <select class="form-select select2" id="filter-status">
                    <option value="">All Status</option>
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="divorced">Divorced</option>
                    <option value="separated">Separated</option>
                    <option value="corporation">Corporation</option>
                    <option value="llc">LLC</option>
                    <option value="trust">Trust</option>
                    <option value="partnership">Partnership</option>
                    <option value="foundation">Foundation</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-content">
                <select class="form-select select2" id="filter-gender">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="form-content">
                <input type="text" class="form-control" id="filter-name" placeholder="Search by name, email, phone...">
            </div>
            <div class="form-button">
                <button type="button" class="btn red-btn the-btn">Search</button>
            </div>
        </div>
        <div class="case-mananement-table table-responsive">
            <table class="table" id="datatables-reponsive">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Type</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Status</th>
                        <th scope="col">Address</th>
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
            ajax: {
                "url": "{{ route(Request::route()->getName()) }}",
                "type": "GET",
                "data" : {
                    filter_type: function() {
                        return $("#filter-type").val();
                    },
                    filter_status: function() {
                        return $("#filter-status").val();
                    },
                    filter_gender: function() {
                        return $("#filter-gender").val();
                    },
                    filter_name: function () {
                        return $('#filter-name').val();
                    }
                }
            },
            columns: [
                {
                    data: 'full_name',
                },
                {
                    data: 'email',
                },
                {
                    data: 'phone_number',
                },
                {
                    data: 'type',
                },
                {
                    data: 'gender',
                },
                {
                    data: 'status',
                },
                {
                    data: 'address',
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                }
            ]
        });

        $('.the-btn').on('click', function () {
            dataTable.ajax.reload();
        });

    $('#filter-type').select2({
        placeholder: 'All Types',
        theme: 'classic',
        width: '100%'
    });

    $('#filter-status').select2({
        placeholder: 'All Status',
        theme: 'classic',
        width: '100%'
    });
    $('#filter-gender').select2({
        placeholder: 'All Genders',
        theme: 'classic',
        width: '100%'
    });

        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('policy-holders.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            );
                            dataTable.ajax.reload();
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        });



    });
</script>
@endpush
