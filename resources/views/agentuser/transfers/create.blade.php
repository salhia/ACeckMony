@extends('agentuser.user_dashboard')
@section('agentuser')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

<div class="page-content">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">
                <i class="fas fa-money-bill-transfer"></i> Money Transfer
            </h3>
        </div>

        <div class="card-body">
            <form id="transferForm" method="POST" action="{{ route('transfers.store') }}">
                @csrf

                <!-- State Information -->
                <div class="form-group mb-4">
                    <label for="state_code">State</label>
                    <select class="form-control" id="state_code" name="state_code" required>
                        <option value="">-- Select State --</option>
                        @foreach($sys_regions as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sender Information -->
                <div class="sender-info mb-4">
                    <h4 class="section-title">
                        <i class="fas fa-user"></i> Sender Information
                    </h4>
                    <div class="form-group">
                        <label for="sender_search">Search Sender</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="sender_search" placeholder="ID or Phone">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" id="searchSender">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="senderResult" class="mt-3"></div>
                    <input type="hidden" id="sender_id" name="sender_id">
                </div>

                <!-- Receiver Information -->
                <div class="receiver-info mb-4">
                    <h4 class="section-title">
                        <i class="fas fa-user-plus"></i> Receiver Information
                    </h4>
                    <div class="form-group">
                        <label for="receiver_search">Search Receiver</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="receiver_search" placeholder="ID or Phone">
                            <div class="input-group-append">
                                <button class="btn btn-outline-success" type="button" id="searchReceiver">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="receiverResult" class="mt-3"></div>
                    <input type="hidden" id="receiver_id" name="receiver_id">
                </div>

                <!-- Transfer Details -->
                <div class="transfer-details mb-4">
                    <h4 class="section-title">
                        <i class="fas fa-info-circle"></i> Transfer Details
                    </h4>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="1"></textarea>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Complete Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Adding New Customer -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <input type="hidden" id="customer_type">
                    <div class="form-group">
                        <label for="customer_name">Full Name</label>
                        <input type="text" class="form-control" id="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Phone Number</label>
                        <input type="text" class="form-control" id="customer_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_identity">Identity Number</label>
                        <input type="text" class="form-control" id="customer_identity">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCustomer">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Search for sender
    $('#searchSender').click(function() {
        const searchTerm = $('#sender_search').val().trim();
        searchCustomer(searchTerm, 'sender');
    });

    // Search for receiver
    $('#searchReceiver').click(function() {
        const searchTerm = $('#receiver_search').val().trim();
        searchCustomer(searchTerm, 'receiver');
    });

    // Save new customer
    $('#saveCustomer').click(function() {
        const type = $('#customer_type').val();
        const formData = {
            name: $('#customer_name').val(),
            phone: $('#customer_phone').val(),
            identity_number: $('#customer_identity').val(),
            _token: "{{ csrf_token() }}"
        };

        if (!formData.name || !formData.phone) {
            showAlert('Please enter both name and phone number', 'danger');
            return;
        }

        $.ajax({
            url: "{{ route('customers.store') }}",
            method: 'POST',
            data: formData,
            beforeSend: function() {
                $('#saveCustomer').html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                $('#saveCustomer').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $('#customerModal').modal('hide');
                    selectCustomer(response.customer.id, response.customer.name, type);
                    showAlert(`${type === 'sender' ? 'Sender' : 'Receiver'} added successfully`, 'success');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'An error occurred while saving';
                showAlert(errorMsg, 'danger');
            },
            complete: function() {
                $('#saveCustomer').html('Save');
                $('#saveCustomer').prop('disabled', false);
            }
        });
    });
});

// Generic customer search function
function searchCustomer(searchTerm, type) {
    if (searchTerm.length < 3) {
        showAlert('Please enter at least 3 characters (ID or phone)', 'warning');
        return;
    }

    $.ajax({
        url: "{{ route('customers.search') }}",
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            search: searchTerm
        },
        beforeSend: function() {
            $(`#search${type === 'sender' ? 'Sender' : 'Receiver'}`).html('<i class="fas fa-spinner fa-spin"></i> Searching...');
        },
        success: function(response) {
            const resultDiv = $(`#${type}Result`);
            if (response.found) {
                resultDiv.html(`
                    <div class="alert alert-success">
                        <h5>${response.customer.name}</h5>
                        <p>Phone: ${response.customer.phone}</p>
                        <p>ID: ${response.customer.identity_number}</p>
                        <button type="button" class="btn btn-sm btn-primary mt-2"
                                onclick="selectCustomer(${response.customer.id}, '${response.customer.name}', '${type}')">
                            Select this ${type === 'sender' ? 'sender' : 'receiver'}
                        </button>
                    </div>
                `);
            } else {
                resultDiv.html(`
                    <div class="alert alert-warning">
                        <p>No ${type === 'sender' ? 'sender' : 'receiver'} found with this data</p>
                        <button type="button" class="btn btn-sm btn-success"
                                onclick="showCustomerModal('${searchTerm}', '${type}')">
                            Add new ${type === 'sender' ? 'sender' : 'receiver'}
                        </button>
                    </div>
                `);
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'An error occurred during search';
            showAlert(errorMsg, 'danger');
        },
        complete: function() {
            $(`#search${type === 'sender' ? 'Sender' : 'Receiver'}`).html('<i class="fas fa-search"></i> Search');
        }
    });
}

// Show add customer modal
function showCustomerModal(searchTerm, type) {
    $('#customer_type').val(type);
    $('#customer_phone').val(searchTerm);
    $('#customerModalLabel').text(`Add new ${type === 'sender' ? 'Sender' : 'Receiver'}`);
    $('#modalHeader').removeClass('bg-primary bg-success').addClass(type === 'sender' ? 'bg-primary' : 'bg-success');
    $('#saveCustomer').removeClass('btn-primary btn-success').addClass(type === 'sender' ? 'btn-primary' : 'btn-success');
    $('#customer_name').val('');
    $('#customer_identity').val('');
    $('#customerModal').modal('show');
}

// Select customer and show confirmation
function selectCustomer(customerId, customerName, type) {
    $(`#${type}_id`).val(customerId);
    $(`#${type}Result`).html(`
        <div class="alert ${type === 'sender' ? 'alert-info' : 'alert-success'}">
            <p>Selected ${type === 'sender' ? 'Sender' : 'Receiver'}: <strong>${customerName}</strong></p>
        </div>
    `);
}

// Show alert messages
function showAlert(message, type) {
    const alert = $(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>`);

    $('#transferForm').prepend(alert);
    setTimeout(() => alert.alert('close'), 5000);
}
</script>

@if ($errors->has('error'))
<script>
    $(document).ready(function () {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-left",
            "timeOut": "5000"
        };
        toastr.error("{{ $errors->first('error') }}", "Error");
    });
</script>
@endif
@endpush


@section('styles')
<style>
.section-title {
    color: #444;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.alert {
    margin-bottom: 0;
}
#modalHeader.bg-primary {
    background-color: #007bff !important;
}
#modalHeader.bg-success {
    background-color: #28a745 !important;
}
</style>
@endsection


