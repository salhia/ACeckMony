<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('/') }}" target="_blank" class="sidebar-brand">
            Akec<span>Money</span>
        </a>
    </div>

    <div class="sidebar-body">
        <ul class="nav">

            <li class="nav-item nav-category">Agent Dashboard</li>

            <li class="nav-item">
                <a href="{{ route('agent.dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <!-- Customers Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#customersManagement" role="button" aria-expanded="false" aria-controls="customersManagement">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">agent Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="customersManagement">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('all.agent') }}" class="nav-link">Agent List</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('add.agent') }}" class="nav-link">Add New Agent</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Agent Settings</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Transactions -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#transactionsMenu" role="button" aria-expanded="false" aria-controls="transactionsMenu">
                    <i class="link-icon" data-feather="repeat"></i>
                    <span class="link-title">Transactions</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="transactionsMenu">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">New Transaction</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Transactions History</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Search Transactions</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Earnings and Commissions -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#earningsCommissions" role="button" aria-expanded="false" aria-controls="earningsCommissions">
                    <i class="link-icon" data-feather="dollar-sign"></i>
                    <span class="link-title">Earnings & Commissions</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="earningsCommissions">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">Total Earnings</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Hierarchy Commissions</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Account Statement</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Reports -->
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="file-text"></i>
                    <span class="link-title">Reports</span>
                </a>
            </li>

            <!-- Notifications -->
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="link-icon" data-feather="bell"></i>
                    <span class="link-title">Notifications</span>
                    @if(auth()->user()->unreadNotifications->count())
                        <span class="badge badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </a>
            </li>

        </ul>
    </div>
</nav>
