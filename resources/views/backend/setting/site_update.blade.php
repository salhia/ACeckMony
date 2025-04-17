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

                            <h6 class="card-title">Update Site Setting </h6>

                            <form id="myForm" method="POST" action="{{ route('update.site.setting') }}"
                                class="forms-sample" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ $sitesetting->id }}">

                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Support Phone </label>
                                    <input type="text" name="support_phone" class="form-control"
                                        value="{{ $sitesetting->support_phone }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Company Address </label>
                                    <input type="text" name="company_address" class="form-control"
                                        value="{{ $sitesetting->company_address }}">
                                </div>


                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Email </label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ $sitesetting->email }}">
                                </div>



                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Facebook </label>
                                    <input type="text" name="facebook" class="form-control"
                                        value="{{ $sitesetting->facebook }}">
                                </div>



                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Instagram </label>
                                    <input type="text" name="twitter" class="form-control"
                                        value="{{ $sitesetting->twitter }}">
                                </div>


                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Copyright </label>
                                    <input type="text" name="copyright" class="form-control"
                                        value="{{ $sitesetting->copyright }}">
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-5">
                                        <label for="logoInput" class="form-label">Upload Logo</label>
                                        <input type="file" name="logo" id="logoInput" class="form-control"
                                            accept="image/*">
                                        <br>
                                        <img id="showLogo" class="wd-80"
                                            src="{{ !empty($sitesetting->logo) ? asset($sitesetting->logo) : url('upload/no_image.jpg') }}"
                                            alt="profile">
                                    </div>
                                    <div class="col-sm-7">
                                        <label for="bannerInput" class="form-label">Upload Banner Photo</label>
                                        <input type="file" name="banner_photo" id="bannerInput" class="form-control"
                                            accept="image/*">
                                        <br>
                                        <img id="showBanner" class="wd-80"
                                            src="{{ !empty($sitesetting->banner_photo) ? asset($sitesetting->banner_photo) : url('upload/no_image.jpg') }}"
                                            alt="profile">
                                    </div>
                                </div>

                                <button type="submit" class=" btn btn-primary me-2">Save Changes </button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- middle wrapper end -->
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function() {
            $('#logoInput').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showLogo').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files[0]);
            });

            $('#bannerInput').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showBanner').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files[0]);
            });
        });
    </script>
@endsection
