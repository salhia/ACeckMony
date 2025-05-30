@extends('agent.agent_dashboard')
@section('agent')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>

    <div class="page-content">
        <div class="row profile-body">
            <div class="col-md-8 col-xl-8 middle-wrapper">
                <div class="row">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Add Agent</h6>

                            <!-- Display Validation Errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Display Flash Notifications -->
                            @if (session('message'))
                                <div class="alert alert-{{ session('alert-type') }}">
                                    {{ session('message') }}
                                </div>
                            @endif

<form id="myForm" method="POST" action="{{ route('store.agent') }}" enctype="multipart/form-data" class="forms-sample">
    @csrf

    <div class="form-group mb-3">
        <label>Agent Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label>Agent Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control">
    </div>

    <div class="form-group mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label>Address</label>
        <input type="text" name="address" class="form-control">
    </div>

    <div class="form-group mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label>Region</label>
        <select name="region_id" class="form-control" required>
            <option value="">-- Select Region --</option>
            @foreach($regions as $region)
                <option value="{{ $region->id }}">{{ $region->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group mb-3">
        <label>Commission Rate (%)</label>
        <input type="number" step="0.01" name="commission_rate" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label>Transfer Limit</label>
        <input type="number" name="transfer_limit" class="form-control" required>
    </div>


    <div class="form-group mb-3">
        <label>Agent Image</label>
        <input type="file" name="photo" class="form-control" onchange="agentImage(this)">
        <img src="" id="agentImg" style="display: none; width: 80px; height: 80px;">
    </div>

    <button type="submit" class="btn btn-primary">Save Agent</button>
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
                    name: { required: true },
                    email: { required: true, email: true },
                    address: { required: true },
                    password: { required: true }
                },
                messages: {
                    name: { required: 'Please enter the agent\'s name.' },
                    email: { required: 'Please enter the agent\'s email.', email: 'Please enter a valid email address.' },
                    address: { required: 'Please enter the agent\'s address.' },
                    password: { required: 'Please enter a password.' }
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

        function agentImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#agentImg').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
