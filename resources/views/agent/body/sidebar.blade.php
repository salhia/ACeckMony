@php
    $id = Auth::user()->id;
    $agentId = App\Models\User::find($id);
    $status = $agentId->status;
@endphp

<nav class="sidebar">
    <div class="sidebar-header" style="background: #011b39;">
        <a href="{{ route('agent.dashboard') }}" class="sidebar-brand">
            Akeck<span>Mony</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body" style="background: #011b39;">
        <ul class="nav">
            <li class="nav-item nav-category">Juba Mony Transfer</li>
            <li class="nav-item">
                <a href="{{ route('agent.dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="box"></i>
                    <span class="link-title">Transfer Dashboard</span>
                </a>
            </li>

            @if ($status == 'active')
                <li class="nav-item nav-category">Main menu</li>

                {{-- Property --}}
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#property" role="button" aria-expanded="false"
                        aria-controls="emails">
                        <i class="link-icon" data-feather="home"></i>
                        <span class="link-title">Property</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" id="property">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="{{ route('agent.all.property') }}" class="nav-link">All Property</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('agent.add.property') }}" class="nav-link">Add Property</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="{{ route('buy.package') }}" class="nav-link">
                        <i class="link-icon" data-feather="unlock"></i>
                        <span class="link-title">Buy Package</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('package.history') }}" class="nav-link">
                        <i class="link-icon" data-feather="package"></i>
                        <span class="link-title">Package History</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('agent.property.message') }}" class="nav-link">
                        <i class="link-icon" data-feather="mail"></i>
                        <span class="link-title">Property Message</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('agent.schedule.request') }}" class="nav-link">
                        <i class="link-icon" data-feather="mail"></i>
                        <span class="link-title">Schedule Request</span>
                    </a>
                </li>

                <li class="nav-item nav-category">Components</li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#uiComponents" role="button"
                        aria-expanded="false" aria-controls="uiComponents">
                        <i class="link-icon" data-feather="feather"></i>
                        <span class="link-title">UI Kit</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>
                    <div class="collapse" id="uiComponents">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="pages/ui-components/accordion.html" class="nav-link">Accordion</a>
                            </li>
                            <li class="nav-item">
                                <a href="pages/ui-components/alerts.html" class="nav-link">Alerts</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#advancedUI" role="button"
                        aria-expanded="false" aria-controls="advancedUI">
                        <i class="link-icon" data-feather="anchor"></i>
                        <span class="link-title">Advanced UI</span>
                        <i class="link-arrow" data-feather="chevron-down"></i>
                    </a>

                    <div class="collapse" id="advancedUI">
                        <ul class="nav sub-menu">
                            <li class="nav-item">
                                <a href="pages/advanced-ui/cropper.html" class="nav-link">Cropper</a>
                            </li>
                            <li class="nav-item">
                                <a href="pages/advanced-ui/owl-carousel.html" class="nav-link">Owl carousel</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @else
            @endif
                <li class="nav-item nav-category">Docs</li>
                <li class="nav-item">
                    <a href="#" target="_blank" class="nav-link">
                        <i class="link-icon" data-feather="hash"></i>
                        <span class="link-title">Documentation</span>
                    </a>
                </li>
        </ul>
    </div>
</nav>

