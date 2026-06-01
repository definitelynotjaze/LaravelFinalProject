@extends('layouts.app')
@section('title', 'Reset Password')

@section('content')
<div class="auth-bg" style="padding-top: 110px;">
  <div class="auth-card">
    <div class="auth-brand">
      <span class="name">Brew<em>Craft</em></span>
      <p>Set your new password below.</p>
    </div>

    @if(session('error'))
      <div class="bc-alert bc-alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ session('reset_token') }}">

      <div class="bc-form-group">
        <label class="bc-label">New Password</label>
        <div style="position:relative;">
          <input type="password" name="password" id="newPw"
            class="bc-input @error('password') is-invalid @enderror"
            placeholder="Min 8 characters" required>
          <button type="button" onclick="togglePw('newPw','eyeNew')"
            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--latte);cursor:pointer;">
            <i class="bi bi-eye" id="eyeNew"></i>
          </button>
        </div>
        @error('password')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="bc-form-group">
        <label class="bc-label">Confirm New Password</label>
        <input type="password" name="password_confirmation"
          class="bc-input" placeholder="Repeat new password" required>
      </div>

      <button type="submit" class="btn-bc btn-bc-lg w-100" style="display:flex;align-items:center;justify-content:center;gap:.4rem;">
        <i class="bi bi-lock"></i> Reset Password
      </button>
    </form>
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
