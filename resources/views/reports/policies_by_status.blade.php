@extends('layouts.app')
@push('css')
    <style>
        .container-fluid {
            max-width: inherit;
        }
    </style>
@endpush
@section('content')
<div class="row ">
    <div class="col-12">
        <div class="card rpt-crd">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="sub-title">
                    <h2>
                        Policies by Status
                    </h2>
                </div>
                {{-- <h5 class="mb-0">Policies by Status</h5> --}}
                <form method="GET" class="d-flex align-items-center" action="{{ route('reports.policies-by-status') }}">
                    <select name="status" class="form-select me-2 form-control selct-common" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @if(($filterStatus ?? '') === $status) selected @endif>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @if($filterStatus)
                        <a href="{{ route('reports.policies-by-status') }}" class="btn red-btn search-btn the-btn">Clear</a>
                    @endif
                    <a href="{{ route('reports.policies-by-status.export', ['status' => $filterStatus]) }}" style="width: 250px;" class="btn btn-success ms-2" >Export Excel</a>
                </form>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    @foreach($statuses as $status)
                        @php $key = strtolower($status); $count = optional($summary->get($key))->total ?? 0; @endphp
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-2">
                            <a href="{{ route('reports.policies-by-status', ['status' => $status]) }}" class="text-decoration-none">
                                <div class="p-3 border rounded h-100 d-flex justify-content-between align-items-center @if(request('status') == $status) act-man @endif">
                                    <span>{{ ucfirst($status) }}</span>
                                    <strong>{{ $count }}</strong>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Policy Number</th>
                                <th>Status</th>
                                <th>Opening Date</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($policies as $policy)
                                <tr>
                                    <td>{{ $policy->id }}</td>
                                    <td><a href="{{ route('cases.view', encrypt($policy->id)) }}">{{ $policy->policy_number }}</a></td>
                                    <td>{{ $policy->status }}</td>
                                    <td>
                                        @if(!empty($policy->opening_date))
                                            {{ \Carbon\Carbon::parse($policy->opening_date)->format('Y-m-d H:i') }}
                                        @endif
                                    </td>
                                    <td>{{ $policy->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No policies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $policies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


