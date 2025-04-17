@extends('frontend.frontend_dashboard')
@section('main')
@section('title')
    {{ $blog->post_title }} | EMPO RealEstate
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
            <h1>{{ $blog->post_title }} Details</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>{{ $blog->post_title }} Details</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->


<!-- sidebar-page-container -->
<section class="sidebar-page-container blog-details sec-pad-2">
    <div class="auto-container">
        <div class="row clearfix">
            {{-- Left side --}}
            <div class="col-lg-8 col-md-12 col-sm-12 content-side">
                <div class="blog-details-content">
                    <div class="news-block-one">
                        <div class="inner-box">
                            <div class="image-box">
                                <figure class="image"><img
                                        src="{{ !empty($blog->post_image) ? asset($blog->post_image) : url('upload/no_image_blog.png') }}"
                                        alt=""></figure>
                                <span class="category">Featured</span>
                            </div>
                            <div class="lower-content">
                                <h3>{{ $blog->post_title }}</h3>
                                <ul class="post-info clearfix">
                                    <li class="author-box">
                                        <figure class="author-thumb"><img
                                                src="{{ !empty($blog->user->photo) ? url('upload/admin_images/' . $blog->user->photo) : url('upload/no_image.jpg') }}"
                                                alt=""></figure>
                                        <h5><a href="">{{ $blog->user->name }}</a></h5>
                                    </li>
                                    <li>{{ $blog->created_at->format('d M Y') }}</li>
                                </ul>
                                <div class="text">
                                    <p>{!! $blog->long_descp !!}</p>
                                </div>
                                <div class="post-tags">
                                    <ul class="tags-list clearfix">
                                        <li>
                                            <h5>Tags:</h5>
                                        </li>
                                        @foreach ($tags_all as $tag)
                                            <li><a href="">{{ ucwords($tag) }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="comments-area">
                        @php
                            $comment = App\Models\Comment::where('post_id', $blog->id)
                                ->where('parent_id', null)
                                ->limit(5)
                                ->get();
                        @endphp

                        @if ($comment->count() > 0)
                            <div class="group-title">
                                <h4>{{ $comment->count() }} Comments</h4>
                            </div>
                        @else
                            <div class="group-title">
                                <h4>No Comments</h4>
                            </div>
                        @endif
                        
                        <div class="comment-box">
                            @foreach ($comment as $com)
                                <div class="comment">
                                    <figure class="thumb-box">
                                        <img src="{{ !empty($com->user->photo) ? url('upload/user_images/' . $com->user->photo) : url('upload/no_image.jpg') }}"
                                            alt="">
                                    </figure>
                                    <div class="comment-inner">
                                        <div class="comment-info clearfix">
                                            <h5>{{ $com->user->name }}</h5>
                                            <span>{{ $com->created_at->format('M j, Y h:i A') }}</span>
                                        </div>
                                        <div class="comment-info clearfix">
                                            <h5>{{ $com->subject }}</h5>
                                            <p>{{ $com->message }}</p>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $reply = App\Models\Comment::where('parent_id', $com->id)->get();
                                @endphp

                                @foreach ($reply as $rep)
                                    <div class="comment replay-comment">
                                        <figure class="thumb-box">
                                            <img src="{{ url('upload/salman_sourov.png') }}" alt="">
                                        </figure>
                                        <div class="comment-inner">
                                            <div class="comment-info clearfix">
                                                <h5>{{ $rep->subject }}</h5>
                                                <span>{{ $rep->created_at->format('M j, Y h:i A') }}</span>
                                            </div>
                                            <div class="text">
                                                <p>{{ $rep->message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="comments-form-area">
                    <div class="group-title">
                        <h4>Leave a Comment</h4>
                    </div>

                    @auth
                        <form action="{{ route('store.comment') }}" method="post" class="comment-form default-form">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $blog->id }}">

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                    <input type="text" name="subject" placeholder="Subject" required="">
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                    <textarea name="message" placeholder="Your message" required=""></textarea>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 form-group message-btn">
                                    <button type="submit" class="theme-btn btn-one">Submit Now</button>
                                </div>
                            </div>
                        </form>
                    @else
                        <p><b>For Add Comment! You need to login first <br> <a href="{{ route('login') }}">Login
                                    Here</a></b></p>
                    @endauth
                </div>
            </div>

            {{-- Right Side --}}
            <div class="col-lg-4 col-md-12 col-sm-12 sidebar-side">
                <div class="blog-sidebar">
                    <div class="sidebar-widget social-widget">
                        <div class="widget-title">
                            <h4>Follow Us On</h4>
                        </div>
                        <ul class="social-links clearfix">
                            <li><a href="https://tech.empobd.com/"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="https://tech.empobd.com/"><i class="fab fa-google-plus-g"></i></a></li>
                            <li><a href="https://tech.empobd.com/"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="https://tech.empobd.com/"><i class="fab fa-linkedin-in"></i></a></li>
                            <li><a href="https://tech.empobd.com/"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                    <div class="sidebar-widget category-widget">
                        <div class="widget-title">
                            <h4>Category</h4>
                        </div>
                        <div class="widget-content">
                            <ul class="category-list clearfix">
                                @foreach ($bcategory as $cat)
                                    @php
                                        $post = App\Models\BlogPost::where('blogcat_id', $cat->id)->get();
                                    @endphp
                                    <li><a
                                            href="{{ url('blog/category/' . $cat->id) }}">{{ $cat->category_name }}<span>{{ count($post) }}</span></a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="sidebar-widget post-widget">
                        <div class="widget-title">
                            <h4>Recent Posts</h4>
                        </div>

                        @foreach ($dpost as $item)
                            <div class="post-inner">
                                <div class="post">
                                    <figure class="post-thumb"><a
                                            href="{{ url('blog/details/' . $item->post_slug) }}"><img
                                                src="{{ !empty($item->post_image) ? asset($item->post_image) : url('upload/no_image_blog.png') }}"
                                                alt=""></a></figure>
                                    <h5><a
                                            href="{{ url('blog/details/' . $item->post_slug) }}">{{ $item->post_title }}</a>
                                    </h5>
                                    <span class="post-date">{{ $item->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- sidebar-page-container -->
@endsection
