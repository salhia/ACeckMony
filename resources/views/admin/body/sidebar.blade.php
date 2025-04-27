<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('/') }}" target="_blank" class="sidebar-brand">
             Akec<span>Money</span>
        </a>
    </div>

    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">Admin Dashboard</li>

            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <!-- Users Management -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#usersManagement" role="button" aria-expanded="false" aria-controls="usersManagement">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Users Management</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="usersManagement">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">Manage Agents</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Add New Agent</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Manage Roles & Permissions</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Financial Transactions -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#financialTransactions" role="button" aria-expanded="false" aria-controls="financialTransactions">
                    <i class="link-icon" data-feather="repeat"></i>
                    <span class="link-title">Financial Transactions</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="financialTransactions">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">Transaction History</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Pending Transactions</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Account Movements</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Commissions & Discounts -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#commissionsDiscounts" role="button" aria-expanded="false" aria-controls="commissionsDiscounts">
                    <i class="link-icon" data-feather="percent"></i>
                    <span class="link-title">Commissions & Discounts</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="commissionsDiscounts">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">Commission Settings</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Hierarchy Structure</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Profit Distribution</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Reports -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#reportsStatistics" role="button" aria-expanded="false" aria-controls="reportsStatistics">
                    <i class="link-icon" data-feather="bar-chart-2"></i>
                    <span class="link-title">Reports & Statistics</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="reportsStatistics">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">Transaction Reports</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Agents Reports</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Financial Reports</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- System Settings -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#systemSettings" role="button" aria-expanded="false" aria-controls="systemSettings">
                    <i class="link-icon" data-feather="settings"></i>
                    <span class="link-title">System Settings</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="systemSettings">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">General Settings</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Notification Management</a>
                        </li>

                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
