<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>
<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- Topbar header - style you can find in pages.scss -->
<!-- ============================================================== -->
<header class="topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin6">
            <!-- ============================================================== -->
            <!-- Logo -->
            <!-- ============================================================== -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <!-- Logo icon -->
                <b class="logo-icon text-center">
                    <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                    <!-- Dark Logo icon -->
                    <img src={{ asset('public/images/UFUND.png') }} alt="homepage" class="dark-logo ufound-Navbar-Logo" />

                </b>
            </a>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <a class="nav-toggler waves-effect waves-light text-dark d-block d-md-none" href="javascript:void(0)"><i
                    class="ti-menu ti-close"></i></a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">

            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->

            <div class="container text-center text-white">
                <h2><i class="fas fa-envelope" aria-hidden="true"></i> SMS Dashboard</h2>
            </div>
        </div>

    </nav>
</header>


<aside class="left-sidebar" data-sidebarbg="skin6">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                {{-- <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" aria-expanded="false" href="{{ url('/') }}"
                            id="test_tag_a">
                            <i class="me-3 far fa-clock fa-fw" aria-hidden="true"></i>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li> --}}
                <li class="sidebar-item">
                    <a class="{{ Request::is('/') ? 'sidebar-link waves-effect waves-dark sidebar-link active' : 'sidebar-link waves-effect waves-dark sidebar-link' }}"
                        aria-expanded="false" href="{{ url('/') }}">
                        <i class="me-3 far fa-clock fa-fw" aria-hidden="true"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" aria-expanded="false"
                        href="{{ url('/Detail_Send_SMS_bill') }}">
                        <i class="me-3 fa fa-table" aria-hidden="true"></i>
                        <span class="hide-menu">Detail Send SMS</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" aria-expanded="false"
                        href="{{ url('/profile') }}">
                        <i class="me-3 fa fa-user" aria-hidden="true"></i>
                        <span class="hide-menu">Profile</span>
                    </a>
                </li>
                {{-- <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        aria-expanded="false"><i class="me-3 fa fa-font" aria-hidden="true"></i><span
                            class="hide-menu">Icon</span></a></li>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" aria-expanded="false"  href="{{ url('/Map') }}">
                        <i class="me-3 fa fa-globe" aria-hidden="true"></i>
                        <span class="hide-menu">Google Map</span>
                    </a>
                </li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        aria-expanded="false"><i class="me-3 fa fa-columns" aria-hidden="true"></i><span
                            class="hide-menu">Blank</span></a></li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('/page_404') }}" aria-expanded="false"><i class="me-3 fa fa-info-circle"
                            aria-hidden="true"></i><span class="hide-menu">Error 404</span></a></li> --}}
                <li class="text-center p-20 upgrade-btn">
                    <a href="{{ url('/logout') }}" class="btn btn-danger text-white mt-4">Logout</a>
                </li>
            </ul>

        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>

<script>
    $('#test_tag_a').on('click', function() {
        // alert('gh')
        // $(".preloader").css('display', 'block');
    })
</script>
