@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@section('content')
<div class="row">
    <form method="POST" action="{{ route('roles.store') }}"> @csrf    
        <div class="col-md-12 roles-create">
            <div class="card">
                <!-- <div class="card-header"> {{  $subTitle  }} </div> -->
                <div class="sub-title">
                    <h2> {{  $subTitle  }}</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label"> Name <span class="text-danger"> * </span> </label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
    
            <div class="card">
                <!-- <div class="card-header">Select Permissions</div> -->
                 <div class="sub-title">
                    <h2> Select Permissions</h2>
                </div>
                <div class="card-body">

                    <div class="row">
                        @forelse($permissions as $key => $permission)
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h3>
                                            <input type="checkbox" class="parent-checkbox check-main" id="{{ Str::slug($key) }}">
                                            <label for="{{ Str::slug($key) }}"> {{  ucwords(str_replace('-', ' ', $key))  }} </label>
                                        </h3>
                                        @forelse($permission as $row)
                                        <input type="checkbox" class="check-main" name="permissions[]" data-parent="{{ Str::slug($key) }}" id="{{ $row->name }}" value="{{ $row->id }}">
                                        <label for="{{ $row->name }}">{{ $row->title }}</label> <br>
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary">Create Role</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection 

@push('js')
<script>
    $(document).ready(function () {
        $('.parent-checkbox').on('change', function () {
            if ($(this).is(':checked')) {
                $(`[data-parent="${$(this).attr('id')}"]`).prop('checked', true);
            } else {
                $(`[data-parent="${$(this).attr('id')}"]`).prop('checked', false);
            }
        })                
    });
</script>
@endpush
