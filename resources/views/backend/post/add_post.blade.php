@extends('admin.admin_dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <div class="page-content">
        <div class="row profile-body">
            <!-- middle wrapper start -->
            <div class="col-md-12 col-xl-12 middle-wrapper">
                <div class="row">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Add Post</h6>

                            <form method="post" action="{{ route('store.post') }}" id="myForm"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Post Title</label>
                                            <input type="text" name="post_title" class="form-control">
                                        </div>
                                    </div><!-- Col -->

                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Blog Category</label>
                                            <select name="blogcat_id" class="form-select" id="exampleFormControlSelect1">
                                                <option selected="" disabled="">Select Category</option>
                                                @foreach ($blogcat as $item)
                                                    <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('blogcat_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div><!-- Col -->
                                </div><!-- Row -->

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Short Description</label>
                                        <textarea name="short_descp" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                    </div>
                                </div><!-- Col -->

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Long Description</label>
                                        <textarea name="long_descp" class="form-control" name="tinymce" id="tinymceExample" rows="10"></textarea>
                                    </div>
                                </div><!-- Col -->

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Main Thambnail </label>
                                            <input type="file" name="post_image" class="form-control"
                                                onChange="mainThamUrl(this)">
                                            <img src="" id="mainThmb">
                                        </div>
                                    </div><!-- Col -->

                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Post Tags </label>
                                            <input name="post_tags" id="tags" value="Realestate," />
                                        </div>
                                    </div><!-- Col -->
                                </div>

                                <button type="submit" class="btn btn-primary">Save Changes </button>
                            </form>

                        </div>
                    </div>



                </div>
            </div>
            <!-- middle wrapper end -->
        </div>
    </div>



    <script type="text/javascript">
        function mainThamUrl(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#mainThmb').attr('src', e.target.result).width(80).height(80);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
