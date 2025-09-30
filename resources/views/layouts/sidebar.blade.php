@php
    $user = auth()->user();

    $userManagementPermissions = [
        'users.index', 'roles.index'
    ];

    $customerManagementPermissions = [
        'introducers.index'
    ];

    $caseManagementPermissions = [
        'cases.index', 'cases.create'
    ];

    $canViewUserManagement = collect($userManagementPermissions)->contains(fn($perm) => $user->can($perm));
    $canViewCustomerManagement = collect($customerManagementPermissions)->contains(fn($perm) => $user->can($perm));
    $canViewCaseManagement = collect($caseManagementPermissions)->contains(fn($perm) => $user->can($perm));

    $segment = request()->segment(1);

    $userManagementSegments = ['users', 'roles'];
    $customerManagementSegment = ['introducers'];
    $caseManagementSegment = ['cases'];
    $reportsSegments = ['reports'];

    $activeUserManagement = in_array($segment, $userManagementSegments);
    $activeCustomerManagement = in_array($segment, $customerManagementSegment);
    $activeCaseManagement = in_array($segment, $caseManagementSegment);
    $activeReports = in_array($segment, $reportsSegments);

    $caseId = implode('***', [auth()->id(), null, 'sha-2']);
    $oldCasePending = false;

@endphp

<aside class="sidebar-main-menu collapse show" id="navbar-content">
    <nav class="navbar navbar-expand">
        <div class="sidebar-logo">
            <a class="navbar-brand" href="index.html">
                <img src="{{ Helper::logo() }}" alt="logo" class="img-fluid">
            </a>

            <span>Case Management Portal</span>
        </div>
        <div class="collapse navbar-collapse" id="navbar-content">
            <div class="sidebar-menu-heading">
                <img src="{{ asset('assets/images/svg/dashboard.svg') }}" alt="dashboard" class="img-fluid">
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </div>
            <ul class="navbar-nav">


                @if ($canViewCaseManagement)
                <li class="nav-item dropdown child-dropdown">
                    <a class="nav-link dropdown-toggle @if ($activeCaseManagement) show @endif" href="#" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside">Case Management</a>
                    <ul class="dropdown-menu @if ($activeCaseManagement) show @endif">
                        <li><a class="dropdown-item" href="{{ route('cases.index') }}">Policies Overview</a></li>
                        <li><a class="dropdown-item" href="{{ route('cases.create', encrypt($caseId)) }}"> Add New Case </a></li>
                    </ul>
                </li>
                @endif


                @if ($canViewUserManagement)
                <li class="nav-item dropdown child-dropdown">
                    <a class="nav-link dropdown-toggle @if ($activeUserManagement) show @endif" href="#" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside">Users Management</a>
                    <ul class="dropdown-menu @if ($activeUserManagement) show @endif">
                        <li><a class="dropdown-item" href="{{ route('users.index') }}">Users</a></li>
                        <li><a class="dropdown-item" href="{{ route('roles.index') }}">Roles</a></li>
                    </ul>
                </li>
                @endif


                @if ($canViewCustomerManagement)
                <li class="nav-item dropdown child-dropdown">
                    <a class="nav-link dropdown-toggle @if ($activeCustomerManagement) show @endif" href="#" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside">External Users Management</a>
                    <ul class="dropdown-menu @if ($activeCustomerManagement) show @endif">
                        <li><a class="dropdown-item" href="{{ route('introducers.index') }}">Introducers</a></li>
                        <li><a class="dropdown-item" href="{{ route('introducers.create') }}"> Add New </a></li>
                    </ul>
                </li>
                @endif


                <li class="nav-item dropdown child-dropdown">
                    <a class="nav-link dropdown-toggle @if ($activeReports) show @endif" href="#" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside">Reports</a>
                    <ul class="dropdown-menu @if ($activeReports) show @endif">
                        <li><a class="dropdown-item" href="{{ route('reports.policies-by-status') }}">Policies by Status</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.new-policies') }}">New Policies Issued</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.top-introducers') }}">Top Introducers</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.status-report') }}">Status Report</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.missing-expired-documents') }}">Missing/Expired Documents</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link @if ($segment == 'mailbox') active @endif" href="{{ route('mailbox.index') }}">
                        Mail Box
                        <span id="unread-count" class="badge bg-danger ms-2" style="display: none;">0</span>
                    </a>
                </li>


            </ul>
            <div class="h-login-logout">
                <ul>
                    <li>
                        <form action="{{ route('logout') }}" method="POST"> @csrf
                            <button type="submit" style="border: none;background:transparent;">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" stroke="#837E7E"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.71533 19.4612L0.766574 19.4612L0.766574 0.871826L3.71533 0.871826"
                                        stroke-width="1.5" stroke-miterlimit="10" stroke-linejoin="round" />
                                    <path d="M13.5438 16.0369L19.4413 10.1665L13.5438 4.29622" stroke-width="1.5"
                                        stroke-miterlimit="10" stroke-linejoin="round" />
                                    <path d="M19.4414 10.1665L4.20612 10.1665" stroke-width="1.5"
                                        stroke-miterlimit="10" stroke-linejoin="round" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</aside>
