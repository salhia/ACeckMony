<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('/') }}" target="_blank" class="sidebar-brand">
            Akec<span>Money</span>
        </a>
    </div>

    <div class="sidebar-body" id="sidebarAccordion">
        <ul class="nav">

            <li class="nav-item nav-category">Supper Agent Dashboard</li>

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
                <div class="collapse" id="customersManagement" data-bs-parent="#sidebarAccordion">
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
                    <span class="link-title">Fee payment Details</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="transactionsMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('agent.payments.grouped') }}" class="nav-link">Payment paid (Grouped by Date)</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agent.payments.history') }}" class="nav-link">Search Payment History</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Earnings and Commissions -->


            <!-- Reports -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#reportsMenu" role="button" aria-expanded="false" aria-controls="reportsMenu">
                    <i class="link-icon" data-feather="file-text"></i>
                    <span class="link-title">Reports</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="reportsMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('agent.office.summary') }}" class="nav-link">Office Summary Report</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agent.office.detailed') }}" class="nav-link">Office Detailed Report</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agent.user.transactions') }}" class="nav-link">User Transactions</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agent.commission.report') }}" class="nav-link">Vendor Commission </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agent.users.balance.report') }}" class="nav-link">Users Balance Report</a>
                        </li>
                    </ul>
                </div>
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
