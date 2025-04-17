@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <div class="row inbox-wrapper">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 border-end-lg">
                                <div class="d-flex align-items-center justify-content-between">
                                    <button class="navbar-toggle btn btn-icon border d-block d-lg-none"
                                        data-bs-target=".email-aside-nav" data-bs-toggle="collapse" type="button">
                                        <span class="icon"><i data-feather="chevron-down"></i></span>
                                    </button>
                                    <div class="order-first">
                                        <h4>Mail Service</h4>
                                        <p class="text-muted">support@empobd.com</p>
                                    </div>
                                </div>
                                <br>
                                {{-- <div class="d-grid my-3">
                                    <a class="btn btn-primary" href="./compose.html">Compose Email</a>
                                </div> --}}
                                <div class="email-aside-nav collapse">
                                    <ul class="nav flex-column">
                                        <li class="nav-item active">
                                            <a class="nav-link d-flex align-items-center"
                                                href="{{ route('admin.property.message') }}">
                                                <i data-feather="inbox" class="icon-lg me-2"></i>
                                                Inbox
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="d-flex align-items-end mb-2 mb-md-0">
                                                <i data-feather="inbox" class="text-muted me-2"></i>
                                                <h4 class="me-1">Inbox</h4>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <input class="form-control" type="text" placeholder="Search mail...">
                                                <button class="btn btn-light btn-icon" type="button"
                                                    id="button-search-addon"><i data-feather="search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="email-list">
                                    <!-- email list item -->
                                    <div class="table-responsive">
                                        <table class="table">

                                            <tbody>
                                                <tr>
                                                    <th>Customer Name : </th>
                                                    <td>{{ isset($msgdetails['user']['name']) ? $msgdetails['user']['name'] : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Customer Email : </th>
                                                    <td>{{ isset($msgdetails['user']['email']) ? $msgdetails['user']['email'] : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Customer Phone : </th>
                                                    <td>{{ isset($msgdetails['user']['phone']) ? $msgdetails['user']['phone'] : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Agent ID : </th>
                                                    <td>{{ isset($msgdetails['property']['agent_id']) ? $msgdetails['property']['agent_id'] : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Agent Name : </th>
                                                    <td>{{ isset($msgdetails['agent']['name']) ? $msgdetails['agent']['name'] : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Property Name : </th>
                                                    <td>{{ isset($msgdetails['property']['property_name']) ? $msgdetails['property']['property_name'] : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Property Code : </th>
                                                    <td>{{ isset($msgdetails['property']['property_code']) ? $msgdetails['property']['property_code'] : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Property Status : </th>
                                                    <td>{{ isset($msgdetails['property']['property_status']) ? $msgdetails['property']['property_status'] : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Message : </th>
                                                    <td>{{ isset($msgdetails->message) ? $msgdetails->message : 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Sending Time : </th>
                                                    <td>{{ isset($msgdetails->created_at) ? $msgdetails->created_at->format('l M d') : 'N/A' }}
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
