@extends('layouts.admin')
@section('title', 'Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT))
@section('page-title', 'Order Detail')

@section('content')
@php
  $isAdmin = auth()->user()->role === 'admin';
  $ordersRoute      = $isAdmin ? 'admin.orders'       : 'staff.orders';
  $orderStatusRoute = $isAdmin ? 'admin.orders.status' : 'staff.orders.status';
  $statusFlow  = ['pending'=>'confirmed','confirmed'=>'preparing','preparing'=>'ready','ready'=>'completed'];
  $nextLabels  = ['confirmed'=>'Confirm Order','preparing'=>'Start Preparing','ready'=>'Mark Ready','completed'=>'Complete Order'];
  $nextIcons   = ['confirmed'=>'check-circle','preparing'=>'fire','ready'=>'bag-check','completed'=>'trophy'];
  $nextStatus  = $statusFlow[$order->status] ?? null;
@endphp

{{-- Page header --}}
<div class="bc-page-head d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h2>Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
    <p>Placed {{ $order->created_at->format('F j, Y') }} at {{ $order->created_at->format('g:i A') }} &mdash; {{ $order->created_at->diffForHumans() }}</p>
  </div>
  <a href="{{ route($ordersRoute) }}" class="btn-bc-ghost btn-bc-sm">
    <i class="bi bi-arrow-left me-1"></i> Back to Orders
  </a>
</div>

{{-- Flash messages --}}
@if(session('success'))
  <div class="bc-alert bc-alert-success mb-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif

<div class="row g-4">

  {{-- LEFT: Items + Summary --}}
  <div class="col-lg-8">

    {{-- Order Items --}}
    <div class="bc-card mb-4">
      <div class="bc-card-head">
        <h5><i class="bi bi-cup-hot me-2"></i>Items Ordered</h5>
        <span style="font-size:.8rem;color:var(--chestnut);">{{ $order->items->count() }} item(s)</span>
      </div>
      <div class="bc-table-wrap">
        <table class="bc-table">
          <thead>
            <tr>
              <th>Item</th>
              <th style="text-align:center;">Qty</th>
              <th style="text-align:right;">Unit Price</th>
              <th style="text-align:right;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr>
              <td>
                <div style="font-weight:500;font-size:.9rem;">{{ $item->menu_item_name }}</div>
                @if($item->menuItem)
                  <div style="font-size:.75rem;color:var(--chestnut);">{{ ucfirst($item->menuItem->category ?? '') }}</div>
                @endif
              </td>
              <td style="text-align:center;">
                <span style="font-size:.85rem;background:var(--cream);padding:.15rem .55rem;border-radius:50px;">{{ $item->quantity }}</span>
              </td>
              <td style="text-align:right;font-size:.88rem;">₱{{ number_format($item->price, 2) }}</td>
              <td style="text-align:right;font-weight:600;">₱{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Totals --}}
      <div style="padding:1rem 1.5rem;border-top:2px dashed var(--cream);">
        <div class="d-flex justify-content-between" style="font-size:.88rem;color:var(--chestnut);margin-bottom:.4rem;">
          <span>Subtotal</span>
          <span>₱{{ number_format($order->subtotal ?? ($order->total - ($order->delivery_fee ?? 0)), 2) }}</span>
        </div>
        <div class="d-flex justify-content-between" style="font-size:.88rem;color:var(--chestnut);margin-bottom:.75rem;">
          <span>Delivery Fee</span>
          <span>{{ ($order->delivery_fee ?? 0) > 0 ? '₱'.number_format($order->delivery_fee, 2) : 'Free' }}</span>
        </div>
        <div class="d-flex justify-content-between" style="font-size:1.15rem;font-weight:700;color:var(--espresso);font-family:var(--ff-serif);">
          <span>Total</span>
          <span>₱{{ number_format($order->total, 2) }}</span>
        </div>
      </div>
    </div>

    {{-- Notes --}}
    @if($order->notes)
    <div class="bc-card mb-4">
      <div class="bc-card-head">
        <h5><i class="bi bi-chat-left-text me-2"></i>Customer Notes</h5>
      </div>
      <div class="bc-card-body">
        <p style="margin:0;font-size:.9rem;color:var(--dark);line-height:1.6;">{{ $order->notes }}</p>
      </div>
    </div>
    @endif

  </div>

  {{-- RIGHT: Status + Customer + Payment --}}
  <div class="col-lg-4">

    {{-- Status & Actions --}}
    <div class="bc-card mb-4">
      <div class="bc-card-head">
        <h5><i class="bi bi-activity me-2"></i>Order Status</h5>
      </div>
      <div class="bc-card-body">

        {{-- Current status badge --}}
        <div class="d-flex align-items-center gap-2 mb-4">
          <span id="status-badge" class="badge-status {{ $order->status }}" style="font-size:.85rem;padding:.35rem .9rem;border-radius:50px;font-weight:600;">
            {{ ucfirst($order->status) }}
          </span>
        </div>

        {{-- Status progress track --}}
        <div style="margin-bottom:1.5rem;">
          @php $steps = ['pending','confirmed','preparing','ready','completed']; @endphp
          @foreach($steps as $i => $step)
          @php
            $isDone   = array_search($order->status, $steps) > $i || $order->status === $step;
            $isActive = $order->status === $step;
            $dotColor = $isActive ? 'var(--mahogany)' : ($isDone ? 'var(--success)' : '#ddd');
            $textColor= $isActive ? 'var(--espresso)' : ($isDone ? 'var(--dark)' : '#aaa');
          @endphp
          <div class="d-flex align-items-center gap-2 mb-2" id="step-{{ $step }}">
            <div style="width:10px;height:10px;border-radius:50%;background:{{ $dotColor }};flex-shrink:0;transition:background .3s;"></div>
            <span style="font-size:.82rem;font-weight:{{ $isActive ? '700' : '400' }};color:{{ $textColor }};transition:color .3s;">
              {{ ucfirst($step) }}
            </span>
            @if($isActive)
              <span style="font-size:.7rem;background:var(--cream);color:var(--chestnut);padding:.1rem .45rem;border-radius:50px;margin-left:auto;">current</span>
            @elseif($isDone && $step !== $order->status)
              <i class="bi bi-check" style="color:var(--success);margin-left:auto;font-size:.8rem;"></i>
            @endif
          </div>
          @if(!$loop->last)
            <div style="width:2px;height:12px;background:{{ $isDone ? 'var(--success)' : '#eee' }};margin-left:4px;margin-bottom:2px;transition:background .3s;"></div>
          @endif
          @endforeach

          @if($order->status === 'cancelled')
          <div class="d-flex align-items-center gap-2 mt-2">
            <div style="width:10px;height:10px;border-radius:50%;background:var(--danger);flex-shrink:0;"></div>
            <span style="font-size:.82rem;font-weight:700;color:var(--danger);">Cancelled</span>
          </div>
          @endif
        </div>

        {{-- Action buttons --}}
        <div class="d-flex flex-column gap-2" id="action-buttons">
          @if($nextStatus)
            <button id="btn-advance"
              class="btn-bc w-100 justify-content-center"
              data-next="{{ $nextStatus }}"
              data-url="{{ route($orderStatusRoute, $order) }}"
              style="display:flex;align-items:center;gap:.4rem;">
              <i class="bi bi-{{ $nextIcons[$nextStatus] }}"></i> {{ $nextLabels[$nextStatus] }}
            </button>
          @endif

          @if(!in_array($order->status, ['completed','cancelled']))
            <button id="btn-cancel"
              class="btn-bc-danger btn-bc w-100 justify-content-center"
              data-url="{{ route($orderStatusRoute, $order) }}"
              style="display:flex;align-items:center;gap:.4rem;">
              <i class="bi bi-x-circle"></i> Cancel Order
            </button>
          @endif

          @if(in_array($order->status, ['completed','cancelled']))
            <div style="text-align:center;font-size:.82rem;color:var(--chestnut);padding:.5rem 0;">
              <i class="bi bi-lock me-1"></i>This order is {{ $order->status }}.
            </div>
          @endif
        </div>

      </div>
    </div>

    {{-- Customer Info --}}
    <div class="bc-card mb-4">
      <div class="bc-card-head">
        <h5><i class="bi bi-person me-2"></i>Customer</h5>
      </div>
      <div class="bc-card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="avatar-sm" style="width:42px;height:42px;font-size:.85rem;flex-shrink:0;">
            {{ strtoupper(substr($order->user->name ?? 'G', 0, 2)) }}
          </div>
          <div>
            <div style="font-weight:600;font-size:.95rem;">{{ $order->user->name ?? 'Guest' }}</div>
            <div style="font-size:.78rem;color:var(--chestnut);">{{ $order->user->email ?? '—' }}</div>
          </div>
        </div>
        @if($order->user)
          <div style="font-size:.82rem;color:var(--chestnut);">
            <i class="bi bi-receipt me-1"></i>
            {{ $order->user->orders()->count() }} total order(s)
          </div>
        @endif
      </div>
    </div>

    {{-- Order Meta --}}
    <div class="bc-card">
      <div class="bc-card-head">
        <h5><i class="bi bi-info-circle me-2"></i>Order Info</h5>
      </div>
      <div class="bc-card-body">
        <div style="font-size:.85rem;display:flex;flex-direction:column;gap:.65rem;">
          <div class="d-flex justify-content-between">
            <span style="color:var(--chestnut);">Type</span>
            <span style="font-weight:500;">{{ ucfirst($order->type ?? 'pickup') }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <span style="color:var(--chestnut);">Payment</span>
            <span style="font-weight:500;">{{ ucfirst(str_replace('_',' ', $order->payment_method ?? 'cash')) }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <span style="color:var(--chestnut);">Payment Status</span>
            <span class="badge-status {{ $order->payment_status === 'paid' ? 'completed' : 'pending' }}" style="font-size:.75rem;padding:.15rem .55rem;">
              {{ ucfirst($order->payment_status ?? 'pending') }}
            </span>
          </div>
          @if($order->payment_reference)
          <div class="d-flex justify-content-between">
            <span style="color:var(--chestnut);">Reference</span>
            <span style="font-weight:500;font-size:.78rem;font-family:monospace;">{{ $order->payment_reference }}</span>
          </div>
          @endif
          <div class="d-flex justify-content-between">
            <span style="color:var(--chestnut);">Placed</span>
            <span>{{ $order->created_at->format('M d, Y g:i A') }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <span style="color:var(--chestnut);">Last Updated</span>
            <span>{{ $order->updated_at->format('M d, Y g:i A') }}</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
const statusFlow = { pending:'confirmed', confirmed:'preparing', preparing:'ready', ready:'completed' };
const nextLabels = { confirmed:'Confirm Order', preparing:'Start Preparing', ready:'Mark Ready', completed:'Complete Order' };
const nextIcons  = { confirmed:'check-circle', preparing:'fire', ready:'bag-check', completed:'trophy' };
const steps      = ['pending','confirmed','preparing','ready','completed'];

const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

function setLoading(btn, loading) {
  btn.disabled = loading;
  btn.style.opacity = loading ? '.6' : '1';
}

function updateUI(newStatus) {
  // Badge
  const badge = document.getElementById('status-badge');
  badge.className = 'badge-status ' + newStatus;
  badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

  // Progress steps
  const currentIdx = steps.indexOf(newStatus);
  steps.forEach((step, i) => {
    const row = document.getElementById('step-' + step);
    if (!row) return;
    const dot  = row.querySelector('div');
    const text = row.querySelector('span');
    const isDone   = currentIdx > i;
    const isActive = newStatus === step;
    dot.style.background  = isActive ? 'var(--mahogany)' : (isDone ? 'var(--success)' : '#ddd');
    text.style.color      = isActive ? 'var(--espresso)'  : (isDone ? 'var(--dark)' : '#aaa');
    text.style.fontWeight = isActive ? '700' : '400';
    // clear old indicators
    row.querySelectorAll('.step-indicator').forEach(el => el.remove());
    if (isActive) {
      const cur = document.createElement('span');
      cur.className = 'step-indicator';
      cur.style.cssText = 'font-size:.7rem;background:var(--cream);color:var(--chestnut);padding:.1rem .45rem;border-radius:50px;margin-left:auto;';
      cur.textContent = 'current';
      row.appendChild(cur);
    } else if (isDone) {
      const chk = document.createElement('i');
      chk.className = 'bi bi-check step-indicator';
      chk.style.cssText = 'color:var(--success);margin-left:auto;font-size:.8rem;';
      row.appendChild(chk);
    }
  });

  // Action buttons
  const container = document.getElementById('action-buttons');
  container.innerHTML = '';

  const next = statusFlow[newStatus];
  if (next) {
    const advBtn = document.createElement('button');
    advBtn.id = 'btn-advance';
    advBtn.className = 'btn-bc w-100 justify-content-center';
    advBtn.dataset.next = next;
    advBtn.dataset.url  = advBtn.dataset.url || document.getElementById('btn-advance')?.dataset.url;
    advBtn.style.display = 'flex';
    advBtn.style.alignItems = 'center';
    advBtn.style.gap = '.4rem';
    advBtn.innerHTML = `<i class="bi bi-${nextIcons[next]}"></i> ${nextLabels[next]}`;
    advBtn.addEventListener('click', handleAdvance);
    container.appendChild(advBtn);
  }

  if (!['completed','cancelled'].includes(newStatus)) {
    const canBtn = document.createElement('button');
    canBtn.id = 'btn-cancel';
    canBtn.className = 'btn-bc-danger btn-bc w-100 justify-content-center';
    canBtn.style.display = 'flex';
    canBtn.style.alignItems = 'center';
    canBtn.style.gap = '.4rem';
    canBtn.innerHTML = '<i class="bi bi-x-circle"></i> Cancel Order';
    canBtn.addEventListener('click', handleCancel);
    container.appendChild(canBtn);
  } else {
    const msg = document.createElement('div');
    msg.style.cssText = 'text-align:center;font-size:.82rem;color:var(--chestnut);padding:.5rem 0;';
    msg.innerHTML = `<i class="bi bi-lock me-1"></i>This order is ${newStatus}.`;
    container.appendChild(msg);
  }
}

function patchStatus(url, status, btn) {
  setLoading(btn, true);
  fetch(url, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ status }),
  })
  .then(r => { if (!r.ok) throw new Error(); return r.json(); })
  .then(() => updateUI(status))
  .catch(() => {
    setLoading(btn, false);
    alert('Failed to update status. Please try again.');
  });
}

function handleAdvance() {
  const btn = document.getElementById('btn-advance');
  const url = '{{ route($orderStatusRoute, $order) }}';
  patchStatus(url, btn.dataset.next, btn);
}

function handleCancel() {
  if (!confirm('Are you sure you want to cancel this order?')) return;
  const btn = document.getElementById('btn-cancel');
  const url = '{{ route($orderStatusRoute, $order) }}';
  patchStatus(url, 'cancelled', btn);
}

document.getElementById('btn-advance')?.addEventListener('click', handleAdvance);
document.getElementById('btn-cancel')?.addEventListener('click', handleCancel);
</script>
@endpush
