@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <div class="page-content">
        <div class="row profile-body">
            <!-- middle wrapper start -->
            <div class="col-md-8 col-xl-8 middle-wrapper">
                <div class="row">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Add Permission </h6>

                            <form id="myForm" method="POST" action="{{ route('store.permission') }}" class="forms-sample">
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Permission Name </label>
                                    <input type="text" name="name" class="form-control">

                                </div>

                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Group Name </label>
                                    <select name="group_name" class="form-select" id="exampleFormControlSelect1">
                                        <option selected="" disabled="">Select Group</option>
                                        <option value="type">Type</option>
                                        <option value="state">State</option>
                                        <option value="amenities">Amenities</option>
                                        <option value="property">Property</option>
                                        <option value="history">Package History </option>
                                        <option value="message">Property Message </option>
                                        <option value="testimonials">Testimonials</option>
                                        <option value="agent">Manage Agent</option>
                                        <option value="category">Blog Category</option>
                                        <option value="post">Blog Post</option>
                                        <option value="comment">Blog Comment</option>
                                        <option value="smtp">SMTP Setting</option>
                                        <option value="site">Site Setting</option>
                                        <option value="role">Role & Permission </option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary me-2">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- middle wrapper end -->
        </div>
    </div>
@endsection
