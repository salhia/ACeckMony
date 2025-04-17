@php
    $states = App\Models\State::latest()->get();
    $ptypes = App\Models\PropertyType::latest()->get();
    $setting = App\Models\SiteSetting::first();
@endphp

<section class="banner-section" style="background-image: url('{{ asset($setting->banner_photo) }}');">

    <div class="auto-container">
        <div class="inner-container">
            <div class="content-box centred">
                <h2>Find Your Perfect Property</h2>
                <p>Discover top real estate listings for homes, apartments, and more.</p>
            </div>
            <div class="search-field">
                <div class="tabs-box">
                    <div class="tab-btn-box">
                        <ul class="tab-btns tab-buttons centred clearfix">
                            <li class="tab-btn active-btn" data-tab="#tab-1">BUY</li>
                            <li class="tab-btn" data-tab="#tab-2">RENT</li>
                        </ul>
                    </div>
                    <div class="tabs-content info-group">
                        <div class="tab active-tab" id="tab-1">
                            <div class="inner-box">
                                <div class="top-search">
                                    <form action="{{ route('buy.property.search') }}" method="post"
                                        class="search-form">
                                        @csrf
                                        <div class="row clearfix">
                                            <div class="col-lg-4 col-md-12 col-sm-12 column">
                                                <div class="form-group">
                                                    <label>Buy Search Property</label>
                                                    <div class="field-input">
                                                        <i class="fas fa-search"></i>
                                                        <input type="search" name="search"
                                                            placeholder="Search by Property Name" required="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12 column">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <div class="select-box">
                                                        <i class="far fa-compass"></i>
                                                        <select name="state" class="wide">
                                                            <option data-display="Input State">Input State
                                                            </option>
                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->state_name }}">
                                                                    {{ $state->state_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12 column">
                                                <div class="form-group">
                                                    <label>Property Type</label>
                                                    <div class="select-box">
                                                        <select name="ptype_id" class="wide">
                                                            <option data-display="All Type">All Type</option>
                                                            @foreach ($ptypes as $ptype)
                                                                <option value="{{ $ptype->type_name }}">
                                                                    {{ $ptype->type_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="search-btn">
                                            <button type="submit"><i class="fas fa-search"></i>Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab" id="tab-2">
                            <div class="inner-box">
                                <div class="top-search">
                                    <form action="{{ route('rent.property.search') }}" method="post"
                                        class="search-form">
                                        @csrf
                                        <div class="row clearfix">
                                            <div class="col-lg-4 col-md-12 col-sm-12 column">
                                                <div class="form-group">
                                                    <label>Rent Search Property</label>
                                                    <div class="field-input">
                                                        <i class="fas fa-search"></i>
                                                        <input type="search" name="search"
                                                            placeholder="Search by Property Name" required="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12 column">
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <div class="select-box">
                                                        <i class="far fa-compass"></i>
                                                        <select name="state" class="wide">
                                                            <option data-display="Input State">Input State
                                                            </option>
                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->state_name }}">
                                                                    {{ $state->state_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12 column">
                                                <div class="form-group">
                                                    <label>Property Type</label>
                                                    <div class="select-box">
                                                        <select name="ptype_id" class="wide">
                                                            <option data-display="All Type">All Type</option>
                                                            @foreach ($ptypes as $ptype)
                                                                <option value="{{ $ptype->type_name }}">
                                                                    {{ $ptype->type_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="search-btn">
                                            <button type="submit"><i class="fas fa-search"></i>Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="propertyResults"></div> <!-- Placeholder for property search results -->
                </div>
            </div>
        </div>
    </div>
</section>
