<nav class="navbar navbar-vertical navbar-expand-lg">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">

                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ navbar('/') }}" href="{{ route('dashboard') }}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon">
                                    <span data-feather="pie-chart"></span>
                                </span>
                                <div class="d-flex align-items-center">
                                    <span class="nav-link-text">Dashboard</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </li>

                @if(auth()->id() == 1)
                <li class="nav-item">
                    <p class="navbar-vertical-label"><b> Company & Users</b></p>
                    <hr class="navbar-vertical-line" />

                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1" href="#nv-company" role="button" data-bs-toggle="collapse" aria-expanded="{{ navbar(['company','company/create']) ? 'true' : 'false' }}" aria-controls="nv-company">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="briefcase"></span>
                                </span>
                                <span class="nav-link-text">Company</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent {{ navbar(['company','company/create']) ? 'show' : '' }}" data-bs-parent="#navbarVerticalCollapse" id="nv-company">
                                <li class="collapsed-nav-item-title d-none">Company</li>

                                <li class="nav-item">
                                    <a class="nav-link {{ navbar(['company/create']) }}" href="{{ route('company.create') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Create</span>
                                        </div>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ navbar(['company','company/edit/*']) }}" href="{{ route('company.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">List</span>
                                        </div>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </li>
                @endif

                <li class="nav-item">
                    <p class="navbar-vertical-label"><b> Transactions & Sales</b></p>
                    <hr class="navbar-vertical-line" />

                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1" href="#nv-sales" role="button" data-bs-toggle="collapse" aria-expanded="{{ navbar('transaction') ? 'true' : 'false' }}" aria-controls="nv-sales">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="credit-card"></span>
                                </span>
                                <span class="nav-link-text">Transactions</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent {{ navbar('transaction') ? 'show' : '' }}" data-bs-parent="#navbarVerticalCollapse" id="nv-sales">
                                <li class="collapsed-nav-item-title d-none">Transactions</li>

                                <li class="nav-item">
                                    <a class="nav-link {{ navbar('transaction') && ! request()->has('date') ? 'active' : '' }}" href="{{ route('tnx.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">All</span>
                                        </div>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ navbar('transaction') && request('date') == date('Y-m-d') ? 'active' : '' }}" href="{{ route('tnx.index',['date' => date('Y-m-d')]) }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Today</span>
                                        </div>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ navbar('transaction') && request('date') == date('Y-m-d', strtotime('-1 day')) ? 'active' : '' }}" href="{{ route('tnx.index',['date' => date('Y-m-d', strtotime('-1 day'))]) }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Yesterday</span>
                                        </div>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</nav>