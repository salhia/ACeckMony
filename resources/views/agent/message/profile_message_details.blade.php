@extends('agent.agent_dashboard')
@section('agent')
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
                                <div class="d-grid my-3">
                                    <a class="btn btn-primary" href="./compose.html">Compose Email</a>
                                </div>
                                <div class="email-aside-nav collapse">
                                    <ul class="nav flex-column">
                                        <li class="nav-item active">
                                            <a class="nav-link d-flex align-items-center"
                                                href="{{ route('agent.profile.message.details') }}">
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
                                                <h4 class="me-1">Agent Profile Message</h4>
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
                                                    <td>{{ $promsgdetails['user']['name'] }}</td>
                                                </tr>

                                                <tr>
                                                    <th>Customer Email : </th>
                                                    <td>{{ $promsgdetails['user']['email'] }}</td>
                                                </tr>

                                                <tr>
                                                    <th>Customer Phone : </th>
                                                    <td>{{ $promsgdetails['user']['phone'] }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Message : </th>
                                                    <td>{{ $promsgdetails->message }}</td>
                                                </tr>

                                                <tr>
                                                    <th>Sending Time : </th>
                                                    <td>{{ $promsgdetails->created_at->format('l M d') }}</td>
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
