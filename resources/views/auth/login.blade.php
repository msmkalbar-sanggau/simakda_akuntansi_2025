<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Mirrored from coderthemes.com/adminto/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 27 Apr 2023 09:46:42 GMT -->

<head>
    <meta charset="utf-8" />
    <title>{{ 'Login Simakda Akuntansi' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('template/assets/images/favicon.ico') }}">

    <!-- App css -->

    <link href="{{ asset('template/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- icons -->
    <link href="{{ asset('template/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .authentication-bgs {
            background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
            background-size: 100% auto;
        }

        */
        /* For Desktop View */
        /* 1920 × 1080 */

        @media screen and (min-width: 1500px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        @media screen and (min-width: 1422px) and (max-device-width: 1500px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        @media screen and (min-width: 1366px) and (max-device-width: 1422px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        @media screen and (min-width: 1024px) and (max-device-width: 1366px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For Tablet View */
        @media screen and (min-device-width: 768px) and (max-device-width: 1024px) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For Mobile Portrait View */
        @media screen and (max-device-width: 480px) and (orientation: portrait) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For Mobile Landscape View */
        @media screen and (max-device-width: 640px) and (orientation: landscape) {
            .authentication-bgs {
                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For Mobile Phones Portrait or Landscape View */
        @media screen and (max-device-width: 640px) {
            .authentication-bgs {

                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For iPhone 4 Portrait or Landscape View */
        @media screen and (min-device-width: 320px) and (-webkit-min-device-pixel-ratio: 2) {
            .authentication-bgs {

                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For iPhone 5 Portrait or Landscape View */
        @media (device-height: 568px) and (device-width: 320px) and (-webkit-min-device-pixel-ratio: 2) {
            .authentication-bgs {

                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }

        /* For iPhone 6 and 6 plus Portrait or Landscape View */
        @media (min-device-height: 667px) and (min-device-width: 375px) and (-webkit-min-device-pixel-ratio: 3) {
            .authentication-bgs {

                background-image: url('{{ asset('template/assets/images/bg2.jpg') }}');
                background-size: 100% auto;
            }
        }




        .authentication-bgs .auth-logo .logo {
            margin: 0px auto;
        }
    </style>
</head>

<body class="authentication-bgs">

    <div class="account-pages my-5">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-4">
                    <div class="card">
                        <div class="card-body p-4">

                            <div class="text-center mt-3">
                                <h4 class="text-uppercase mt-0"> SIMAKDA AKUNTANSI</h4>
                                <table width="100%" border="0">
                                    <tr>
                                        <td width="25%" align="center">
                                            <img src="{{ asset('template/assets/images/logo_pemda_hp.png') }}"
                                                alt="" height="100" class="logo logo-dark">
                                        </td>
                                        <td width="75%" align="left">
                                            <br>
                                            <h5 class="text-primary">{{ $daerah->nm_pemda }}</h5>
                                            <p style="font-size:10px" class="text-muted">{{ $daerah->nm_badan }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    {{ $errors->first() }}
                                </div>
                            @endif
                            <form method="POST" action="{{ route('login.index') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Masukkan Username" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Masukkan password" required>
                                </div>

                                <div class="d-grid text-center">
                                    <button class="btn btn-primary" type="submit"> Login </button>
                                </div>
                            </form>

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <!-- Vendor -->
    <script src="{{ asset('template/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/feather-icons/feather.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('template/assets/js/app.min.js') }}"></script>

</body>

<!-- Mirrored from coderthemes.com/adminto/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 27 Apr 2023 09:46:42 GMT -->

</html>
