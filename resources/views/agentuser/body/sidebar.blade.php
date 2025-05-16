<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('/') }}" target="_blank" class="sidebar-brand">
            Akec<span>Money</span>
        </a>
    </div>

    <div class="sidebar-body">
        <ul class="nav">

            <li class="nav-item nav-category">User Panel</li>

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('user.dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <!-- Transfers -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#transferMenu" role="button" aria-expanded="false" aria-controls="transferMenu">
                    <i class="link-icon" data-feather="repeat"></i>
                    <span class="link-title">Money Transfers</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="transferMenu">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('transfers.create') }}" class="nav-link">New Transfer</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agent.sending.transfers') }}" class="nav-link">Sending  Transfers  </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('agent.received.transfers') }}" class="nav-link"> Received Transfers </a>
                        </li>

                         <li class="nav-item">
                            <a href="{{ route('transfers.index') }}" class="nav-link"> ALL Transfers </a>
                        </li>




                    </ul>
                </div>
            </li>

            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#profileMenu" role="button" aria-expanded="false" aria-controls="profileMenu">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">My Profile</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="profileMenu">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link">Edit Profile</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Change Password</a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>
    </div>
</nav>
