@php
    $setting = App\Models\SiteSetting::first();
    $blog = App\Models\BlogPost::latest()->limit(2)->get();
@endphp
<footer class="main-footer">
    <div class="footer-top bg-color-2">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                    <div class="footer-widget about-widget">
                        <div class="widget-title">
                            <h3>About</h3>
                        </div>
                        <div class="text">
                            <p>Your trusted real estate partner specializing in residential, commercial, and investment
                                properties. Our expert team provides personalized service for a seamless buying,
                                selling, or leasing experience.Discover your dream property with us today!</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                    <div class="footer-widget links-widget ml-70">
                        <div class="widget-title">
                            <h3>Services</h3>
                        </div>
                        <div class="widget-content">
                            <ul class="links-list class">
                                <li><a href="{{ url('/') }}">About Us</a></li>
                                <li><a href={{ route('buy.property') }}>Listing</a></li>
                                <li><a href="{{ url('/') }}">How It Works</a></li>
                                <li><a href="{{ url('/') }}">Our Services</a></li>
                                <li><a href="{{ route('blog.list') }}">Our Blog</a></li>
                                <li><a href="{{ url('/') }}">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                    <div class="footer-widget post-widget">
                        <div class="widget-title">
                            <h3>Top News</h3>
                        </div>
                        <div class="post-inner">
                            @foreach ($blog as $item)
                                <div class="post">
                                    <figure class="post-thumb"><a
                                            href="{{ url('blog/details/' . $item->post_slug) }}"><img
                                                src="{{ !empty($item->post_image) ? asset($item->post_image) : url('upload/no_image.jpg') }}"
                                                alt=""></a></figure>
                                    <p class="two-line-text"><a
                                            href="url('blog/details/'.$item->post_slug)">{{ $item->post_title }}</a></p>
                                    <p>{{ $item->created_at->format('M d Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 footer-column">
                    <div class="footer-widget contact-widget">
                        <div class="widget-title">
                            <h3>Contacts</h3>
                        </div>
                        <div class="widget-content">
                            <ul class="info-list clearfix">
                                <li><i class="fas fa-map-marker-alt"></i>{{ $setting->company_address }}</li>
                                <li><i class="fas fa-microphone"></i><a
                                        href="tel:{{ $setting->support_phone }}">{{ $setting->support_phone }}</a>
                                </li>
                                <li><i class="fas fa-envelope"></i><a
                                        href="mailto:{{ $setting->email }}">{{ $setting->email }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="auto-container">
            <div class="inner-box clearfix">

                <figure class="footer-logo" style="text-align: center;">
                    <a href="{{ url('https://empotechbd.com/') }}">
                        <img src="{{ asset('frontend') }}/assets/images/EMPO_TECH_BD_logo_2.png" alt="Empotech BD Logo"
                            style="width: 146px; height: auto; margin-top: 2px;">
                    </a>
                    <figcaption style="display: block; margin-top: -14px; font-size: 12px; color: #b9b8b8;">
                        Developed by Empotech BD
                    </figcaption>
                </figure>

                <div class="copyright pull-left">
                    <p><a href="{{ url('https://empotechbd.com/') }}">{{ $setting->copyright }}</a> &copy; 2023 All
                        Right Reserved</p>
                </div>
                <ul class="footer-nav pull-right clearfix">
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
