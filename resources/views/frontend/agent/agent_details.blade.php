@extends('frontend.frontend_dashboard')
@section('main')
@section('title')
    {{ $agent->name }} | EMPO RealEstate
@endsection
<!--Page Title-->
<section class="page-title-two bg-color-1 centred">
    <div class="pattern-layer">
        <div class="pattern-1" style="background-image: url({{ asset('frontend') }}/assets/images/shape/shape-9.png);">
        </div>
        <div class="pattern-2" style="background-image: url({{ asset('frontend') }}/assets/images/shape/shape-10.png);">
        </div>
    </div>
    <div class="auto-container">
        <div class="content-box clearfix">
            <h1>{{ $agent->name }} Details</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="">Home</a></li>
                <li>{{ $agent->name }} Details</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->


<!-- agent-details -->
<section class="agent-details">
    <div class="auto-container">
        <div class="agent-details-content">
            <div class="agents-block-one">
                <div class="inner-box mr-0">
                    <figure class="image-box"><img
                            src="{{ !empty($agent->photo) ? url('upload/agent_images/' . $agent->photo) : url('upload/no_image.jpg') }}"
                            alt="" style="width:270px; height:330px;"></figure>
                    <div class="content-box">
                        <div class="upper clearfix">
                            <div class="title-inner pull-left">
                                <h4>{{ $agent->name }}</h4>
                                <span class="designation">{{ $agent->username }}</span>
                            </div>
                            <ul class="social-list pull-right clearfix">
                                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                            </ul>
                        </div>
                        <div class="text">
                            <p>{{ $agent->description }}</p>
                        </div>
                        <ul class="info clearfix mr-0">
                            <li><i class="fab fa fa-envelope"></i><a
                                    href="mailto:{{ $agent->email }}">{{ $agent->email }}</a></li>
                            <li><i class="fab fa fa-phone"></i><a
                                    href="tel:{{ $agent->phone }}">{{ $agent->phone }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- agent-details end -->


<!-- agents-page-section -->
<section class="agents-page-section agent-details-page">
    <div class="auto-container">
        <div class="row clearfix">

            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="agents-content-side tabs-box">
                    <div class="group-title">
                        <h3>Listing By {{ $agent->name }}</h3>
                    </div>

                    <div class="tabs-content">
                        <div class="tab active-tab" id="tab-1">
                            <div class="wrapper list">
                                <div class="deals-list-content list-item">

                                    @foreach ($property as $item)
                                        <div class="deals-block-one">
                                            <div class="inner-box">
                                                <div class="image-box">
                                                    <figure class="image"><img
                                                            src="{{ asset($item->property_thambnail) }}" alt=""
                                                            style="width:300px; height:350px;"></figure>
                                                    <div class="batch"><i class="icon-11"></i></div>
                                                    @if ($item->featured == 1)
                                                        <span class="category">Featured</span>
                                                    @else
                                                        <span class="category">New</span>
                                                    @endif
                                                    <div class="buy-btn"><a
                                                            href="{{ url('property/details/' . $item->id . '/' . $item->property_slug) }}"">For
                                                            {{ $item->property_status }}</a></div>
                                                </div>
                                                <div class="lower-content">
                                                    <div class="title-text">
                                                        <h4><a
                                                                href="{{ url('property/details/' . $item->id . '/' . $item->property_slug) }}">{{ $item->property_name }}</a>
                                                        </h4>
                                                    </div>
                                                    <div class="price-box clearfix">
                                                        <div class="price-info pull-left">
                                                            <h6>Start From</h6>
                                                            <h4>${{ $item->lowest_price }}</h4>
                                                        </div>

                                                        @if ($item->agent_id == null)
                                                            <div class="author-box pull-right">
                                                                <figure class="author-thumb">
                                                                    <img src="{{ url('upload/salman.jpg') }}"
                                                                        alt="">
                                                                    <span>Admin</span>
                                                                </figure>
                                                            </div>
                                                        @else
                                                            <div class="author-box pull-right">
                                                                <figure class="author-thumb">
                                                                    <img src="{{ !empty($item->user->photo) ? url('upload/agent_images/' . $item->user->photo) : url('upload/no_image.jpg') }}"
                                                                        alt="">
                                                                    <span>{{ $item->user->name }}</span>
                                                                </figure>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <p>{{ $item->short_descp }}</p>
                                                    <ul class="more-details clearfix">
                                                        <li><i class="icon-14"></i>{{ $item->bedrooms }} Beds</li>
                                                        <li><i class="icon-15"></i>{{ $item->bathrooms }} Baths</li>
                                                        <li><i class="icon-16"></i>{{ $item->property_size }} Sq Ft
                                                        </li>
                                                    </ul>
                                                    <div class="other-info-box clearfix">
                                                        <div class="btn-box pull-left"><a
                                                                href="{{ url('property/details/' . $item->id . '/' . $item->property_slug) }}"
                                                                class="theme-btn btn-two">See Details</a></div>
                                                        <ul class="other-option pull-right clearfix">
                                                            <li><a href="property-details.html"><i
                                                                        class="icon-12"></i></a>
                                                            </li>
                                                            <li><a href="property-details.html"><i
                                                                        class="icon-13"></i></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>


                                <div class="deals-grid-content">
                                    <div class="row clearfix">
                                        <div class="col-lg-6 col-md-6 col-sm-12 feature-block">
                                            <div class="feature-block-one">
                                                <div class="inner-box">
                                                    <div class="image-box">
                                                        <figure class="image"><img
                                                                src="assets/images/feature/feature-1.jpg"
                                                                alt=""></figure>
                                                        <div class="batch"><i class="icon-11"></i></div>
                                                        <span class="category">Featured</span>
                                                    </div>
                                                    <div class="lower-content">
                                                        <div class="author-info clearfix">
                                                            <div class="author pull-left">
                                                                <figure class="author-thumb"><img
                                                                        src="assets/images/feature/author-1.jpg"
                                                                        alt=""></figure>
                                                                <h6>Michael Bean</h6>
                                                            </div>
                                                            <div class="buy-btn pull-right"><a
                                                                    href="property-details.html">For Buy</a></div>
                                                        </div>
                                                        <div class="title-text">
                                                            <h4><a href="property-details.html">Luxury Villa With
                                                                    Pool</a></h4>
                                                        </div>
                                                        <div class="price-box clearfix">
                                                            <div class="price-info pull-left">
                                                                <h6>Start From</h6>
                                                                <h4>$30,000.00</h4>
                                                            </div>
                                                            <ul class="other-option pull-right clearfix">
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-12"></i></a></li>
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-13"></i></a></li>
                                                            </ul>
                                                        </div>
                                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing sed.</p>
                                                        <ul class="more-details clearfix">
                                                            <li><i class="icon-14"></i>3 Beds</li>
                                                            <li><i class="icon-15"></i>2 Baths</li>
                                                            <li><i class="icon-16"></i>600 Sq Ft</li>
                                                        </ul>
                                                        <div class="btn-box"><a href="property-details.html"
                                                                class="theme-btn btn-two">See Details</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 feature-block">
                                            <div class="feature-block-one">
                                                <div class="inner-box">
                                                    <div class="image-box">
                                                        <figure class="image"><img
                                                                src="assets/images/feature/feature-2.jpg"
                                                                alt=""></figure>
                                                        <div class="batch"><i class="icon-11"></i></div>
                                                        <span class="category">Featured</span>
                                                    </div>
                                                    <div class="lower-content">
                                                        <div class="author-info clearfix">
                                                            <div class="author pull-left">
                                                                <figure class="author-thumb"><img
                                                                        src="assets/images/feature/author-2.jpg"
                                                                        alt=""></figure>
                                                                <h6>Robert Niro</h6>
                                                            </div>
                                                            <div class="buy-btn pull-right"><a
                                                                    href="property-details.html">For Rent</a></div>
                                                        </div>
                                                        <div class="title-text">
                                                            <h4><a href="property-details.html">Contemporary
                                                                    Apartment</a></h4>
                                                        </div>
                                                        <div class="price-box clearfix">
                                                            <div class="price-info pull-left">
                                                                <h6>Start From</h6>
                                                                <h4>$45,000.00</h4>
                                                            </div>
                                                            <ul class="other-option pull-right clearfix">
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-12"></i></a></li>
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-13"></i></a></li>
                                                            </ul>
                                                        </div>
                                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing sed.</p>
                                                        <ul class="more-details clearfix">
                                                            <li><i class="icon-14"></i>3 Beds</li>
                                                            <li><i class="icon-15"></i>2 Baths</li>
                                                            <li><i class="icon-16"></i>600 Sq Ft</li>
                                                        </ul>
                                                        <div class="btn-box"><a href="property-details.html"
                                                                class="theme-btn btn-two">See Details</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 feature-block">
                                            <div class="feature-block-one">
                                                <div class="inner-box">
                                                    <div class="image-box">
                                                        <figure class="image"><img
                                                                src="assets/images/feature/feature-3.jpg"
                                                                alt=""></figure>
                                                        <div class="batch"><i class="icon-11"></i></div>
                                                        <span class="category">Featured</span>
                                                    </div>
                                                    <div class="lower-content">
                                                        <div class="author-info clearfix">
                                                            <div class="author pull-left">
                                                                <figure class="author-thumb"><img
                                                                        src="assets/images/feature/author-3.jpg"
                                                                        alt=""></figure>
                                                                <h6>Keira Mel</h6>
                                                            </div>
                                                            <div class="buy-btn pull-right"><a
                                                                    href="property-details.html">Sold Out</a></div>
                                                        </div>
                                                        <div class="title-text">
                                                            <h4><a href="property-details.html">Villa on Grand
                                                                    Avenue</a></h4>
                                                        </div>
                                                        <div class="price-box clearfix">
                                                            <div class="price-info pull-left">
                                                                <h6>Start From</h6>
                                                                <h4>$63,000.00</h4>
                                                            </div>
                                                            <ul class="other-option pull-right clearfix">
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-12"></i></a></li>
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-13"></i></a></li>
                                                            </ul>
                                                        </div>
                                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing sed.</p>
                                                        <ul class="more-details clearfix">
                                                            <li><i class="icon-14"></i>3 Beds</li>
                                                            <li><i class="icon-15"></i>2 Baths</li>
                                                            <li><i class="icon-16"></i>600 Sq Ft</li>
                                                        </ul>
                                                        <div class="btn-box"><a href="property-details.html"
                                                                class="theme-btn btn-two">See Details</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 feature-block">
                                            <div class="feature-block-one">
                                                <div class="inner-box">
                                                    <div class="image-box">
                                                        <figure class="image"><img
                                                                src="assets/images/feature/feature-4.jpg"
                                                                alt=""></figure>
                                                        <div class="batch"><i class="icon-11"></i></div>
                                                        <span class="category">Featured</span>
                                                    </div>
                                                    <div class="lower-content">
                                                        <div class="author-info clearfix">
                                                            <div class="author pull-left">
                                                                <figure class="author-thumb"><img
                                                                        src="assets/images/feature/author-1.jpg"
                                                                        alt=""></figure>
                                                                <h6>Michael Bean</h6>
                                                            </div>
                                                            <div class="buy-btn pull-right"><a
                                                                    href="property-details.html">For Buy</a></div>
                                                        </div>
                                                        <div class="title-text">
                                                            <h4><a href="property-details.html">Home in Merrick Way</a>
                                                            </h4>
                                                        </div>
                                                        <div class="price-box clearfix">
                                                            <div class="price-info pull-left">
                                                                <h6>Start From</h6>
                                                                <h4>$30,000.00</h4>
                                                            </div>
                                                            <ul class="other-option pull-right clearfix">
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-12"></i></a></li>
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-13"></i></a></li>
                                                            </ul>
                                                        </div>
                                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing sed.</p>
                                                        <ul class="more-details clearfix">
                                                            <li><i class="icon-14"></i>3 Beds</li>
                                                            <li><i class="icon-15"></i>2 Baths</li>
                                                            <li><i class="icon-16"></i>600 Sq Ft</li>
                                                        </ul>
                                                        <div class="btn-box"><a href="property-details.html"
                                                                class="theme-btn btn-two">See Details</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 feature-block">
                                            <div class="feature-block-one">
                                                <div class="inner-box">
                                                    <div class="image-box">
                                                        <figure class="image"><img
                                                                src="assets/images/feature/feature-5.jpg"
                                                                alt=""></figure>
                                                        <div class="batch"><i class="icon-11"></i></div>
                                                        <span class="category">Featured</span>
                                                    </div>
                                                    <div class="lower-content">
                                                        <div class="author-info clearfix">
                                                            <div class="author pull-left">
                                                                <figure class="author-thumb"><img
                                                                        src="assets/images/feature/author-2.jpg"
                                                                        alt=""></figure>
                                                                <h6>Robert Niro</h6>
                                                            </div>
                                                            <div class="buy-btn pull-right"><a
                                                                    href="property-details.html">For Rent</a></div>
                                                        </div>
                                                        <div class="title-text">
                                                            <h4><a href="property-details.html">Apartment in
                                                                    Glasgow</a></h4>
                                                        </div>
                                                        <div class="price-box clearfix">
                                                            <div class="price-info pull-left">
                                                                <h6>Start From</h6>
                                                                <h4>$45,000.00</h4>
                                                            </div>
                                                            <ul class="other-option pull-right clearfix">
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-12"></i></a></li>
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-13"></i></a></li>
                                                            </ul>
                                                        </div>
                                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing sed.</p>
                                                        <ul class="more-details clearfix">
                                                            <li><i class="icon-14"></i>3 Beds</li>
                                                            <li><i class="icon-15"></i>2 Baths</li>
                                                            <li><i class="icon-16"></i>600 Sq Ft</li>
                                                        </ul>
                                                        <div class="btn-box"><a href="property-details.html"
                                                                class="theme-btn btn-two">See Details</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 feature-block">
                                            <div class="feature-block-one">
                                                <div class="inner-box">
                                                    <div class="image-box">
                                                        <figure class="image"><img
                                                                src="assets/images/feature/feature-6.jpg"
                                                                alt=""></figure>
                                                        <div class="batch"><i class="icon-11"></i></div>
                                                        <span class="category">Featured</span>
                                                    </div>
                                                    <div class="lower-content">
                                                        <div class="author-info clearfix">
                                                            <div class="author pull-left">
                                                                <figure class="author-thumb"><img
                                                                        src="assets/images/feature/author-3.jpg"
                                                                        alt=""></figure>
                                                                <h6>Keira Mel</h6>
                                                            </div>
                                                            <div class="buy-btn pull-right"><a
                                                                    href="property-details.html">Sold Out</a></div>
                                                        </div>
                                                        <div class="title-text">
                                                            <h4><a href="property-details.html">Family Home For
                                                                    Sale</a></h4>
                                                        </div>
                                                        <div class="price-box clearfix">
                                                            <div class="price-info pull-left">
                                                                <h6>Start From</h6>
                                                                <h4>$63,000.00</h4>
                                                            </div>
                                                            <ul class="other-option pull-right clearfix">
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-12"></i></a></li>
                                                                <li><a href="property-details.html"><i
                                                                            class="icon-13"></i></a></li>
                                                            </ul>
                                                        </div>
                                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing sed.</p>
                                                        <ul class="more-details clearfix">
                                                            <li><i class="icon-14"></i>3 Beds</li>
                                                            <li><i class="icon-15"></i>2 Baths</li>
                                                            <li><i class="icon-16"></i>600 Sq Ft</li>
                                                        </ul>
                                                        <div class="btn-box"><a href="property-details.html"
                                                                class="theme-btn btn-two">See Details</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                <div class="default-sidebar agent-sidebar">
                    <div class="agents-contact sidebar-widget">
                        <div class="widget-title">
                            <h5>Contact To {{ $agent->name }}</h5>
                        </div>

                        <div class="form-inner">
                            @auth

                                @php
                                    $id = Auth::user()->id;
                                    $userData = App\Models\User::find($id);
                                @endphp

                                <form action="{{ route('agent.details.message') }}" method="post" class="default-form">
                                    @csrf

                                    <input type="hidden" name="agent_id" value="{{ $agent->id }}">

                                    <div class="form-group">
                                        <input type="text" name="msg_name" placeholder="Your name"
                                            value="{{ $userData->name }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="msg_email" placeholder="Your Email"
                                            value="{{ $userData->email }}">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="msg_phone" placeholder="Phone"
                                            value="{{ $userData->phone }}">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="message" placeholder="Message"></textarea>
                                    </div>
                                    <div class="form-group message-btn">
                                        <button type="submit" class="theme-btn btn-one">Send Message</button>
                                    </div>
                                </form>
                            @else
                                <form action="{{ route('agent.details.message') }}" method="post" class="default-form">
                                    @csrf

                                    <input type="hidden" name="agent_id" value="{{ $agent->id }}">


                                    <div class="form-group">
                                        <input type="text" name="msg_name" placeholder="Your name" required="">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="msg_email" placeholder="Your Email" required="">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="msg_phone" placeholder="Phone" required="">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="message" placeholder="Message"></textarea>
                                    </div>
                                    <div class="form-group message-btn">
                                        <button type="submit" class="theme-btn btn-one">Send Message</button>
                                    </div>
                                </form>

                            @endauth



                        </div>
                    </div>

                    <div class="category-widget sidebar-widget">
                        <div class="widget-title">
                            <h5>Status Of Property</h5>
                        </div>
                        <ul class="category-list clearfix">
                            <li><a href="{{ route('rent.property') }}">For Rent
                                    <span>({{ count($rentproperty) }})</span></a></li>
                            <li><a href="{{ route('buy.property') }}">For Buy
                                    <span>({{ count($buyproperty) }})</span></a></li>
                        </ul>
                    </div>

                    <div class="featured-widget sidebar-widget">
                        <div class="widget-title">
                            <h5>Featured Properties</h5>
                        </div>
                        <div class="single-item-carousel owl-carousel owl-theme owl-nav-none dots-style-one">

                            @foreach ($featured as $feat)
                                <div class="feature-block-one">
                                    <div class="inner-box">
                                        <div class="image-box">
                                            <figure class="image"><img src="{{ asset($feat->property_thambnail) }}"
                                                    alt="" style="width:370px; height:250px;"></figure>
                                            <div class="batch"><i class="icon-11"></i></div>
                                            <span class="category">Featured</span>
                                        </div>
                                        <div class="lower-content">
                                            <div class="title-text">
                                                <h4><a
                                                        href="{{ url('property/details/' . $feat->id . '/' . $feat->property_slug) }}">{{ $feat->property_name }}</a>
                                                </h4>
                                            </div>
                                            <div class="price-box clearfix">
                                                <div class="price-info">
                                                    <h6>Start From</h6>
                                                    <h4>${{ $feat->lowest_price }}</h4>
                                                </div>
                                            </div>
                                            <p>{{ $feat->short_descp }}</p>
                                            <div class="btn-box"><a
                                                    href="{{ url('property/details/' . $feat->id . '/' . $feat->property_slug) }}"
                                                    class="theme-btn btn-two">See Details</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- agents-page-section end -->
@endsection
