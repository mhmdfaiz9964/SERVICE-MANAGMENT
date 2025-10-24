<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="p-0 m-0">
                <!-- Dashboard -->
                <li class="submenu-open">
                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-feather="grid" class="sidebar-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Users & Roles -->
                <li class="submenu-open">
                    <a href="{{ route('admin.users.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i data-feather="users" class="sidebar-icon"></i>
                        <span>Users & Roles</span>
                    </a>
                </li>

                <!-- Services & Categories -->
                <li class="menu-item submenu {{ request()->routeIs('admin.service.categories.*') || request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link submenu-toggle">
                        <i data-feather="tool" class="sidebar-icon"></i>
                        <span>Services</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <ul style="display: {{ request()->routeIs('admin.service.categories.*') || request()->routeIs('admin.services.*') ? 'block' : 'none' }};" class="submenu">
                        <li>
                            <a href="{{ route('admin.service.categories.index') }}" 
                               class="submenu-item {{ request()->routeIs('admin.service.categories.*') ? 'active' : '' }}">
                                <i data-feather="list" class="sidebar-icon"></i>
                                <span>Service Categories</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.services.index') }}" 
                               class="submenu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                                <i data-feather="tool" class="sidebar-icon"></i>
                                <span>Services</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Technicians -->
                <li class="submenu-open">
                    <a href="{{ route('admin.technician.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.technician.*') ? 'active' : '' }}">
                        <i data-feather="user-check" class="sidebar-icon"></i>
                        <span>Technicians</span>
                    </a>
                </li>

                <!-- Customers -->
                <li class="submenu-open">
                    <a href="{{ route('admin.customer.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                        <i data-feather="user-plus" class="sidebar-icon"></i>
                        <span>Customers</span>
                    </a>
                </li>

                <!-- Bookings -->
                <li class="submenu-open">
                    <a href="#" class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <i data-feather="calendar" class="sidebar-icon"></i>
                        <span>Bookings</span>
                    </a>
                </li>

                <!-- AMC Contracts -->
                <li class="submenu-open">
                    <a href="#" class="sidebar-link {{ request()->routeIs('admin.amc.*') ? 'active' : '' }}">
                        <i data-feather="file-text" class="sidebar-icon"></i>
                        <span>AMC Contracts</span>
                    </a>
                </li>

                <!-- Reports -->
                <li class="submenu-open">
                    <a href="#" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i data-feather="bar-chart-2" class="sidebar-icon"></i>
                        <span>Reports</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="submenu-open">
                    <a href="{{ route('admin.app_settings') }}"
                        class="sidebar-link {{ request()->routeIs('admin.app_settings') ? 'active' : '' }}">
                        <i data-feather="settings" class="sidebar-icon"></i>
                        <span>Settings</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->

@push('styles')
    <style>
        /* Sidebar background */
        .sidebar-inner.slimscroll {
            background-color: #062A74;
            min-height: 100vh;
            padding-top: 10px;
        }

        .sidebar .sidebar-menu>ul>li>a.active span,
        .sidebars .sidebar-menu>ul>li>a.active span {
            color: #062A74;
        }

        .sidebar .sidebar-menu>ul>li>a:hover span,
        .sidebars .sidebar-menu>ul>li>a:hover span {
            color: #062A74;
        }

        /* Menu items */
        .sidebar .sidebar-menu>ul>li {
            margin: 4px 0;
        }

        .sidebar .sidebar-menu>ul>li>a {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: 6px;
            color: #ffffff;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            justify-content: flex-start; /* Align content to the left */
        }

        .sidebar .sidebar-menu>ul>li>a span {
            margin-left: 12px;
            font-size: 15px;
            font-weight: 500;
            color: #ffffff;
        }

        /* Submenu toggle arrow - positioned to the right but content left-aligned */
        .sidebar .sidebar-menu>ul>li>a .menu-arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 12px;
        }

        .sidebar .sidebar-menu>ul>li.menu-item.submenu.active .menu-arrow,
        .sidebar .sidebar-menu>ul>li.menu-item.submenu:hover .menu-arrow {
            transform: rotate(90deg);
        }

        /* Icons */
        .sidebar .sidebar-menu>ul>li>a svg {
            width: 20px;
            height: 20px;
            color: #ffffff;
            flex-shrink: 0;
        }

        .sidebar .sidebar-menu>ul>li>a .submenu-item svg {
            width: 16px;
            height: 16px;
        }

        /* Hover & Active */
        .sidebar .sidebar-menu>ul>li>a:hover,
        .sidebar .sidebar-menu>ul>li>a.active {
            background-color: #ffffff;
            color: #062A74;
        }

        .sidebar .sidebar-menu>ul>li>a:hover svg,
        .sidebar .sidebar-menu>ul>li>a.active svg {
            color: #062A74;
        }

        /* Submenu styles - left-aligned */
        .sidebar .sidebar-menu .submenu {
            padding-left: 0;
            list-style: none;
        }

        .sidebar .sidebar-menu .submenu-item {
            padding: 8px 16px 8px 44px; /* Left padding for indentation */
            color: #ffffff;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: all 0.3s ease;
            background: none;
            border-radius: 4px;
            margin: 2px 0;
            text-align: left; /* Ensure left alignment */
        }

        .sidebar .sidebar-menu .submenu-item:hover,
        .sidebar .sidebar-menu .submenu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .sidebar .sidebar-menu .submenu-hdr {
            color: #ffffff;
            font-size: 13px;
            text-transform: uppercase;
            padding: 10px 16px;
            margin-top: 10px;
            display: block;
            letter-spacing: 0.5px;
            text-align: left;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        // Activate feather icons
        feather.replace();

        // Submenu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const submenuToggles = document.querySelectorAll('.submenu-toggle');
            submenuToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const parentLi = this.closest('.menu-item.submenu');
                    const submenu = parentLi.querySelector('.submenu');
                    const isOpen = submenu.style.display === 'block';

                    // Close all other submenus
                    document.querySelectorAll('.menu-item.submenu .submenu').forEach(s => {
                        s.style.display = 'none';
                    });
                    document.querySelectorAll('.menu-item.submenu').forEach(li => {
                        li.classList.remove('active');
                    });

                    // Toggle current submenu
                    if (isOpen) {
                        submenu.style.display = 'none';
                        parentLi.classList.remove('active');
                    } else {
                        submenu.style.display = 'block';
                        parentLi.classList.add('active');
                    }
                });
            });

            // Auto-open submenu if active route is inside
            const activeSubmenus = document.querySelectorAll('.menu-item.submenu.active .submenu');
            activeSubmenus.forEach(submenu => {
                submenu.style.display = 'block';
            });
        });
    </script>
@endpush