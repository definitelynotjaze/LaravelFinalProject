@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="auth-bg" style="padding-top: 110px;">
  <div class="auth-card">
    <div class="auth-brand">
      <span class="name">Brew<em>Craft</em></span>
      <p>Welcome to BrewCraft! Sign in to continue.</p>
    </div>

    @if(session('error'))
      <div class="bc-alert bc-alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
      <div class="bc-alert bc-alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('login') }}" method="POST">
      @csrf

      <div class="bc-form-group">
        <label class="bc-label">Email Address</label>
        <input type="email" name="email"
          class="bc-input @error('email') is-invalid @enderror"
          value="{{ old('email') }}"
          placeholder="youremail@email.com" autofocus required>
        @error('email')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="bc-form-group">
        <label class="bc-label">Password</label>
        <div style="position:relative;">
          <input type="password" name="password" id="loginPw"
            class="bc-input @error('password') is-invalid @enderror"
            placeholder="Enter your password" required>
          <button type="button" onclick="togglePw('loginPw','eyeLogin')"
            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--latte);cursor:pointer;">
            <i class="bi bi-eye" id="eyeLogin"></i>
          </button>
        </div>
        @error('password')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
      </div>

      {{-- Remember me + Forgot password on same row --}}
      <div class="d-flex align-items-center justify-content-between mb-4" style="font-size:.85rem;">
        <label class="d-flex align-items-center gap-2" for="rememberMe" style="cursor:pointer;color:var(--chestnut);margin:0;">
          <input type="checkbox" name="remember" id="rememberMe"
            style="width:16px;height:16px;accent-color:var(--mahogany);"
            {{ old('remember') ? 'checked' : '' }}>
          Remember me
        </label>
        <a href="{{ route('password.request') }}" style="color:var(--caramel);font-size:.78rem;text-decoration:none;">
          Forgot password?
        </a>
      </div>

      <button type="submit" class="btn-bc btn-bc-lg w-100" style="display:flex;align-items:center;justify-content:center;gap:.4rem;">
        <i class="bi bi-box-arrow-in-right"></i> Log In
      </button>
    </form>

    <div class="auth-divider"><span>or</span></div>

    <p style="text-align:center;font-size:.88rem;color:var(--chestnut);">
      Don't have an account?
      <a href="{{ route('register') }}" style="color:var(--mahogany);font-weight:600;">Sign Up</a>
    </p>

    <p style="text-align:center;margin-top:1.5rem;">
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
