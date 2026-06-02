@extends('layouts.app')
@section('title', 'BrewCraft')

@section('content')

{{-- ══════════════════════════════════════════
     HERO / HOME
════════════════════════════════════════════ --}}
<section class="bc-hero" id="home" data-section="home">
  <div class="container py-5">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <div class="bc-hero-content">
          <div class="hero-eyebrow fade-up">
            <i class="bi bi-cup-hot-fill"></i>
            Manila's Finest Craft Coffee
          </div>

          <h1 class="fade-up fade-up-1">
            Every Cup<br>
            <em>Tells a Story</em>
          </h1>

          <p class="lead fade-up fade-up-2">
            Premium handcrafted beverages made with ethically sourced beans,
            delivered fresh to your door or ready for pick-up — ordered in seconds.
          </p>

          <div class="hero-cta fade-up fade-up-3">
            <a href="#menu" class="btn-bc btn-bc-lg">
              <i class="bi bi-bag"></i> Order Now
            </a>
            <a href="#menu" class="btn-bc-ghost btn-bc-lg" style="color:rgba(253,250,247,.7)!important;border-color:rgba(255,255,255,.2);">
              View Menu
            </a>
          </div>

          <div class="hero-stats fade-up fade-up-4">
            <div class="hero-stat">
              <strong>50+</strong>
              <span>Menu Items</span>
            </div>
            <div class="hero-stat">
              <strong>4.9★</strong>
              <span>Avg Rating</span>
            </div>
            <div class="hero-stat">
              <strong>2,000+</strong>
              <span>Happy Customers</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     SERVICES
════════════════════════════════════════════ --}}
<section class="bc-section" id="services" data-section="services">
  <div class="container scroll-target" id="services">
    <div class="row align-items-center mb-5">
      <div class="col-lg-6">
        <div class="sec-label">What We Offer</div>
        <h2 class="sec-title">Crafted for <em style="font-style:italic;">Every</em> Moment</h2>
      </div>
      <div class="col-lg-6">
        <p class="sec-sub ms-lg-auto">
          From your morning pick-me-up to an afternoon indulgence — BrewCraft delivers
          barista-quality drinks with the convenience of online ordering.
        </p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-sm-6 col-lg-3">
        <div class="svc-card">
          <div class="svc-icon">☕</div>
          <h4>Specialty Coffee</h4>
          <p>Single-origin beans roasted in-house, brewed with precision by our trained baristas.</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="svc-card">
          <div class="svc-icon">🧋</div>
          <h4>Non-Coffee Drinks</h4>
          <p>Matcha lattes, fruit teas, milk teas, smoothies — something for everyone.</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="svc-card">
          <div class="svc-icon">🥐</div>
          <h4>Pastries & Snacks</h4>
          <p>Fresh-baked croissants, muffins, and hearty sandwiches to pair with your drink.</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="svc-card">
          <div class="svc-icon">📱</div>
          <h4>Online Ordering</h4>
          <p>Order ahead for pick-up or delivery. Skip the queue and get your fix faster.</p>
        </div>
      </div>
    </div>

    {{-- Feature strip --}}
    <div class="row g-3 mt-4">
      @foreach([
        ['bi-lightning-charge','Fast Order','Ready in 10 min'],
        ['bi-shield-check','Safe & Secure','Trusted checkout'],
        ['bi-truck','Free Delivery','Orders over ₱500'],
        ['bi-star','Loyalty Points','Earn with every order'],
      ] as $f)
      <div class="col-6 col-md-3">
        <div style="background:var(--milk);border:1px solid var(--cream);border-radius:var(--r-lg);padding:1.25rem;text-align:center;">
          <i class="bi bi-{{ $f[0] }}" style="font-size:1.5rem;color:var(--caramel);"></i>
          <div style="font-weight:600;font-size:.9rem;color:var(--dark);margin-top:.5rem;">{{ $f[1] }}</div>
          <div style="font-size:.78rem;color:var(--chestnut);">{{ $f[2] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     MENU
════════════════════════════════════════════ --}}
<section class="bc-section bc-section-alt" id="menu" data-section="menu">
  <div class="container scroll-target" id="menu">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
      <div>
        <div class="sec-label">Our Menu</div>
        <h2 class="sec-title mb-0">Order What You Love</h2>
      </div>
      @auth
      <a href="{{ route('cart') }}" class="btn-bc d-flex align-items-center gap-2">
        <i class="bi bi-bag"></i> View Cart
        <span id="cartBadgeMenu" class="ms-1" style="background:rgba(255,255,255,.25);padding:.1rem .45rem;border-radius:50px;font-size:.75rem;"></span>
      </a>
      @else
      <a href="{{ route('login') }}" class="btn-bc-outline">Login to Order</a>
      @endauth
    </div>

    {{-- Category tabs --}}
    <div class="menu-tabs">
      <button class="menu-tab active" data-cat="all">All Items</button>
      @foreach($categories as $cat)
        <button class="menu-tab" data-cat="{{ $cat }}">{{ $cat }}</button>
      @endforeach
    </div>

    {{-- Menu grid --}}
    <div class="row g-4">
      @forelse($menuItems as $item)
      <div class="col-sm-6 col-lg-4 col-xl-3 menu-item-wrap" data-cat="{{ $item->category }}">
        <div class="menu-card">
          @if($item->image)
            <img src="{{ asset('images/menu/' . $item->image) }}" alt="{{ $item->name }}" class="menu-card-img">
          @else
            <div class="menu-img-placeholder">{{ $item->emoji ?? '☕' }}</div>
          @endif
          <div class="menu-card-body">
            <div class="menu-card-cat">{{ $item->category }}</div>
            <h5>{{ $item->name }}</h5>
            <p>{{ $item->description }}</p>
            <div class="menu-card-foot">
              <span class="menu-price">₱{{ number_format($item->price, 2) }}</span>
              @auth
                @if($item->is_available)
                  <button
                    class="btn-bc btn-bc-sm add-to-cart-btn"
                    data-id="{{ $item->id }}"
                    data-name="{{ $item->name }}"
                    data-price="{{ $item->price }}"
                    data-image="{{ $item->image ? asset('images/menu/'.$item->image) : '' }}">
                    <i class="bi bi-plus"></i> Add
                </button>
                @else
                  <span style="font-size:.75rem;color:var(--danger);">Sold Out</span>
                @endif
              @else
                <a href="{{ route('login') }}" class="btn-bc-outline btn-bc-sm">Order</a>
              @endauth
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center py-5">
        <div style="font-size:3rem;">☕</div>
        <p class="mt-3" style="color:var(--chestnut);">Menu coming soon. Check back later!</p>
      </div>
      @endforelse
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     CONTACT
════════════════════════════════════════════ --}}
<section class="bc-section bc-section-dark" id="contact" data-section="contact">
  <div class="container scroll-target" id="contact">
    <div class="row g-5">
      <div class="col-lg-5">
        <div class="sec-label">Get in Touch</div>
        <h2 class="sec-title">We'd Love to Hear From You</h2>
        <p class="sec-sub mt-3">Questions, catering inquiries, or just want to say hi — our team usually replies within a few hours.</p>

        <div class="mt-4">
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-geo-alt"></i></div>
            <div>
              <strong>Address</strong>
              <span>123 Aurora Boulevard, Barangay New Manila
Quezon City, Metro Manila, Philippines 1112</span>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-clock"></i></div>
            <div>
              <strong>Hours</strong>
              <span>Mon–Fri: 7AM–9PM &nbsp;·&nbsp; Sat–Sun:8AM–10PM</span>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-envelope"></i></div>
            <div>
              <strong>Email</strong>
              <span>hello@brewcraft.ph</span>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-telephone"></i></div>
            <div>
              <strong>Phone</strong>
              <span>+63 912 345 6789</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:var(--r-xl);padding:2.25rem;">
          @if(session('contact_success'))
            <div class="bc-alert bc-alert-success">
              <i class="bi bi-check-circle me-2"></i>{{ session('contact_success') }}
            </div>
          @endif

          <form action="{{ route('contact.store') }}" method="POST">
            @csrf
            <div class="row g-3">
              <div class="col-sm-6">
                <div class="bc-form-group">
                  <label class="bc-label" style="color:rgba(255,255,255,.6);">Full Name</label>
                  <input type="text" name="name" class="bc-input @error('name') is-invalid @enderror"
                    placeholder="Juan Dela Cruz" value="{{ old('name') }}" required>
                  @error('name')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-sm-6">
                <div class="bc-form-group">
                  <label class="bc-label" style="color:rgba(255,255,255,.6);">Email Address</label>
                  <input type="email" name="email" class="bc-input @error('email') is-invalid @enderror"
                    placeholder="youremail@email.com" value="{{ old('email') }}" required>
                  @error('email')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-12">
                <div class="bc-form-group">
                  <label class="bc-label" style="color:rgba(255,255,255,.6);">Subject</label>
                  <input type="text" name="subject" class="bc-input @error('subject') is-invalid @enderror"
                    placeholder="How can we help?" value="{{ old('subject') }}" required>
                  @error('subject')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-12">
                <div class="bc-form-group">
                  <label class="bc-label" style="color:rgba(255,255,255,.6);">Message</label>
                  <textarea name="message" rows="4" class="bc-textarea @error('message') is-invalid @enderror"
                    placeholder="Tell us more…" required>{{ old('message') }}</textarea>
                  @error('message')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-bc-gold btn-bc-lg w-100">
                  <i class="bi bi-send"></i> Send Message
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
