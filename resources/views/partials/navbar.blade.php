<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid px-3 px-md-5">

        {{-- Brand --}}
        <a class="navbar-brand fw-bold text-maroon d-flex align-items-center gap-2"
           href="{{ route('student.dashboard') }}">
            <span class="logo-circle">P</span>
            <span class="fs-4 tracking-tight">ParkiRek</span>
        </a>

        {{-- Hamburger (mobile) --}}
        <button class="navbar-toggler border-0 p-2 shadow-none" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#studentNavbar"
                aria-controls="studentNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menu --}}
        <div class="collapse navbar-collapse" id="studentNavbar">
            <ul class="navbar-nav ms-lg-auto align-items-lg-center gap-lg-2 mt-3 mt-lg-0">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
                       href="{{ route('student.dashboard') }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.vehicles.*') ? 'active' : '' }}"
                       href="{{ route('student.vehicles.index') }}">
                        <i class="bi bi-car-front"></i>
                        <span>My Vehicles</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.violations.*') ? 'active' : '' }}"
                       href="{{ route('student.violations.index') }}">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Violations</span>
                    </a>
                </li>

                {{-- User Badge (Desktop) --}}
                @if(auth()->check())
                <li class="nav-item d-none d-lg-flex align-items-center ms-lg-2 ps-lg-2 border-start">
                    <div class="d-flex align-items-center gap-2 px-2 py-1">
                        <div class="avatar-circle">
                            {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                        </div>
                        <div class="d-flex flex-column text-start">
                            <span class="fw-bold small text-dark leading-tight">{{ auth()->user()->nama ?? 'Student' }}</span>
                            <span class="text-muted" style="font-size: 0.72rem;">Student</span>
                        </div>
                    </div>
                </li>
                @endif

                {{-- Divider khusus mobile --}}
                <li class="nav-item d-lg-none my-2">
                    <hr class="dropdown-divider">
                </li>

                {{-- ✅ Logout --}}
                <li class="nav-item ms-lg-2">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1.5 border-0 bg-danger-subtle text-danger fw-semibold">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</nav>
