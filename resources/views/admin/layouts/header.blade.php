<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
    <div class="collapse navbar-collapse justify-content-between">
        <div class="navbar-logo">
            <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
                <span class="navbar-toggle-icon">
                    <span class="toggle-line"></span>
                </span>
            </button>
            <a class="navbar-brand me-1 me-sm-3" href="index.html">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('public/admin/assets/img/icons/logo.png') }}" alt="phoenix" width="27" />
                        <h5 class="logo-text ms-2 d-none d-sm-block">{{ config('app.name') }}</h5>
                    </div>
                </div>
            </a>
        </div>
        <ul class="navbar-nav navbar-nav-icons flex-row">
            <li class="nav-item">
                <div class="theme-control-toggle fa-icon-wait px-2">
                    <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
                    <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
                        <span class="icon" data-feather="moon"></span>
                    </label>
                    <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme" style="height:32px;width:32px;">
                        <span class="icon" data-feather="sun"></span>
                    </label>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-l ">
                        <img class="rounded-circle " src="{{ asset('public/admin/assets/img/team/40x40/57.webp') }}" alt="" />
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
                    <div class="card position-relative border-0">
                        <div class="card-body p-0">
                            <div class="text-center pt-4 pb-3">
                                <div class="avatar avatar-xl ">
                                    <img class="rounded-circle " src="{{ asset('public/admin/assets/img/team/72x72/57.webp') }}" alt="" />
                                </div>
                                <h6 class="mt-2 text-body-emphasis">{{ auth()->user()->name }}</h6>
                            </div>
                            <div class="mb-3 mx-3">
                                <input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Update your status" value="{{ auth()->user()->client_id }}" readonly />
                            </div>
                        </div>
                        <div class="overflow-auto scrollbar">
                            <ul class="nav d-flex flex-column mb-2 pb-1">
                                <li class="nav-item">
                                    <a class="nav-link px-3 d-block" href="{{ route('profile') }}">
                                        <span class="me-2 text-body align-bottom" data-feather="user"></span>
                                        <span>Profile</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 d-block" href="{{ route('dashboard') }}">
                                        <span class="me-2 text-body align-bottom" data-feather="pie-chart"></span>Dashboard
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer p-0 border-top border-translucent">
                            @if(auth()->id() == 1)
                            <ul class="nav d-flex flex-column my-3">
                                <li class="nav-item">
                                    <a class="nav-link px-3 d-block" href="{{ route('company.create') }}">
                                        <span class="me-2 text-body align-bottom" data-feather="user-plus"></span>
                                        Add another company
                                    </a>
                                </li>
                            </ul>
                            <hr />
                            @endif
                            <div class="px-3 mb-3">
                                <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="{{ route('logout') }}">
                                    <span class="me-2" data-feather="log-out"> </span>Sign out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>