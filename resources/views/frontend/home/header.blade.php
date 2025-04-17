@php
    $setting = App\Models\SiteSetting::first(); // Retrieves the first record
@endphp

<header class="main-header">
    <!-- header-top -->
    <div class="header-top">
        <div class="top-inner clearfix">
            <div class="left-column pull-left">
                <ul class="info clearfix">
                    <li><i class="far fa-map-marker-alt"></i>{{ $setting->company_address }}</li>
                    <li><i class="far fa-clock"></i>Sun - Thu 10.00 - 7.00</li>
                    <li><i class="far fa-phone"></i><a
                            href="tel:{{ $setting->support_phone }}">{{ $setting->support_phone }}</a></li>
                </ul>
            </div>
            <div class="right-column pull-right">
                <ul class="social-links clearfix">
                    <li><a href="{{ $setting->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                    <li><a href="{{ $setting->twitter }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                </ul>

                {{-- Laravel default login logout method --}}
                @auth
                    <div class="sign-box ">
                        @if (auth()->user() && auth()->user()->hasRole('user'))
                        <a href="{{ route('dashboard') }}" target="_blank"><i class="fas fa-user"></i>Dashboard</a>
                        <a href="{{ route('user.logout') }}"><i class="fas fa-user"></i>Logout</a>
                        @else
                        <a href="{{ route('admin.dashboard') }}" target="_blank"><i class="fas fa-user"></i>Dashboard</a>
                        <a href="{{ route('admin.logout') }}"><i class="fas fa-user"></i>Logout</a>
                        @endif
                    </div>
                @else
                    <div class="sign-box">
                        <a href="{{ route('login') }}"><i class="fas fa-user"></i>Sign In</a>
                    </div>
                @endauth

            </div>
        </div>
    </div>

    <!-- header-lower -->
    <div class="header-lower">
        <div class="outer-box">
            <div class="main-box">
                <div class="logo-box">
                    <figure class="logo"><a href="{{ url('/') }}"><img src="{{ asset($setting->logo) }}"
                                alt=""></a>
                    </figure>
                </div>
                <div class="menu-area clearfix">
                    <!--Mobile Navigation Toggler-->
                    <div class="mobile-nav-toggler">
                        <i class="icon-bar"></i>
                        <i class="icon-bar"></i>
                        <i class="icon-bar"></i>
                    </div>
                    <nav class="main-menu navbar-expand-md navbar-light">
                        <div class="collapse navbar-collapse show clearfix" id="navbarSupportedContent">
                            <ul class="navigation clearfix">
                                <li><a href="{{ url('/') }}"><span>Home</span></a> </li>
                                <li><a href="{{ url('/') }}"><span>About Us </span></a> </li>

                                <li class="dropdown"><a href="#"><span>Property</span></a>
                                    <ul>
                                        <li><a href="{{ route('rent.property') }}">Rent Property</a></li>
                                        <li><a href="{{ route('buy.property') }}">Buy Property </a></li>

                                    </ul>
                                </li>

                                <li><a href="{{ url('/') }}"><span>Agent</span></a> </li>
                                <li><a href="{{ route('blog.list') }}"><span>Blog</span></a> </li>
                                <li><a href="{{ url('/') }}"><span>Contact</span></a> </li>

                                <li>
                                    <a href="{{ route('agent.login') }}" class="btn btn-success"><span>+</span>Add
                                        Listing</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!--sticky Header-->
    <div class="sticky-header">
        <div class="outer-box">
            <div class="main-box">
                <div class="logo-box">
                    <figure class="logo"><a href="{{ url('/') }}"><img src="{{ asset($setting->logo) }}"
                                alt=""></a>
                    </figure>
                </div>
                <div class="menu-area clearfix">
                    <nav class="main-menu clearfix">
                        <!--Keep This Empty / Menu will come through Javascript-->
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
