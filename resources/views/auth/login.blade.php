<!DOCTYPE html>

<html lang="en" class="light-style" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('theme/assets') }}/"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ appName() }} | {{ holderName() }}</title>

    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="{{ asset('theme/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/libs/typeahead-js/typeahead.css') }}" />

    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('theme/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('theme/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('theme/assets/js/config.js') }}"></script>
</head>

<body>
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('theme/assets/img/illustrations/auth-login-illustration-light.png') }}"
                        alt="auth-login-cover" class="img-fluid my-5 auth-illustration"
                        data-app-light-img="illustrations/auth-login-illustration-light.png"
                        data-app-dark-img="illustrations/auth-login-illustration-dark.png" />

                    <img src="{{ asset('theme/assets/img/illustrations/bg-shape-image-light.png') }}"
                        alt="auth-login-cover" class="platform-bg"
                        data-app-light-img="illustrations/bg-shape-image-light.png"
                        data-app-dark-img="illustrations/bg-shape-image-dark.png" />
                </div>
            </div>

            <div class=" d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <h3 class="mb-1 fw-bold">Selamat Datang! 👋</h3>
                    <p class="mb-4">Silakan masuk ke akun Anda untuk memulai.</p>

                    @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center alert-dismissible" role="alert">
                        <span class="alert-icon text-danger me-2">
                            <i class="ti ti-ban ti-xs"></i>
                        </span>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email or Username</label>
                            <input type="text" class="form-control @error('email_or_username') is-invalid @enderror"
                                id="email" name="email_or_username" value="{{ old('email_or_username') }}"
                                placeholder="Email or Username" required autofocus />

                            @error('email_or_username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="Enter your password" aria-describedby="password" required
                                    autocomplete="current-password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember-me" {{
                                    old('remember') ? 'checked' : '' }} />
                                <label class="form-check-label" for="remember-me"> Remember Me </label>
                            </div>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">
                                <small>Lupa Password?</small>
                            </a>
                            @endif
                        </div>
                        <button class="btn btn-primary d-grid w-100">Sign in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('theme/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('theme/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('theme/assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('theme/assets/js/main.js') }}"></script>

</body>

</html>