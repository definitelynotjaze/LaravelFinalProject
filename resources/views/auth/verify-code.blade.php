@extends('layouts.app')
@section('title', 'Enter Verification Code')

@section('content')
<div class="auth-bg" style="padding-top: 110px;">
  <div class="auth-card" style="max-width: 420px;">
    <div class="auth-brand">
      <div style="width:60px;height:60px;background:var(--mahogany);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
        <i class="bi bi-shield-lock" style="font-size:1.6rem;color:#fff;"></i>
      </div>
      <span class="name">Brew<em>Craft</em></span>
      <p style="margin-top:.4rem;">We sent a 6-digit code to</p>
      <p style="font-weight:700;color:var(--mahogany);font-size:.92rem;margin-top:-.4rem;">{{ session('reset_email') }}</p>
    </div>

    @if(session('error'))
      <div class="bc-alert bc-alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('password.verify-code') }}" method="POST" id="codeForm">
      @csrf
      {{-- Hidden input that collects the full code for submission --}}
      <input type="hidden" name="code" id="hiddenCode">

      {{-- OTP digit boxes --}}
      <div style="display:flex;gap:10px;justify-content:center;margin:1.5rem 0 .5rem;">
        @for($i = 0; $i < 6; $i++)
        <div style="
          display:flex;
          flex-direction:column;
          align-items:center;
          justify-content:flex-end;
          width:48px;
          height:68px;
          border:2px solid var(--cream);
          border-radius:10px;
          background:var(--milk);
          position:relative;
          transition:border-color .2s, box-shadow .2s;
        " class="otp-box" id="box{{ $i }}">
          <span class="otp-digit" id="digit{{ $i }}" style="
            font-size:1.75rem;
            font-weight:700;
            color:var(--mahogany);
            font-family:var(--font-serif, Georgia, serif);
            line-height:2;
            margin-bottom:8px;
          "></span>
          <div style="
            position:absolute;
            bottom:8px;
            width:24px;
            height:2px;
            background:var(--latte);
            border-radius:2px;
            transition:background .2s;
          " class="otp-cursor" id="cursor{{ $i }}"></div>
        </div>
        @endfor
      </div>

      @error('code')
        <div style="text-align:center;color:#dc3545;font-size:.82rem;margin-bottom:.75rem;">{{ $message }}</div>
      @enderror

      <div style="text-align:center;font-size:.75rem;color:var(--chestnut);margin-bottom:1.25rem;">
        <i class="bi bi-clock me-1"></i> Code expires in <span id="countdown" style="font-weight:700;color:var(--caramel);">15:00</span>
      </div>

      <button type="submit" class="btn-bc btn-bc-lg w-100" id="submitBtn"
        style="display:flex;align-items:center;justify-content:center;gap:.4rem;opacity:.5;pointer-events:none;">
        <i class="bi bi-shield-check"></i> Verify Code
      </button>
    </form>

    <p style="text-align:center;margin-top:1.25rem;font-size:.82rem;color:var(--chestnut);">
      Didn't receive it?
      <a href="{{ route('password.request') }}" style="color:var(--caramel);font-weight:600;text-decoration:none;">Resend Code</a>
    </p>

    <p style="text-align:center;margin-top:.5rem;">
      <a href="{{ route('login') }}" style="font-size:.8rem;color:var(--latte);text-decoration:none;">
        <i class="bi bi-arrow-left"></i> Back to Login
      </a>
    </p>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const DIGITS  = 6;
  const digits  = [];   // stored characters
  let   cursor  = 0;    // which box is "active"

  // ── Countdown timer ─────────────────────────────────────────────
  let seconds = 15 * 60;
  const countEl = document.getElementById('countdown');
  const timer = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
      clearInterval(timer);
      countEl.textContent = 'Expired';
      countEl.style.color = '#dc3545';
      return;
    }
    const m = String(Math.floor(seconds / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    countEl.textContent = m + ':' + s;
  }, 1000);

  // ── Render boxes ────────────────────────────────────────────────
  function render() {
    for (let i = 0; i < DIGITS; i++) {
      const box    = document.getElementById('box' + i);
      const span   = document.getElementById('digit' + i);
      const cur    = document.getElementById('cursor' + i);
      const filled = digits[i] !== undefined;
      const active = i === cursor;

      span.textContent = filled ? digits[i] : '';

      // box border
      if (active) {
        box.style.borderColor = 'var(--mahogany)';
        box.style.boxShadow   = '0 0 0 3px rgba(120,50,20,.15)';
        box.style.background  = '#fff';
      } else if (filled) {
        box.style.borderColor = 'var(--caramel)';
        box.style.boxShadow   = 'none';
        box.style.background  = 'var(--milk)';
      } else {
        box.style.borderColor = 'var(--cream)';
        box.style.boxShadow   = 'none';
        box.style.background  = 'var(--milk)';
      }

      // blinking cursor line: show only when active and box is empty
      cur.style.display  = (active && !filled) ? 'block' : 'none';
      cur.style.background = active ? 'var(--mahogany)' : 'var(--latte)';
    }

    // Enable submit only when all 6 digits filled
    const btn = document.getElementById('submitBtn');
    const full = digits.filter(d => d !== undefined).length === DIGITS;
    btn.style.opacity       = full ? '1'    : '.5';
    btn.style.pointerEvents = full ? 'auto' : 'none';
  }

  // ── Global keydown listener ──────────────────────────────────────
  document.addEventListener('keydown', function (e) {
    // Digits 0-9
    if (/^[0-9]$/.test(e.key)) {
      if (cursor < DIGITS) {
        digits[cursor] = e.key;
        cursor = Math.min(cursor + 1, DIGITS);
        render();
      }
      return;
    }

    // Backspace
    if (e.key === 'Backspace') {
      if (cursor > 0 && digits[cursor - 1] !== undefined) {
        cursor--;
        digits[cursor] = undefined;
      } else if (cursor === DIGITS && digits[DIGITS - 1] !== undefined) {
        cursor = DIGITS - 1;
        digits[cursor] = undefined;
      }
      render();
      return;
    }

    // Arrow keys
    if (e.key === 'ArrowLeft')  { cursor = Math.max(0, cursor - 1); render(); }
    if (e.key === 'ArrowRight') { cursor = Math.min(DIGITS - 1, cursor + 1); render(); }

    // Enter = submit if full
    if (e.key === 'Enter') {
      trySubmit();
    }
  });

  // Click a box to move cursor there
  for (let i = 0; i < DIGITS; i++) {
    document.getElementById('box' + i).addEventListener('click', () => {
      cursor = i;
      render();
    });
  }

  // Handle paste (e.g. from email client)
  document.addEventListener('paste', function (e) {
    const text = (e.clipboardData || window.clipboardData).getData('text');
    const nums  = text.replace(/\D/g, '').slice(0, DIGITS);
    for (let i = 0; i < nums.length; i++) digits[i] = nums[i];
    cursor = Math.min(nums.length, DIGITS);
    render();
    e.preventDefault();
  });

  // ── Submit ───────────────────────────────────────────────────────
  function trySubmit() {
    if (digits.filter(d => d !== undefined).length < DIGITS) return;
    document.getElementById('hiddenCode').value = digits.join('');
    document.getElementById('codeForm').submit();
  }

  document.getElementById('codeForm').addEventListener('submit', function (e) {
    e.preventDefault();
    trySubmit();
  });

  // ── Blinking cursor animation ────────────────────────────────────
  let blink = true;
  setInterval(() => {
    blink = !blink;
    const cur = document.getElementById('cursor' + cursor);
    if (cur && digits[cursor] === undefined) {
      cur.style.opacity = blink ? '1' : '0';
    }
  }, 500);

  // Initial render
  render();
})();
</script>
@endpush
