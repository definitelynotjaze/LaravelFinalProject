<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
  <title>@yield('title', 'BrewCraft') | Expertly Crafted Coffee</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/brewcraft.css') }}">
  @stack('styles')
</head>
<body>

{{-- Tell JS whether this is a fresh login (account switch detection) --}}
<div id="app-config"
     data-just-logged-in="{{ session('just_logged_in') ? 1 : 0 }}">
</div>
<script>
  window.BC_USER_ID  = {{ auth()->check() ? auth()->id() : 'null' }};
  window.BC_BASE_URL = '{{ rtrim(config("app.url"), '/') }}';
</script>

{{-- ══════════════ NAVBAR ══════════════ --}}
<nav class="bc-nav">
  <div class="container">
    <a href="{{ route('home') }}" class="bc-brand">Brew<em>Craft</em></a>

    @if(Request::routeIs('home'))
    <div class="bc-nav-links ms-3 d-none d-lg-flex">
      <a href="#home">Home</a>
      <a href="#services">Services</a>
      <a href="#menu">Menu</a>
      <a href="#contact">Contact</a>
    </div>
    @endif

    <div class="bc-nav-right">
      @auth
        <a href="{{ route('cart') }}" class="cart-icon-btn" title="Cart">
          <i class="bi bi-bag" style="font-size:1.1rem;"></i>
          <span class="cart-badge" id="cartBadge">0</span>
        </a>

        <div class="dropdown">
          <button class="btn-bc-outline btn-bc-sm d-flex align-items-center gap-2 py-2" data-bs-toggle="dropdown">
            <div class="avatar-sm" style="width:26px;height:26px;font-size:.7rem;">
              @if(auth()->user()->profile_photo)
                <img src="{{ asset('images/profiles/' . auth()->user()->profile_photo) }}" alt="">
              @else
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
              @endif
            </div>
            <span class="d-none d-sm-inline" style="font-size:.85rem; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->name }}</span>
            <i class="bi bi-chevron-down" style="font-size:.7rem;"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end border-0 shadow" style="border-radius:var(--r); min-width:190px;">
            @if(in_array(auth()->user()->role, ['admin','staff']))
              <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
              </a></li>
            @endif
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('orders.index') }}">
              <i class="bi bi-clock-history"></i> My Orders
            </a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile') }}">
              <i class="bi bi-person-circle"></i> Profile
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="dropdown-item d-flex align-items-center gap-2 text-danger">
                  <i class="bi bi-box-arrow-right"></i> Log Out
                </button>
              </form>
            </li>
          </ul>
        </div>
      @else
        <a href="{{ route('login') }}"    class="btn-bc-outline btn-bc-sm">Log In</a>
        <a href="{{ route('register') }}" class="btn-bc btn-bc-sm">Sign Up</a>
      @endauth
    </div>
  </div>
</nav>

@yield('content')

<footer class="bc-footer">
  <div class="container">
    <div class="row g-4 g-lg-5">
      <div class="col-lg-4">
        <div class="foot-brand">Brew<em>Craft</em></div>
        <p class="foot-desc">Expertly crafted coffee and beverages made with quality and care. Order fresh, sip better.</p>
        <div class="d-flex gap-2 mt-3">
          <a href="#" style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);"><i class="bi bi-facebook"></i></a>
          <a href="#" style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);"><i class="bi bi-instagram"></i></a>
          <a href="#" style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6>Navigate</h6>
        <a href="{{ route('home') }}#home">Home</a>
        <a href="{{ route('home') }}#services">Services</a>
        <a href="{{ route('home') }}#menu">Menu</a>
        <a href="{{ route('home') }}#contact">Contact</a>
      </div>
      <div class="col-6 col-lg-2">
        <h6>Account</h6>
        @guest
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
        @endguest
        @auth
        <a href="{{ route('orders.index') }}">My Orders</a>
        <a href="{{ route('profile') }}">Profile</a>
        @endauth
        <a href="{{ route('cart') }}">Cart</a>
      </div>
      <div class="col-lg-4">
        <h6>Visit Us</h6>
        <p style="color:rgba(255,255,255,.45); font-size:.85rem; line-height:1.9;">
          123 Aurora Boulevard, Barangay New<br>
Manila Quezon City, Metro Manila, Philippines 1112<br>
          <a href="mailto:hello@brewcraft.ph" style="color:var(--gold); display:inline;">hello@brewcraft.ph</a><br>
          +63 956 950 8925
        </p>
        <p style="color:rgba(255,255,255,.35); font-size:.78rem; margin-top:.5rem;">
          Mon–Fri: 7:00 AM – 9:00 PM<br>
          Sat–Sun: 8:00 AM – 10:00 PM
        </p>
      </div>
    </div>
    <div class="bc-footer-bar">
      © {{ date('Y') }} BrewCraft. All rights reserved. &nbsp;·&nbsp; Crafted with quality and care in the Philippines.
    </div>
  </div>
</footer>

<div id="bc-toast"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/brewcraft.js') }}"></script>


@if(session('success'))
<div
    id="flash-success"
    data-message="{{ session('success') }}"
    hidden>
</div>
@endif

@if(session('error'))
<div
    id="flash-error"
    data-message="{{ session('error') }}"
    hidden>
</div>
@endif

@stack('scripts')
</body>
</html>