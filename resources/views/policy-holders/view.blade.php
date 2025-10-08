@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Policyholder Details</h5>
                    <div>
                        @if(auth()->user()->can('policy-holders.edit'))
                        <a href="{{ route('policy-holders.edit', encrypt($policyHolder->id)) }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('policy-holders.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Basic Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Full Name:</strong></td>
                                <td>{{ $policyHolder->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td><span class="badge bg-info">{{ ucfirst($policyHolder->type) }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($policyHolder->status == 'other')
                                        <span class="badge bg-secondary">{{ ucfirst($policyHolder->status_name) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($policyHolder->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $policyHolder->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone Number:</strong></td>
                                <td>{{ $policyHolder->full_phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Gender:</strong></td>
                                <td>{{ ucfirst($policyHolder->gender) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date of Birth:</strong></td>
                                <td>{{ $policyHolder->dob ? $policyHolder->dob : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Place of Birth:</strong></td>
                                <td>{{ $policyHolder->place_of_birth }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Address Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Address Line 1:</strong></td>
                                <td>{{ $policyHolder->address_line_1 }}</td>
                            </tr>
                            <tr>
                                <td><strong>City:</strong></td>
                                <td>{{ $policyHolder->city }}</td>
                            </tr>
                            <tr>
                                <td><strong>Country:</strong></td>
                                <td>{{ $policyHolder->country }}</td>
                            </tr>
                            <tr>
                                <td><strong>Zipcode:</strong></td>
                                <td>{{ $policyHolder->zipcode }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="text-primary mb-3">Additional Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="15%"><strong>Created:</strong></td>
                                <td>{{ $policyHolder->created_at ? $policyHolder->created_at->format('F j, Y g:i A') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $policyHolder->updated_at ? $policyHolder->updated_at->format('F j, Y g:i A') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
