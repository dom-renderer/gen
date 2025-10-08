<div class="collapse mt-3" id="filterPanel">
    <div class="card mb-3">
        <div class="card-body flt-user">
            <div class="row">

                @if(Request::route()->getName() == 'users.index')
                    <div class="col-md-3">
                        <label for="filter-name" class="form-label"> Search </label>
                        <input type="text" id="filter-name" class="form-control" placeholder="Name, Email or Phone Number">
                    </div>

                    <div class="col-md-3">
                        <label for="filter-role mb-3" class="form-label">Role</label>
                        <select id="filter-role" class="form-select" multiple>
                            @forelse (\Spatie\Permission\Models\Role::whereNotIn('name', App\Http\Controllers\UserController::$excludeRoles)->get() as $item)
                                <option value="{{ $item->id }}"> {{ $item->title }} </option>
                            @empty                                
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-status mb-4" class="form-label">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                @endif

                <div class="col-12 d-flex justify-content-end mt-3">
                    <button type="button" id="btn-search" class="btn btn-primary me-2">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <button type="button" id="btn-clear" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>