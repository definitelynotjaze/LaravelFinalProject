<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
  <title>@yield('title', 'Dashboard') | BrewCraft Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/brewcraft.css') }}">
  @stack('styles')
</head>
<body>

{{-- Sidebar overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ══════════════ SIDEBAR ══════════════ --}}
<aside class="bc-sidebar" id="bcSidebar">
  <div class="sidebar-brand">
    <span class="name">Brew<em>Craft</em></span>
    <small>{{ ucfirst(auth()->user()->role ?? 'Dashboard') }} Panel</small>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-sect">Overview</div>
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
      <i class="bi bi-grid-1x2"></i> Dashboard
    </a>

    @if(auth()->user()->role === 'admin')
    <div class="sidebar-sect">Management</div>
    <a href="{{ route('admin.users') }}" class="sidebar-link {{ Request::routeIs('admin.users') ? 'active' : '' }}">
      <i class="bi bi-people"></i> Users
    </a>
    <a href="{{ route('admin.menu') }}" class="sidebar-link {{ Request::routeIs('admin.menu*') ? 'active' : '' }}">
      <i class="bi bi-cup-hot"></i> Menu Items
    </a>
    <a href="{{ route('admin.orders') }}" class="sidebar-link {{ Request::routeIs('admin.orders') ? 'active' : '' }}">
      <i class="bi bi-receipt"></i> All Orders
      @php $pendingOrders = \App\Models\Order::where('status','pending')->count(); @endphp
      @if($pendingOrders > 0)
        <span class="sidebar-badge">{{ $pendingOrders }}</span>
      @endif
    </a>
    <a href="{{ route('admin.contacts') }}" class="sidebar-link {{ Request::routeIs('admin.contacts') ? 'active' : '' }}">
      <i class="bi bi-envelope"></i> Contact Messages
      @php $unread = \App\Models\Contact::where('is_read', false)->count(); @endphp
      @if($unread > 0)
        <span class="sidebar-badge">{{ $unread }}</span>
      @endif
    </a>
    <a href="{{ route('admin.analytics') }}" class="sidebar-link {{ Request::routeIs('admin.analytics') ? 'active' : '' }}">
      <i class="bi bi-bar-chart-line"></i> Analytics
    </a>
    @endif

    @if(auth()->user()->role === 'staff')
    <div class="sidebar-sect">Operations</div>
    <a href="{{ route('staff.orders') }}" class="sidebar-link {{ Request::routeIs('staff.orders') ? 'active' : '' }}">
      <i class="bi bi-receipt"></i> Orders
    </a>
    <a href="{{ route('staff.menu') }}" class="sidebar-link {{ Request::routeIs('staff.menu') ? 'active' : '' }}">
      <i class="bi bi-cup-hot"></i> Menu
    </a>
    @endif

    <div class="sidebar-sect">Account</div>
    <a href="{{ route('profile') }}" class="sidebar-link">
      <i class="bi bi-person-circle"></i> Profile
    </a>
    <a href="{{ route('home') }}" class="sidebar-link">
      <i class="bi bi-house"></i> View Site
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="avatar">
        @if(auth()->user()->profile_photo)
          <img src="{{ asset('images/profiles/' . auth()->user()->profile_photo) }}" alt="">
        @else
          {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        @endif
      </div>
      <div class="info">
        <strong>{{ auth()->user()->name }}</strong>
        <small>{{ auth()->user()->role }}</small>
      </div>
      <form action="{{ route('logout') }}" method="POST" class="ms-auto">
        @csrf
        <button title="Log Out" style="background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;padding:.2rem;font-size:1rem;">
          <i class="bi bi-box-arrow-right"></i>
        </button>
      </form>
    </div>
  </div>
</aside>

{{-- ══════════════ MAIN ══════════════ --}}
<div class="bc-main">
  {{-- Topbar --}}
  <div class="bc-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="d-lg-none" id="sidebarToggle" style="background:none;border:none;font-size:1.3rem;color:var(--chestnut);cursor:pointer;">
        <i class="bi bi-list"></i>
      </button>
      <span class="bc-topbar-title">@yield('page-title', 'Dashboard')</span>
    </div>
    <div class="bc-topbar-right">
      <span style="font-size:.8rem;color:var(--chestnut);" class="d-none d-sm-block">
        {{ now()->format('l, F j, Y') }}
      </span>
      <div class="dropdown">
        <button class="avatar-sm" data-bs-toggle="dropdown" style="cursor:pointer;border:none;">
          @if(auth()->user()->profile_photo)
            <img src="{{ asset('images/profiles/' . auth()->user()->profile_photo) }}" alt="">
          @else
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
          @endif
        </button>
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow" style="border-radius:var(--r);">
          <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>

  {{-- Page content --}}
  <div class="bc-page">
    @yield('content')
  </div>
</div>

<div id="bc-toast"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  window.BC_USER_ID  = {{ auth()->check() ? auth()->id() : 'null' }};
  window.BC_BASE_URL = '{{ rtrim(config("app.url"), '/') }}';
</script>
<script src="{{ asset('js/brewcraft.js') }}"></script>

@if(session('success'))
<script>
  document.addEventListener('DOMContentLoaded', () => bcToast('{{ addslashes(session("success")) }}'));
</script>
@endif
@if(session('error'))
<script>
  document.addEventListener('DOMContentLoaded', () => bcToast('{{ addslashes(session("error")) }}', 'error'));
</script>
@endif

@stack('scripts')
</body>
</html>
