<nav class="navbar" style="background: #011b39;">
    <a href="#" class="sidebar-toggler">
        <i data-feather="menu"></i>
    </a>
    <div class="navbar-content">
        <form class="search-form">
            <div class="input-group">
                <div class="input-group-text">
                    <i data-feather="search"></i>
                </div>
                <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
            </div>
        </form>
        <ul class="navbar-nav">
            @php
                $id = Auth::user()->id;
                $user = App\Models\User::find($id);
                $role = $user->role;

                // Set the image path based on role
                $imagePath = match($role) {
                    'admin' => 'upload/admin_images/',
                    'agent' => 'upload/agent_images/',
                    'user' => 'upload/user_images/',
                    default => 'upload/no_image.jpg'
                };
            @endphp

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="wd-30 ht-30 rounded-circle"
                         src="{{ !empty($user->photo) ? url($imagePath . $user->photo) : url('upload/no_image.jpg') }}"
                         alt="profile">
                </a>
                <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                    <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                        <div class="mb-3">
                            <img class="wd-80 ht-80 rounded-circle"
                                src="{{ !empty($user->photo) ? url($imagePath . $user->photo) : url('upload/no_image.jpg') }}"
                                alt="profile">
                        </div>
                        <div class="text-center">
                            <p class="tx-16 fw-bolder">{{ $user->name }}</p>
                            <p class="tx-12 text-muted">{{ $user->email }}</p>
                        </div>
                    </div>
                    <ul class="list-unstyled p-1">
                        @if($role === 'admin')
                            <li class="dropdown-item py-2">
                                <a href="{{ route('admin.profile') }}" class="text-body ms-0">
                                    <i class="me-2 icon-md" data-feather="user"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li class="dropdown-item py-2">
                                <a href="{{ route('admin.change.password') }}" class="text-body ms-0">
                                    <i class="me-2 icon-md" data-feather="edit"></i>
                                    <span>Change Password</span>
                                </a>
                            </li>
                            <li class="dropdown-item py-2">
                                <a href="{{ route('admin.logout') }}" class="text-body ms-0">
                                    <i class="me-2 icon-md" data-feather="log-out"></i>
                                    <span>Log Out</span>
                                </a>
                            </li>
                        @elseif($role === 'agent')
                            <li class="dropdown-item py-2">
                                <a href="{{ route('agent.profile') }}" class="text-body ms-0">
                                    <i class="me-2 icon-md" data-feather="user"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li class="dropdown-item py-2">
                                <a href="{{ route('agent.change.password') }}" class="text-body ms-0">
                                    <i class="me-2 icon-md" data-feather="edit"></i>
                                    <span>Change Password</span>
                                </a>
                            </li>
                            <li class="dropdown-item py-2">
                                <a href="{{ route('agent.logout') }}" class="text-body ms-0">
                                    <i class="me-2 icon-md" data-feather="log-out"></i>
                                    <span>Log Out</span>
                                </a>
                            </li>
                        @else
                            <li class="dropdown-item py-2">
                                <a href="{{ route('profile.edit') }}" class="text-body ms-0">
                                    <i class="me-2 icon-md" data-feather="user"></i>
                                    <span>Edit Profile</span>
                                </a>
                            </li>
                            <li class="dropdown-item py-2">
                                <a href="{{ route('profile.change-password') }}" class="text-body ms-0">
                                    <i class="me-2 icon-md" data-feather="lock"></i>
                                    <span>Change Password</span>
                                </a>
                            </li>
                            <li class="dropdown-item py-2">
                                <a href="{{ route('logout') }}" class="text-body ms-0"
                                   onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                    <i class="me-2 icon-md" data-feather="log-out"></i>
                                    <span>Log Out</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
