<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Techesthete</title>
    <!-- base:css -->
    <link rel="stylesheet" href="{{ asset('template/vendors/typicons.font/font/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('template/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('template/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('template/images/favicon.png') }}" />

    <!-- Toaster Link -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .brand-logo {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img style="justify-content: center"
                                    src="{{ asset('template/images/techesthete-logo.png') }}" alt="logo">
                            </div>
                            <div style="text-align: center">
                                <h4>Techesthete</h4>
                                <h6 class="font-weight-light">Sign in to continue.</h6>
                            </div>
                            <form class="pt-3" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" :value="old('email')"
                                        class="form-control form-control-lg" id="email" placeholder="Username">
                                </div>
                                <x-input-error :messages="$errors->get('email')" style="color: red" class="mt-2" />
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-lg"
                                        id="password" placeholder="Password">
                                </div>
                                <x-input-error :messages="$errors->get('password')" style="color: red" class="mt-2" />
                                <div class="mt-3">
                                    <button
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN
                                        IN</button>
                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input">
                                            Keep me signed in
                                        </label>
                                    </div>
                                    <a href="#" class="auth-link text-black">Forgot password?</a>
                                </div>
                                <div class="mb-2">
                                    {{-- <button type="button" class="btn btn-block btn-facebook auth-form-btn">
                                        <i class="typcn typcn-social-facebook-circular mr-2"></i>Connect using facebook
                                    </button> --}}
                                    <a href="{{route('facebook.login')}}" class="btn btn-block btn-facebook auth-form-btn">
                                        <i class="typcn typcn-social-facebook-circular mr-2"></i> Sign in using facebook+
                                    </a>
                                    <a href="{{route('google.login')}}" class="btn btn-block btn-danger">
                                        <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                                    </a>
                                </div>
                                <div class="text-center mt-4 font-weight-light">
                                    Don't have an account? <a href="{{ route('register') }}"
                                        class="text-primary">Create</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- base:js -->
    <script src="{{ asset('template/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- inject:js -->
    <script src="{{ asset('template/js/off-canvas.js') }}"></script>
    <script src="{{ asset('template/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('template/js/template.js') }}"></script>
    <script src="{{ asset('template/js/settings.js') }}"></script>
    <script src="{{ asset('template/js/todolist.js') }}"></script>
    <!-- endinject -->


    <!-- Toaster script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    @if (Session::has('success'))
        <script>
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ session('success') }}")
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('error') }}")
        </script>
    @endif
</body>

</html>
