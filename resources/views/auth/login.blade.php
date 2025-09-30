<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title>Geneva International</title>
    <meta name="description" content="" />
    <meta name="title" content="" />
    <meta name="keywords" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/svg/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />

    <!-- Bootstrap -->
    <link href="{{ asset('assets/new-css/bootstrap.min.css') }}?time={{ time() }}" rel="stylesheet">
    <!--=== Add By Designer ===-->
    <link href="{{ asset('assets/new-css/style.css') }}?time={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/fonts.css') }}?time={{ time() }}" rel="stylesheet">
    <link href="{{ asset('assets/new-css/responsive.css') }}?time={{ time() }}" rel="stylesheet">
    <style>
        .login .page-wrapper .content {
            padding-bottom: 20px;
        }
    </style>
</head>


<body>
    <!-- header start -->
    <!-- header end -->

    <div class="body-wrapper login">

        <div class="page-wrapper">
            <!-- <div class="content-wrapper"> -->
                <!-- content start -->
                <div class="content">
                    <div class="login-form">
                        <div class="container-fluid">
                            <div class="login-form-main">
                                <div class="logo">
                                    <img src="{{ asset('assets/images/svg/logo.svg') }}" alt="logo" class="img-fluid">
                                </div>
                                <div class="heading">
                                    <div class="title">
                                        <h1>Case Management Portal</h1>
                                    </div>
                                    <div class="sub-title">
                                        <h2>Login to your account</h2>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('login') }}" id="login-form" class="form"> @csrf
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="InputEmail" placeholder="Email" name="email" aria-describedby="emailHelp" required>
										@if ($errors->has('email'))
											<span class="text-danger d-block mt-2" style="font-size: 12px;">{{ $errors->first('email') }}</span>
										@endif
                                    </div>
                                    <div class="form-group password-field">
                                        <input type="password" class="form-control" id="InputPassword" placeholder="Password" name="password" required>
                                        <div class="password-icon eye-icon">
                                            <img src="{{ asset('assets/images/svg/eye.svg') }}" alt="eye" class="img-fluid">
                                            <img src="{{ asset('assets/images/svg/eye-open.svg') }}" alt="eye" class="img-fluid d-none">
                                        </div>
										@if ($errors->has('password'))
											<span class="text-danger d-block mt-2" style="font-size: 12px;">{{ $errors->first('password') }}</span>
										@elseif(session()->has('error'))
											<span class="text-danger d-block mt-2" style="font-size: 12px;">{{ session()->get('error') }}</span>
										@endif
                                    </div>
                                    <div class="form-check">
                                        <div class="check-container">
                                            <input type="checkbox" class="form-check-input" id="Check">
                                            <label class="form-check-label" for="Check">Remember me</label>
                                        </div>
                                        <div class="forget-passoword">
                                            <p><a href="#">Forgot Password?</a></p>
                                        </div>
                                    </div>
                                    <div class="login-btn">
                                        <button type="submit" class="btn red-btn">Login</button>
                                    </div>
                                    <div class="detail">
                                        <p>Secured by ECC 256 bit ssl encryption.</p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
            <!-- </div> -->
        </div>

    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
	<script src="{{ asset('assets/js/bootstrap.js') }}"></script>
	
    <!-- Bootstrap Bundle with Popper -->
</body>

</html>
<script>
jQuery(document).ready(function($){
    /* password hide-show */
    $(".login-form .login-form-main .form .eye-icon").click(function(){
        $(this).parent().toggleClass("show-pwd");
        $(this).find("img").toggleClass("d-none");
        if($(this).parent().hasClass("show-pwd")){
            $(this).parent().find("input").attr("type", "text");
        } else {
            $(this).parent().find("input").attr("type", "password");
        }
    });
    /* password hide-show */
});
</script>