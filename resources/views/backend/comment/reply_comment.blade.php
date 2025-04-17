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
                            <h6 class="card-title">Add State </h6>

                            <form id="myForm" method="POST" action="{{ route('reply.message') }}" class="forms-sample">
                                @csrf

                                <input type="hidden" name="id" value="{{ $comment->id }}">
                                <input type="hidden" name="user_id" value="{{ $comment->user_id }}">
                                <input type="hidden" name="post_id" value="{{ $comment->post_id }}">

                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">User Name: </label>
                                    <code>{{ $comment['user']['name'] }}</code>
                                </div>

                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Post Name: </label>
                                    <code>{{ $comment['post']['post_title'] }}</code>
                                </div>

                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Subject: </label>
                                    <code>{{ $comment->subject }}</code>
                                </div>

                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Message: </label>
                                    <code>{{ $comment->message }}</code>
                                </div>

                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Message: </label>
                                    <a href="{{ url('/blog/details/' . $comment['post']['post_slug']) }}"
                                        target="_blank">View Post
                                        Details</a>
                                </div>

                                @if ($view_comments->isNotEmpty())
                                    <div class="row">
                                        <div class="col-md-12 grid-margin stretch-card">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">Reply All</h6>
                                                    <div class="table-responsive">
                                                        <table id="dataTableExample" class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sl</th>
                                                                    <th>Subject</th>
                                                                    <th>Message</th>
                                                                    <th>Time</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($view_comments as $item)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $item->subject }}</td>
                                                                        <td>{{ $item->message }}</td>
                                                                        <td>{{ $item->created_at->format('M j, Y h:i A') }}
                                                                        <td>
                                                                            <a href="{{ route('admin.delete.comment', $item->id) }}"
                                                                                class="btn btn-inverse-danger" id="delete" title="Delete"> <i
                                                                                    data-feather="trash-2"></i> </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Input Data for store --}}
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Subject </label>
                                    <input type="text" name="subject" class="form-control ">
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea id="message" name="message" class="form-control" rows="4" cols="50"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary me-2">Save Changes </button>
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
            $('#myForm').validate({
                rules: {
                    subject: {
                        required: true
                    },
                    message: {
                        required: true
                    }
                },
                messages: {
                    address: {
                        required: 'Please enter the subject.'
                    },
                    password: {
                        required: 'Please enter a message.'
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.mb-3').append(error);
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

@endsection
