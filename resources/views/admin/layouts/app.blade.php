<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ $siteName ?? config('app.name') }}</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #e0e7ff;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-bg: #1e1b4b;
            --sidebar-text: #c7d2fe;
            --sidebar-hover: rgba(79, 70, 229, 0.3);
            --sidebar-active: #4f46e5;
            --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --card-hover-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }

        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }

        /* Sidebar Styles */
        .main-sidebar { background: var(--sidebar-bg) !important; box-shadow: 4px 0 20px rgba(0,0,0,0.15); }
        .brand-link { background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1) !important; padding: 16px 15px; }
        .brand-link .brand-text { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.1rem; color: #fff !important; letter-spacing: 0.5px; }
        .brand-link:hover { background: rgba(255,255,255,0.08); }

        .sidebar { background: transparent; }
        .nav-sidebar .nav-item > .nav-link {
            color: var(--sidebar-text) !important;
            border-radius: 8px;
            margin: 2px 10px;
            padding: 10px 15px;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .nav-sidebar .nav-item > .nav-link:hover {
            background: var(--sidebar-hover) !important;
            color: #fff !important;
            transform: translateX(3px);
        }
        .nav-sidebar .nav-item > .nav-link.active {
            background: var(--sidebar-active) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }
        .nav-sidebar .nav-link .nav-icon { color: var(--sidebar-text); width: 1.25rem; }
        .nav-sidebar .nav-item > .nav-link.active .nav-icon { color: #fff; }

        .nav-header {
            color: #6366f1 !important;
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            letter-spacing: 1.5px !important;
            text-transform: uppercase;
            padding: 12px 20px 4px;
        }

        /* Navbar */
        .main-header { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: none; }
        .main-header .nav-link { color: #64748b !important; }

        /* User menu in sidebar */
        .user-panel { border-bottom: 1px solid rgba(255,255,255,0.1) !important; padding: 15px; }
        .user-panel .info a { color: #c7d2fe; font-size: 0.875rem; }
        .user-panel img { border: 2px solid var(--primary); }

        /* Cards */
        .card { border: none; border-radius: 12px; box-shadow: var(--card-shadow); transition: all 0.3s ease; }
        .card:hover { box-shadow: var(--card-hover-shadow); }
        .card-header { border-radius: 12px 12px 0 0 !important; background: #fff; border-bottom: 1px solid #f1f5f9; padding: 16px 20px; }
        .card-header h3 { font-family: 'Poppins', sans-serif; font-size: 1rem; font-weight: 600; color: #1e293b; margin: 0; }
        .card-body { padding: 20px; }

        /* Stat cards */
        .info-box {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: var(--card-shadow) !important;
            min-height: 90px;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .info-box:hover { transform: translateY(-3px); box-shadow: var(--card-hover-shadow) !important; }
        .info-box-icon { border-radius: 12px 0 0 12px !important; width: 80px; display: flex; align-items: center; justify-content: center; }
        .info-box-content { padding: 15px; }
        .info-box-text { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
        .info-box-number { font-family: 'Poppins', sans-serif; font-size: 1.8rem; font-weight: 700; color: #1e293b; }

        /* Buttons */
        .btn { border-radius: 8px; font-weight: 500; font-size: 0.875rem; transition: all 0.2s ease; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); transform: translateY(-1px); }
        .btn-sm { padding: 0.25rem 0.6rem; font-size: 0.8rem; }

        /* Tables */
        .table thead th { background: #f8fafc; color: #64748b; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border: none; padding: 12px 16px; }
        .table td { padding: 12px 16px; vertical-align: middle; border-color: #f1f5f9; font-size: 0.875rem; }
        .table tbody tr { transition: background 0.15s; }
        .table tbody tr:hover { background: #f8fafc; }

        /* Badges */
        .badge { border-radius: 6px; font-size: 0.7rem; font-weight: 600; padding: 4px 8px; }

        /* Forms */
        .form-control, .form-select { border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.875rem; padding: 0.5rem 0.75rem; transition: all 0.2s; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        label { font-weight: 500; font-size: 0.875rem; color: #374151; margin-bottom: 4px; }

        /* Alert messages */
        .alert { border-radius: 10px; border: none; font-size: 0.875rem; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        .alert-warning { background: #fef3c7; color: #92400e; }
        .alert-info { background: #dbeafe; color: #1e40af; }

        /* Breadcrumb */
        .content-header { padding: 15px 20px; }
        .breadcrumb { background: transparent; padding: 0; margin: 0; }
        .breadcrumb-item a { color: var(--primary); }
        .breadcrumb-item.active { color: #64748b; }
        .content-header h1 { font-family: 'Poppins', sans-serif; font-size: 1.4rem; font-weight: 700; color: #1e293b; }

        /* Avatar */
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
        .user-avatar-sm { width: 32px; height: 32px; }
        .user-avatar-lg { width: 60px; height: 60px; }
        .user-avatar-xl { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }

        /* Sidebar scroll */
        .os-content-glue { height: auto !important; }

        /* Notification badge */
        .notification-badge { position: absolute; top: 5px; right: 5px; background: #ef4444; color: white; font-size: 0.6rem; min-width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        /* Role badge colors */
        .role-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; color: #fff; }

        /* Page header card */
        .page-header-card { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: 12px; padding: 24px; color: white; margin-bottom: 24px; }
        .page-header-card h1 { font-family: 'Poppins', sans-serif; font-size: 1.5rem; font-weight: 700; margin: 0; }
        .page-header-card p { opacity: 0.85; margin: 4px 0 0; font-size: 0.875rem; }

        /* Sidebar brand logo */
        .brand-logo-img { width: 30px; height: 30px; border-radius: 6px; object-fit: cover; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

        /* Footer */
        .main-footer { background: #fff; border-top: 1px solid #f1f5f9; font-size: 0.8rem; color: #94a3b8; padding: 12px 20px; }
    </style>

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('admin.dashboard') }}" class="nav-link text-muted">
                    <i class="fas fa-home me-1"></i> Home
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">

            {{-- Notifications --}}
            <li class="nav-item dropdown position-relative">
                <a class="nav-link" data-toggle="dropdown" href="#" role="button">
                    <i class="far fa-bell"></i>
                    @if(($unreadNotifs ?? 0) > 0)
                        <span class="badge badge-warning navbar-badge">{{ $unreadNotifs }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="border-radius:12px; border:none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); min-width:320px;">
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span style="font-weight:600; font-size:0.9rem;">Notifications</span>
                        <a href="{{ route('admin.notifications.read-all') }}" class="text-primary" style="font-size:0.8rem;" onclick="event.preventDefault(); document.getElementById('mark-all-form').submit();">Mark all read</a>
                    </div>
                    @forelse(auth()->user()->notifications()->whereNull('read_at')->latest()->take(5)->get() as $notif)
                        <a href="#" class="dropdown-item">
                            <div class="media">
                                <span class="mr-3 badge badge-{{ $notif->type ?? 'info' }}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:1rem;">
                                    <i class="{{ $notif->icon ?? 'fas fa-bell' }}"></i>
                                </span>
                                <div class="media-body">
                                    <h3 class="dropdown-item-title" style="font-size:0.85rem;font-weight:600;">{{ $notif->title }}</h3>
                                    <p class="text-sm" style="font-size:0.8rem;color:#64748b;">{{ Str::limit($notif->message, 60) }}</p>
                                    <p class="text-sm text-muted" style="font-size:0.75rem;">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4 text-muted" style="font-size:0.85rem;">
                            <i class="far fa-bell-slash mb-2" style="font-size:2rem;display:block;opacity:0.3;"></i>
                            No new notifications
                        </div>
                    @endforelse
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer text-center" style="font-size:0.85rem;color:var(--primary);">
                        View All Notifications
                    </a>
                </div>
                <form id="mark-all-form" action="{{ route('admin.notifications.read-all') }}" method="POST" style="display:none;">@csrf</form>
            </li>

            {{-- User Menu --}}
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" role="button">
                    <img src="{{ auth()->user()->avatarUrl }}" class="user-avatar user-avatar-sm mr-2" alt="User">
                    <span class="d-none d-md-inline" style="font-size:0.85rem;font-weight:500;color:#374151;">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down ml-1" style="font-size:0.65rem;color:#94a3b8;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="border-radius:12px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15);min-width:220px;">
                    <div class="px-3 py-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <img src="{{ auth()->user()->avatarUrl }}" class="user-avatar mr-2" alt="">
                            <div>
                                <div style="font-weight:600;font-size:0.875rem;color:#1e293b;">{{ auth()->user()->name }}</div>
                                <div style="font-size:0.75rem;color:#94a3b8;">{{ auth()->user()->email }}</div>
                                <div class="mt-1">{!! auth()->user()->primaryRoleBadge !!}</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.profile.index') }}" class="dropdown-item" style="font-size:0.875rem;">
                        <i class="fas fa-user mr-2 text-primary"></i> My Profile
                    </a>
                    <a href="{{ route('admin.profile.edit') }}" class="dropdown-item" style="font-size:0.875rem;">
                        <i class="fas fa-edit mr-2 text-info"></i> Edit Profile
                    </a>
                    @can('settings.index')
                    <a href="{{ route('admin.settings.index') }}" class="dropdown-item" style="font-size:0.875rem;">
                        <i class="fas fa-cog mr-2 text-secondary"></i> Site Settings
                    </a>
                    @endcan
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item text-danger" style="font-size:0.875rem;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: var(--sidebar-bg) !important;">
        <!-- Brand Logo -->
        <a href="{{ route('admin.dashboard') }}" class="brand-link" style="background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1);">
            @if(!empty($siteLogo))
                <img src="{{ asset('storage/settings/'.$siteLogo) }}" class="brand-logo-img mr-2" alt="Logo">
            @else
                <span style="background:var(--primary);width:30px;height:30px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;margin-right:8px;">
                    <i class="fas fa-shield-alt text-white" style="font-size:14px;"></i>
                </span>
            @endif
            <span class="brand-text">{{ $siteName ?? 'RBAC System' }}</span>
        </a>

        <div class="sidebar">
            <!-- User Panel -->
            <div class="user-panel mt-3 pb-3 mb-2 d-flex">
                <div class="image">
                    <img src="{{ auth()->user()->avatarUrl }}" class="img-circle elevation-2" alt="User" style="width:35px;height:35px;object-fit:cover;">
                </div>
                <div class="info">
                    <a href="{{ route('admin.profile.index') }}" class="d-block" style="color:#c7d2fe;font-weight:500;font-size:0.875rem;">
                        {{ auth()->user()->name }}
                    </a>
                    <span style="color:#6366f1;font-size:0.7rem;">
                        @if(auth()->user()->primaryRole)
                            <i class="{{ auth()->user()->primaryRole->icon ?? 'fas fa-shield-alt' }} mr-1"></i>
                            {{ auth()->user()->primaryRole->name }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- Nav Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" role="menu">

                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- User Management Section -->
                    @canany(['users.index', 'roles.index', 'permissions.index'])
                    <li class="nav-header">User Management</li>

                    @can('users.index')
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    @endcan

                    @can('roles.index')
                    <li class="nav-item">
                        <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>Roles</p>
                        </a>
                    </li>
                    @endcan

                    @can('permissions.index')
                    <li class="nav-item">
                        <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Permissions</p>
                        </a>
                    </li>
                    @endcan
                    @endcanany

                    <!-- Finance Section -->
                    @canany(['finance.ledgers.index', 'finance.bank.index', 'finance.cashflows.index', 'finance.expenses.index'])
                    <li class="nav-header">Finance</li>
                    @can('finance.ledgers.index')
                    <li class="nav-item">
                        <a href="{{ route('admin.finance.ledgers.index') }}" class="nav-link {{ request()->routeIs('admin.finance.ledgers.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Ledgers</p>
                        </a>
                    </li>
                    @endcan
                    @can('finance.bank.index')
                    <li class="nav-item">
                        <a href="{{ route('admin.finance.bank-accounts.index') }}" class="nav-link {{ request()->routeIs('admin.finance.bank-accounts.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building-columns"></i>
                            <p>Bank & Cash</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.finance.statement.index') }}" class="nav-link {{ request()->routeIs('admin.finance.statement.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-lines"></i>
                            <p>Bank Statement</p>
                        </a>
                    </li>
                    @endcan
                    @can('finance.cashflows.index')
                    <li class="nav-item">
                        <a href="{{ route('admin.finance.cashflows.index') }}" class="nav-link {{ request()->routeIs('admin.finance.cashflows.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-arrow-trend-up"></i>
                            <p>Cashflow Planning</p>
                        </a>
                    </li>
                    @endcan
                    @can('finance.expenses.index')
                    <li class="nav-item">
                        <a href="{{ route('admin.finance.expenses.index') }}" class="nav-link {{ request()->routeIs('admin.finance.expenses.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-receipt"></i>
                            <p>Expense Planning</p>
                        </a>
                    </li>
                    @endcan
                    @endcanany

                    <!-- Content Section -->
                    @can('items.index')
                    <li class="nav-header">Content</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.items.index') }}" class="nav-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Items</p>
                        </a>
                    </li>
                    @endcan

                    <!-- Account Section -->
                    <li class="nav-header">Account</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.profile.index') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-circle"></i>
                            <p>My Profile</p>
                        </a>
                    </li>

                    @can('activity.index')
                    <li class="nav-item">
                        <a href="{{ route('admin.activity.index') }}" class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Activity Logs</p>
                        </a>
                    </li>
                    @endcan

                    @can('settings.index')
                    <li class="nav-header">System</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Site Settings</p>
                        </a>
                    </li>
                    @endcan

                    <!-- Logout -->
                    <li class="nav-header">Session</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-danger-light" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" style="color:#fca5a5 !important;">
                            <i class="nav-icon fas fa-sign-out-alt" style="color:#fca5a5;"></i>
                            <p>Logout</p>
                        </a>
                        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper" style="background:#f1f5f9;">

        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-1 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a></li>
                            @yield('breadcrumbs')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="#" style="color:var(--primary);">{{ $siteName ?? 'RBAC System' }}</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0 &nbsp;|&nbsp; Powered by <span style="color:var(--primary);">Laravel 11</span>
        </div>
    </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({ theme: 'bootstrap4' });

    // Auto-dismiss alerts
    setTimeout(function() { $('.alert').fadeOut('slow'); }, 5000);

    // Delete confirmations with SweetAlert
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            borderRadius: '12px',
        }).then((result) => {
            if (result.isConfirmed) { form.submit(); }
        });
    });

    // Initialize DataTables
    if ($('.datatable').length) {
        $('.datatable').DataTable({
            pageLength: 15,
            language: { search: '<i class="fas fa-search"></i>', searchPlaceholder: 'Search...' },
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
        });
    }
});
</script>

@stack('scripts')
</body>
</html>
