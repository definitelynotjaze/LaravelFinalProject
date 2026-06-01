@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="profile-wrap">
  <div class="container py-5">
    <div class="row g-4">

      {{-- Left: Avatar + quick info --}}
      <div class="col-lg-4 col-xl-3">
        <div class="bc-card mb-4">
          <div class="bc-card-body" style="text-align:center;padding:2rem 1.5rem;">
            {{-- Avatar --}}
            <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" id="pfpForm">
              @csrf @method('PATCH')
              <div style="display:inline-block;position:relative;margin-bottom:1.25rem;">
                <div class="profile-avatar-ring">
                  @if(auth()->user()->profile_photo)
                    <img src="{{ asset('images/profiles/'.auth()->user()->profile_photo) }}"
                      id="pfpPreview" alt="{{ auth()->user()->name }}">
                  @else
                    <img id="pfpPreview" src="" alt="" style="display:none;">
                    <div class="avatar-init" id="pfpInitials">
                      {{ strtoupper(substr(auth()->user()->name,0,2)) }}
                    </div>
                  @endif
                </div>
                <label class="profile-avatar-edit" title="Change photo">
                  <i class="bi bi-camera-fill"></i>
                  <input type="file" name="profile_photo" id="pfpInput" accept="image/*" style="display:none;"
                    onchange="this.form.submit()">
                </label>
              </div>
            </form>

            <h5 style="font-size:1.15rem;margin-bottom:.2rem;">{{ auth()->user()->name }}</h5>
            <div style="font-size:.82rem;color:var(--chestnut);margin-bottom:.75rem;">{{ auth()->user()->email }}</div>
            <span class="badge-role {{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>

            <div class="divider"></div>

            <div class="d-flex justify-content-around text-center">
              <div>
                <div style="font-family:var(--ff-serif);font-size:1.4rem;font-weight:700;color:var(--mahogany);">{{ $orderCount }}</div>
                <div style="font-size:.75rem;color:var(--chestnut);">Orders</div>
              </div>
              <div>
                <div style="font-family:var(--ff-serif);font-size:1.4rem;font-weight:700;color:var(--mahogany);">₱{{ number_format($totalSpent,0) }}</div>
                <div style="font-size:.75rem;color:var(--chestnut);">Total Spent</div>
              </div>
            </div>

            <div class="divider"></div>

            <div style="font-size:.78rem;color:var(--chestnut);">
              <i class="bi bi-calendar3 me-1"></i>
              Member since {{ auth()->user()->created_at->format('F Y') }}
            </div>
          </div>
        </div>

        {{-- Nav links --}}
        <div class="bc-card">
          <div class="bc-card-body" style="padding:.5rem;">
            @foreach([
              ['#profile-info',    'bi-person',         'Personal Info'],
              ['#profile-security','bi-shield-lock',    'Password & Security'],
              ['#profile-address', 'bi-geo-alt',        'Delivery Address'],
              ['#profile-prefs',   'bi-bell',           'Preferences'],
            ] as [$href,$icon,$label])
            <a href="{{ $href }}" style="display:flex;align-items:center;gap:.7rem;padding:.65rem .85rem;border-radius:var(--r);color:var(--chestnut);text-decoration:none;font-size:.88rem;font-weight:500;transition:all .2s;"
              onmouseover="this.style.background='var(--milk)';this.style.color='var(--dark)';"
              onmouseout="this.style.background='';this.style.color='var(--chestnut)';">
              <i class="bi {{ $icon }}" style="width:18px;text-align:center;"></i>
              {{ $label }}
            </a>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Right: Forms --}}
      <div class="col-lg-8 col-xl-9">

        @if(session('success'))
          <div class="bc-alert bc-alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
        @endif

        {{-- Personal Info --}}
        <div class="bc-card mb-4" id="profile-info">
          <div class="bc-card-head"><h5><i class="bi bi-person me-2"></i>Personal Information</h5></div>
          <div class="bc-card-body">
            <form action="{{ route('profile.update') }}" method="POST">
              @csrf @method('PATCH')
              <div class="row g-3">
                <div class="col-sm-6">
                  <div class="bc-form-group">
                    <label class="bc-label">First Name</label>
                    <input type="text" name="first_name" class="bc-input @error('first_name') is-invalid @enderror"
                      value="{{ old('first_name', auth()->user()->first_name) }}" required>
                    @error('first_name')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="bc-form-group">
                    <label class="bc-label">Last Name</label>
                    <input type="text" name="last_name" class="bc-input @error('last_name') is-invalid @enderror"
                      value="{{ old('last_name', auth()->user()->last_name) }}" required>
                    @error('last_name')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="bc-form-group">
                    <label class="bc-label">Email Address</label>
                    <input type="email" name="email" class="bc-input @error('email') is-invalid @enderror"
                      value="{{ old('email', auth()->user()->email) }}" required>
                    @error('email')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="bc-form-group">
                    <label class="bc-label">Phone Number</label>
                    <input type="text" name="phone" class="bc-input @error('phone') is-invalid @enderror"
                      value="{{ old('phone', auth()->user()->phone) }}" placeholder="+63 917 000 0000">
                    @error('phone')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-12">
                  <div class="bc-form-group">
                    <label class="bc-label">Bio <span style="color:var(--latte);font-weight:400;">(optional)</span></label>
                    <textarea name="bio" rows="2" class="bc-textarea" placeholder="A little about yourself…">{{ old('bio', auth()->user()->bio) }}</textarea>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn-bc"><i class="bi bi-check-circle"></i> Save Changes</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        {{-- Password --}}
        <div class="bc-card mb-4" id="profile-security">
          <div class="bc-card-head"><h5><i class="bi bi-shield-lock me-2"></i>Password & Security</h5></div>
          <div class="bc-card-body">
            <form action="{{ route('profile.password') }}" method="POST">
              @csrf @method('PATCH')
              <div class="row g-3">
                <div class="col-sm-6">
                  <div class="bc-form-group">
                    <label class="bc-label">Current Password</label>
                    <input type="password" name="current_password" class="bc-input @error('current_password') is-invalid @enderror" required>
                    @error('current_password')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                  <div class="bc-form-group">
                    <label class="bc-label">New Password</label>
                    <input type="password" name="password" class="bc-input @error('password') is-invalid @enderror"
                      placeholder="Min 8 characters" required>
                    @error('password')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="bc-form-group">
                    <label class="bc-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="bc-input" required>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn-bc"><i class="bi bi-lock"></i> Update Password</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        {{-- Address --}}
        <div class="bc-card mb-4" id="profile-address">
          <div class="bc-card-head"><h5><i class="bi bi-geo-alt me-2"></i>Delivery Address</h5></div>
          <div class="bc-card-body">
            <form action="{{ route('profile.address') }}" method="POST">
              @csrf @method('PATCH')
              <div class="row g-3">
                <div class="col-12">
                  <div class="bc-form-group">
                    <label class="bc-label">Street Address</label>
                    <input type="text" name="address_line" class="bc-input"
                      value="{{ old('address_line', auth()->user()->address_line) }}"
                      placeholder="123 Example St., Brgy. Sample">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="bc-form-group">
                    <label class="bc-label">City</label>
                    <input type="text" name="city" class="bc-input"
                      value="{{ old('city', auth()->user()->city) }}" placeholder="Makati">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="bc-form-group">
                    <label class="bc-label">Province</label>
                    <input type="text" name="province" class="bc-input"
                      value="{{ old('province', auth()->user()->province) }}" placeholder="Metro Manila">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="bc-form-group">
                    <label class="bc-label">ZIP Code</label>
                    <input type="text" name="zip_code" class="bc-input"
                      value="{{ old('zip_code', auth()->user()->zip_code) }}" placeholder="1200">
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn-bc"><i class="bi bi-check-circle"></i> Save Address</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        {{-- Preferences --}}
        <div class="bc-card" id="profile-prefs">
          <div class="bc-card-head"><h5><i class="bi bi-bell me-2"></i>Preferences</h5></div>
          <div class="bc-card-body">
            <form action="{{ route('profile.preferences') }}" method="POST">
              @csrf @method('PATCH')
              <div class="d-flex flex-column gap-3">
                @foreach([
                  ['email_orders',     'Order status email notifications'],
                  ['email_promos',     'Promotional emails & special offers'],
                  ['sms_notifications','SMS order updates'],
                ] as [$key, $label])
                <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid var(--cream);">
                  <label for="pref_{{ $key }}" style="font-size:.9rem;color:var(--dark);cursor:pointer;">{{ $label }}</label>
                  <input type="checkbox" name="{{ $key }}" id="pref_{{ $key }}"
                    value="1" {{ auth()->user()->preferences[$key] ?? true ? 'checked' : '' }}
                    style="width:18px;height:18px;accent-color:var(--mahogany);cursor:pointer;">
                </div>
                @endforeach
                <div>
                  <button type="submit" class="btn-bc btn-bc-sm"><i class="bi bi-check-circle"></i> Save Preferences</button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
