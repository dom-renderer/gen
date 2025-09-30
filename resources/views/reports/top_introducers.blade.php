@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 bg-dm">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="sub-title">
                    <h2>Top Introducers</h2>
                </div>
                {{-- <h5 class="mb-0">Top Introducers</h5> --}}
                <form class="d-flex" method="GET" action="{{ route('reports.top-introducers') }}">
                    <input type="hidden" name="limit" value="{{ $limit }}" />
                    <select name="name" class="form-select me-2 form-control selct-common" onchange="this.form.submit()">
                        <option value="">Select introducer to view policies</option>
                        @foreach($leaders as $row)
                            <option value="{{ $row->introducer_label }}" @if(($introducerName ?? '') === $row->introducer_label) selected @endif>
                                {{ $row->introducer_label }} ({{ $row->policies_count }})
                            </option>
                        @endforeach
                    </select>
                    @if($introducerName)
                        <a href="{{ route('reports.top-introducers') }}" class="btn btn-secondary">Clear</a>
                    @endif
                </form>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Introducer</th>
                                        <th class="text-end">Policies</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaders as $row)
                                        <tr>
                                            <td>{{ $row->introducer_label }}</td>
                                            <td class="text-end">{{ $row->policies_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if($introducerName)
                
                <h6 class="mb-3 fs-3">Policies introduced by: {{ $introducerName }}</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr><th>#</th><th>Policy</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($policies as $p)
                                <tr>
                                    <td>{{ $p->id }}</td>
                                    <td><a href="{{ route('cases.view', encrypt($p->id)) }}">{{ $p->policy_number }}</a></td>
                                    <td>{{ $p->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No policies found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


