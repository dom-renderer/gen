@extends('layouts.app', ['title' => $title ?? 'Reports', 'subTitle' => $subTitle ?? 'Status Report'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Status Report</h5>
                <div class="d-flex">
                    <form method="GET" class="d-flex" action="{{ route('reports.status-report') }}">
                        <select name="status" class="form-select me-2" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            @foreach(['Prospect','Active','Terminated','Cancelled','Surrender','1035 Exchange','Under Review','Lapse','Draft'] as $s)
                                <option value="{{ $s }}" @if(($statusFilter ?? '') === $s) selected @endif>{{ $s }}</option>
                            @endforeach
                        </select>
                        @if($statusFilter)
                            <a href="{{ route('reports.status-report') }}" class="btn btn-outline-secondary me-2">Clear</a>
                        @endif
                    </form>
                    <a class="btn btn-success" href="{{ route('reports.status-report.export', ['status' => $statusFilter]) }}">Export Excel</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Policy #</th>
                                <th>Type</th>
                                <th>User</th>
                                <th>Policy Status</th>
                                <th>Issue Date</th>
                                <th>Effective Date</th>
                                <th>Anniversary Date</th>
                                <th>IDF Manager</th>
                                <th>Introducer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td>{{ $row['policy_number'] }}</td>
                                    <td>{{ $row['type'] }}</td>
                                    <td>{{ $row['user'] }}</td>
                                    <td>{{ $row['policy_status'] }}</td>
                                    <td>{{ $row['issue_date'] }}</td>
                                    <td>{{ $row['effective_date'] }}</td>
                                    <td>{{ $row['anniversary_date'] }}</td>
                                    <td>{{ $row['idf_manager'] }}</td>
                                    <td>{{ $row['introducer'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


