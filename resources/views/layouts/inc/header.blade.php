<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-end mb-0">

        <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="{{ asset('template/assets/images/users/user.png') }}" alt="user-image" class="rounded-circle">
                <span class="pro-user-name ms-1">
                {{ Auth::user()->nama }} <i class="mdi mdi-chevron-down"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Selamat Datang !</h6>
                </div>
                <div class="dropdown-divider"></div>
                <!-- ubah password-->
                <a href="{{ route('ubah_password', Crypt::encryptString(Auth::user()->id)) }}" class="dropdown-item notify-item">
                    <i class="fa fa-key"></i>
                     <span>Ubah Password</span></a>
                <div class="dropdown-divider"></div>
                <!-- item-->
                <a href="{{ route('logout') }}" class="dropdown-item notify-item">
                    <i class="fe-log-out"></i>
                    <span>Logout</span>
                </a>

            </div>
        </li>

    </ul>

    <!-- LOGO -->
    <div class="logo-box">
        <a href="" class="logo logo-dark text-center">
            <span class="logo-sm">
                <img src="{{ asset('template/assets/images/simakda.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('template/assets/images/logo-simakda.png') }}" alt="" height="16">
            </span>
        </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
        <li>
            <button class="button-menu-mobile disable-btn waves-effect">
                <i class="fe-menu"></i>
            </button>
        </li>
    </ul>

    <div class="clearfix"></div>
</div>
