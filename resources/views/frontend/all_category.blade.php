@extends('frontend.frontend_dashboard')
@section('main')
    <!--Page Title-->
@section('title')
    All Category | EMPO RealEstate
@endsection

<!--Page Title-->
<section class="page-title-two bg-color-1 centred">
    <div class="pattern-layer">
        <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});">
        </div>
        <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});">
        </div>
    </div>
    <div class="auto-container">
        <div class="content-box clearfix">
            <h1>Categories</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>All Categories</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->


<!-- category-section -->
<section class="category-section category-page centred mr-0 pt-120 pb-90">
    <div class="auto-container">
        <div class="inner-container wow slideInLeft animated" data-wow-delay="00ms" data-wow-duration="1500ms">
            <ul class="category-list clearfix">
                @foreach ($categories as $category)
                    <li>
                        @php
                            $total_catgory = App\Models\Property::where('ptype_id', $category->id)->get();
                        @endphp
                        <div class="category-block-one">
                            <div class="inner-box">
                                <div class="icon-box"><i class="{{ $category->type_icon }}"></i></div>
                                <h5><a href="property-details.html">{{ $category->type_name }}</a></h5>
                                <span>{{ count($total_catgory) }}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
<!-- category-section end -->


<!-- cta-section -->
<section class="cta-section alternate-2 centred"
    style="background-image: url({{ asset('frontend/assets/images/background/video-1.jpg') }});">
    <div class="auto-container">
        <div class="inner-box clearfix">
            <div class="text">
                <h2>Looking to Buy a New Property or <br />Sell an Existing One?</h2>
            </div>
            <div class="btn-box">
                <a href="{{ route('rent.property') }}" class="theme-btn btn-three">Rent Properties</a>
                <a href="{{ route('buy.property') }}" class="theme-btn btn-one">Buy Properties</a>
            </div>
        </div>
    </div>
</section>
<!-- cta-section end -->


<!-- feature-section -->
<section class="feature-section sec-pad">
    <div class="auto-container">
        <div class="sec-title centred">
            <h5>Latest Property</h5>
            <h2>Recent Properties</h2>
            <p>Featuring the country's most selective developments, we promise investors and buyers an unmatched level
                of service.</p>
        </div>
        <div class="row clearfix">
            @foreach ($property as $item)
                @php
                    $user = App\Models\User::where('id', $item->agent_id)->get();
                @endphp
                <div class="col-lg-4 col-md-6 col-sm-12 feature-block">
                    <div class="feature-block-one wow fadeInUp animated" data-wow-delay="00ms"
                        data-wow-duration="1500ms">
                        <div class="inner-box">
                            <div class="image-box">
                                <figure class="image"><img src="{{ asset($item->property_thambnail) }}" alt="">
                                </figure>
                                <div class="batch"><i class="icon-11"></i></div>
                                <span class="category">Featured</span>
                            </div>
                            <div class="lower-content">
                                <div class="author-info clearfix">
                                    <div class="author pull-left">
                                        @if ($item->agent_id == null)
                                            <figure class="author-thumb"><img
                                                    src="{{ url('upload/salman_sourov.png') }}" alt="">
                                            </figure>
                                            <h6>Admin </h6>
                                        @else
                                            <figure class="author-thumb"><img
                                                    src="{{ !empty($item->user->photo) ? url('upload/agent_images/' . $item->user->photo) : url('upload/no_image.jpg') }}"
                                                    alt=""></figure>
                                            <h6>{{ $item->user->name }}</h6>
                                        @endif
                                    </div>
                                    <div class="buy-btn pull-right"><a
                                            href="{{ url('property/details/' . $item->id . '/' . $item->property_slug) }}">For
                                            Buy</a>
                                    </div>
                                </div>
                                <div class="title-text">
                                    <h4><a
                                            href="{{ url('property/details/' . $item->id . '/' . $item->property_slug) }}">{{ $item->property_name }}</a>
                                    </h4>
                                </div>
                                <div class="price-box clearfix">
                                    <div class="price-info pull-left">
                                        <h6>Start From</h6>
                                        <h4>$ {{ $item->lowest_price }}</h4>
                                    </div>
                                    <ul class="other-option pull-right clearfix">
                                        <li><a aria-label="Compare" class="action-btn" id="{{ $item->id }}"
                                                onclick="addToCompare(this.id)"><i class="icon-12"></i></a></li>
                                        <li><a aria-label="Add To Wishlist" class="action-btn" id="{{ $item->id }}"
                                                onclick="addToWishList(this.id)"><i class="icon-13"></i></a></li>
                                    </ul>
                                </div>
                                <p class="two-line-text">{{ $item->short_descp }}</p>
                                <ul class="more-details clearfix">
                                    <li><i class="icon-14"></i>{{ $item->bedrooms }} Beds</li>
                                    <li><i class="icon-15"></i>{{ $item->bathrooms }} Baths</li>
                                    <li><i class="icon-16"></i>{{ $item->property_size }} Sq Ft</li>
                                </ul>
                                <div class="btn-box"><a
                                        href="{{ url('property/details/' . $item->id . '/' . $item->property_slug) }}"
                                        class="theme-btn btn-two">See Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- feature-section end -->


<!-- subscribe-section -->
<section class="subscribe-section bg-color-3">
    <div class="pattern-layer" style="background-image: url(assets/images/shape/shape-2.png);"></div>
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-12 text-column">
                <div class="text">
                    <span>Subscribe</span>
                    <h2>Sign Up To Our Newsletter To Get The Latest News And Offers.</h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 form-column">
                <div class="form-inner">
                    <form action="contact.html" method="post" class="subscribe-form">
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Enter your email" required="">
                            <button type="submit">Subscribe Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- subscribe-section end -->

@endsection
