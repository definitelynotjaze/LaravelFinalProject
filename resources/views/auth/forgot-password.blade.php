@extends('layouts.app')
@section('title', 'Forgot Password')

@section('content')
<div class="auth-bg" style="padding-top: 110px;">
  <div class="auth-card">
    <div class="auth-brand">
      <span class="name">Brew<em>Craft</em></span>
      <p>Enter your email and we'll send a verification code.</p>
    </div>

    @if(session('error'))
      <div class="bc-alert bc-alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
      <div class="bc-alert bc-alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
      @csrf
      <div class="bc-form-group">
        <label class="bc-label">Email Address</label>
        <input type="email" name="email"
          class="bc-input @error('email') is-invalid @enderror"
          value="{{ old('email') }}"
          placeholder="youremail@email.com" autofocus required>
        @error('email')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <button type="submit" class="btn-bc btn-bc-lg w-100" style="display:flex;align-items:center;justify-content:center;gap:.4rem;">
        <i class="bi bi-envelope"></i> Send Verification Code
      </button>
    </form>

    <p style="text-align:center;margin-top:1.5rem;">
      <a href="{{ route('login') }}" style="font-size:.85rem;color:var(--latte);">
        <i class="bi bi-arrow-left"></i> Back to Login
      </a>
    </p>
  </div>
</div>
@endsection
