<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  	<link rel="icon" type="image/x-icon" href="{{ Helper::favicon() }}">

	<title> {{ Helper::title() }} - {{ isset($title) ? $title : 'Home' }} </title>

	<style>

	</style>
    <!-- Bootstrap -->
    <link href="{{ asset('assets/new-css/bootstrap.min.css') }}?time={{ time() }}" rel="stylesheet">
    <!--=== Add By Designer ===-->
    <link href="{{ asset('assets/new-css/style.css') }}?time={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/fonts.css') }}?time={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/new-css/responsive.css') }}?time={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-icons.css') }}?time={{ time() }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
	@if(isset($datatable))
		<link rel="stylesheet" href="{{ asset('assets/css/datatable.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">
	@endif

	@if(isset($select2))
		<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
	@endif
		
	@if(isset($datepicker))
		<link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
	@endif

	@if(isset($editor))
		<link rel="stylesheet" href="{{ asset('assets/css/ckeditor.min.css') }}">
	@endif

	<link rel="stylesheet" href="{{ asset('assets/css/swal.min.css') }}">
	@stack('css') 
    <style>
        .select2-container--classic .select2-selection--single .select2-selection__arrow {
            background: url("{{ asset('assets/images/arrow-select.svg') }}") !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            background-color: #fff !important
        }
    </style>
</head>

<body>

    <div class="body-wrapper">

	@include('layouts.sidebar')


        <div class="page-wrapper">

            <!-- header start -->
            <header class="header">
                <div class="container-fluid">
                    <div class="header-main">
                        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbar-content" aria-expanded="true">
                            <div class="hamburger-toggle">
                                <div class="hamburger">
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </button>
                        <form class="search-form" role="search">
                            <input class="form-control" type="search" placeholder="Search" aria-label="Search" />
                            <div class="search-icon">
                                <img src="{{ asset('assets/images/svg/search.svg') }}" alt="search" class="img-fluid">
                            </div>
                        </form>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="profile-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset('assets/images/svg/round-arrow.svg') }}" alt="round-arrow" class="img-fluid">
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <!-- header end -->

            <div class="content-wrapper">
                <!-- content start -->
                <div class="content">
                    <div class="@if(in_array(Request::route()->getName(), ['cases.index', 'cases.create', 'introducers.index', 'users.index'])) case-management @else cards-wrapper @endif">
                        <!-- <div class="@if(in_array(Request::route()->getName(), ['cases.create', 'cases.edit', 'cases.view'])) @else container-fluid @endif"> -->
                        <div class="container-fluid">
							@yield('content')
                        </div>
                    </div>
                    
                    {{-- <div class="logo-banner">
                        <img src="{{ asset('assets/images/svg/favicon.svg') }}" alt="logo" class="img-fluid">
                    </div> --}}
                </div>
                <!-- content end -->

                <!-- footer start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="footer-main">
                            <p>
                                <script>document.write(new Date().getFullYear());</script>. All rights reserved
                            </p>
                        </div>
                    </div>
                </footer>
                <!-- footer end -->
            </div>
        </div>

    </div>	
	
</body>

<script src="{{ asset('assets/js/app.js') }}"></script>
@if(isset($datatable))
	<script src="{{ asset('assets/js/datatable.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
@endif
	
@if(isset($select2))
	<script src="{{ asset('assets/js/select2.min.js') }}"></script>
@endif
	
@if(isset($datepicker))
	<script src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>
	<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
@endif

@if(isset($editor))
	<script src="{{ asset('assets/js/ckeditor.min.js') }}"></script>
@endif
	
<script src="{{ asset('assets/js/swal.min.js') }}"></script>
@include('layouts.script')

@stack('js')
</html>