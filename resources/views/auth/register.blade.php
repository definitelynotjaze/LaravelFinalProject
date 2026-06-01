@extends('layouts.app')
@section('title', 'Register')

@section('content')
<div class="auth-bg" style="padding-top: 110px;">
  <div class="auth-card" style="max-width:500px;">
    <div class="auth-brand">
      <span class="name">Brew<em>Craft</em></span>
      <p>Create your free account to start ordering</p>
    </div>

    @if(session('error'))
      <div class="bc-alert bc-alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('register') }}" method="POST">
      @csrf

      <div class="row g-3">
        <div class="col-sm-6">
          <div class="bc-form-group">
            <label class="bc-label">First Name</label>
            <input type="text" name="first_name"
              class="bc-input @error('first_name') is-invalid @enderror"
              value="{{ old('first_name') }}" placeholder="Juan" required>
            @error('first_name')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-sm-6">
          <div class="bc-form-group">
            <label class="bc-label">Last Name</label>
            <input type="text" name="last_name"
              class="bc-input @error('last_name') is-invalid @enderror"
              value="{{ old('last_name') }}" placeholder="Dela Cruz" required>
            @error('last_name')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      <div class="bc-form-group">
        <label class="bc-label">Email Address</label>
        <input type="email" name="email"
          class="bc-input @error('email') is-invalid @enderror"
          value="{{ old('email') }}" placeholder="youremail@email.com" required>
        @error('email')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="bc-form-group">
        <label class="bc-label">Phone Number</label>
        <input type="text" name="phone"
          class="bc-input @error('phone') is-invalid @enderror"
          value="{{ old('phone') }}" placeholder="0987 654 3210">
        @error('phone')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="row g-3">
        <div class="col-sm-6">
          <div class="bc-form-group">
            <label class="bc-label">Password</label>
            <div style="position:relative;">
              <input type="password" name="password" id="regPw"
                class="bc-input @error('password') is-invalid @enderror"
                placeholder="Min 8 characters" required>
              <button type="button" onclick="togglePw('regPw','eyeReg')"
                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--latte);cursor:pointer;">
                <i class="bi bi-eye" id="eyeReg"></i>
              </button>
            </div>
            @error('password')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="col-sm-6">
          <div class="bc-form-group">
            <label class="bc-label">Confirm Password</label>
            <input type="password" name="password_confirmation"
              class="bc-input" placeholder="Repeat Password" required>
          </div>
        </div>
      </div>

      <button type="submit" class="btn-bc btn-bc-lg w-100" style="display:flex;align-items:center;justify-content:center;gap:.4rem;">
        <i class="bi bi-person-plus"></i> Create Account
      </button>
    </form>

    <div class="auth-divider"><span>Already have an account?</span></div>

    <p style="text-align:center;font-size:.88rem;color:var(--chestnut);">
      <a href="{{ route('login') }}" style="color:var(--mahogany);font-weight:600;">Log In</a>
    </p>

    <p style="text-align:center;margin-top:1rem;">
      <a href="{{ route('home') }}" style="font-size:.8rem;color:var(--latte);">
        <i class="bi bi-arrow-left"></i> Back to site
      </a>
    </p>
  </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(id, iconId) {
  const inp  = document.getElementById(id);
  const icon = document.getElementById(iconId);
  if (inp.type === 'password') { inp.type = 'text';     icon.className = 'bi bi-eye-slash'; }
  else                         { inp.type = 'password'; icon.className = 'bi bi-eye'; }
}
</script>
@endpush
