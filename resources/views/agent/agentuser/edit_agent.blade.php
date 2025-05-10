@extends('agent.agent_dashboard')
@section('agent')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <div class="page-content">
        <div class="row profile-body">
            <div class="col-md-8 col-xl-8 middle-wrapper">
                <div class="row">
                    <div class="card">
                        <div class="card-body">

                            <h6 class="card-title">Update Agent Information</h6>

                            <form id="myForm" method="POST" action="{{ route('update.agent') }}" enctype="multipart/form-data" class="forms-sample">
                                @csrf

                                <input type="hidden" name="id" value="{{ $allagent->id }}">

                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Agent Name </label>
                                    <input type="text" name="name" class="form-control" value="{{ $allagent->name }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Agent Email </label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ $allagent->email }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Agent Phone </label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ $allagent->phone }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Agent Address </label>
                                    <input type="text" name="address" class="form-control"
                                        value="{{ $allagent->address }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="formFile">Upload Photo</label>
                                    <input class="form-control" name="photo" type="file" id="image">
                                </div>

                                <div class="mb-3">
                                    <div>
                                        <img id="showImage" class="wd-100 rounded-circle"
                                            src="{{ !empty($allagent->photo) ? url('upload/agent_images/' . $allagent->photo) : url('upload/no_image.jpg') }}"
                                            alt="profile">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary me-2">Save Changes </button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#myForm').validate({
                rules: {
                    name: {
                        required: true,
                    },
                    email: {
                        required: true,
                    },
                    phone: {
                        required: true,
                    },
                    password: {
                        required: true,
                    },

                },
                messages: {
                    name: {
                        required: 'Please Enter Name',
                    },
                    email: {
                        required: 'Please Enter Email',
                    },
                    phone: {
                        required: 'Please Enter Phone',
                    },
                    password: {
                        required: 'Please Enter Password',
                    },

                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#image').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });
    </script>

@endsection
