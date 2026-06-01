@extends('layouts.admin')
@section('title', 'All Orders')
@section('page-title', 'Orders')

@section('content')
@php
  $isAdmin = auth()->user()->role === 'admin';
  $ordersRoute     = $isAdmin ? 'admin.orders'      : 'staff.orders';
  $orderShowRoute  = $isAdmin ? 'admin.orders.show'  : 'staff.orders.show';
  $orderStatusRoute= $isAdmin ? 'admin.orders.status': 'staff.orders.status';
@endphp
<div class="bc-page-head">
  <h2>Order Management</h2>
  <p>View and update the status of all customer orders.</p>
</div>

{{-- Status filter tabs --}}
<div class="d-flex gap-2 flex-wrap mb-4">
  @foreach(['all'=>'All','pending'=>'Pending','confirmed'=>'Confirmed','preparing'=>'Preparing','ready'=>'Ready','completed'=>'Completed','cancelled'=>'Cancelled'] as $val => $label)
  <a href="{{ route($ordersRoute, ['status'=>$val]) }}"
    class="{{ request('status',$val==='all'?'all':'x')===$val || (!request('status') && $val==='all') ? 'btn-bc' : 'btn-bc-ghost' }} btn-bc-sm">
    {{ $label }}
    @if($val !== 'all')
      <span style="opacity:.65;">({{ $statusCounts[$val] ?? 0 }})</span>
    @endif
  </a>
  @endforeach
</div>

<div class="bc-card">
  <div class="bc-card-head">
    <h5><i class="bi bi-receipt me-2"></i>Orders <span style="font-size:.78rem;font-weight:400;color:var(--chestnut);">({{ $orders->total() }})</span></h5>
  </div>
  <div class="bc-table-wrap">
    <table class="bc-table">
      <thead>
        <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Items</th>
          <th>Total</th>
          <th>Type</th>
          <th>Status</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
        @php
          $statusFlow = ['pending'=>'confirmed','confirmed'=>'preparing','preparing'=>'ready','ready'=>'completed'];
          $nextStatus = $statusFlow[$order->status] ?? null;
          $nextLabels = ['confirmed'=>'Confirm','preparing'=>'Prepare','ready'=>'Mark Ready','completed'=>'Complete'];
          $nextIcons  = ['confirmed'=>'check-circle','preparing'=>'fire','ready'=>'bag-check','completed'=>'trophy'];
          $badgeStyle = [
            'pending'   => 'background:#fff3cd;color:#856404;',
            'confirmed' => 'background:#cfe2ff;color:#084298;',
            'preparing' => 'background:#fff0d6;color:#7a5230;',
            'ready'     => 'background:#d1e7dd;color:#0a3622;',
            'completed' => 'background:#d2f4ea;color:#0a3622;',
            'cancelled' => 'background:#f8d7da;color:#842029;',
          ][$order->status] ?? '';
        @endphp
        <tr id="order-row-{{ $order->id }}">
          <td><strong>#{{ str_pad($order->id,4,'0',STR_PAD_LEFT) }}</strong></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-sm" style="font-size:.7rem;">{{ strtoupper(substr($order->user->name??'G',0,2)) }}</div>
              <div>
                <div style="font-size:.88rem;font-weight:500;">{{ $order->user->name ?? 'Guest' }}</div>
                <div style="font-size:.75rem;color:var(--chestnut);">{{ $order->user->email ?? '' }}</div>
              </div>
            </div>
          </td>
          <td>
            <div style="font-size:.82rem;max-width:180px;">
              @foreach($order->items->take(2) as $oi)
                <div>{{ $oi->quantity }}× {{ $oi->menu_item_name }}</div>
              @endforeach
              @if($order->items->count() > 2)
                <div style="color:var(--caramel);">+{{ $order->items->count()-2 }} more</div>
              @endif
            </div>
          </td>
          <td><strong>₱{{ number_format($order->total,2) }}</strong></td>
          <td>
            <span style="font-size:.78rem;background:var(--cream);padding:.2rem .6rem;border-radius:50px;color:var(--chestnut);">
              {{ ucfirst($order->type ?? 'pickup') }}
            </span>
          </td>
          <td>
            <span id="status-badge-{{ $order->id }}"
              style="font-size:.75rem;padding:.2rem .7rem;border-radius:50px;font-weight:600;display:inline-block;{{ $badgeStyle }}">
              {{ ucfirst($order->status) }}
            </span>
          </td>
          <td style="font-size:.8rem;color:var(--chestnut);">
            {{ $order->created_at->format('M d') }}<br>
            {{ $order->created_at->format('g:i A') }}
          </td>
          <td>
            <div class="d-flex gap-1 align-items-center flex-wrap">
              {{-- View button --}}
              <a href="{{ route($orderShowRoute, $order) }}" class="btn-bc-outline btn-bc-sm" title="View Details">
                <i class="bi bi-eye"></i>
              </a>

              {{-- Advance status button --}}
              @if($nextStatus)
                <button
                  class="btn-bc btn-bc-sm order-advance-btn"
                  title="{{ $nextLabels[$nextStatus] }}"
                  data-order-id="{{ $order->id }}"
                  data-next-status="{{ $nextStatus }}"
                  data-url="{{ route($orderStatusRoute, $order) }}"
                  style="white-space:nowrap;">
                  <i class="bi bi-{{ $nextIcons[$nextStatus] }} me-1"></i>{{ $nextLabels[$nextStatus] }}
                </button>
              @endif

              {{-- Cancel button (only for active orders) --}}
              @if(!in_array($order->status, ['completed','cancelled']))
                <button
                  class="btn-bc-sm order-cancel-btn"
                  title="Cancel Order"
                  data-order-id="{{ $order->id }}"
                  data-url="{{ route($orderStatusRoute, $order) }}"
                  style="background:#f8d7da;color:#842029;border:1px solid #f1aeb5;border-radius:6px;padding:.2rem .55rem;cursor:pointer;">
                  <i class="bi bi-x-circle"></i>
                </button>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align:center;padding:3rem;color:var(--chestnut);">No orders found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($orders->hasPages())
  <div class="p-3 d-flex justify-content-end" style="border-top:1px solid var(--cream);">
    {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
const statusBadgeStyles = {
  pending:   'background:#fff3cd;color:#856404;',
  confirmed: 'background:#cfe2ff;color:#084298;',
  preparing: 'background:#fff0d6;color:#7a5230;',
  ready:     'background:#d1e7dd;color:#0a3622;',
  completed: 'background:#d2f4ea;color:#0a3622;',
  cancelled: 'background:#f8d7da;color:#842029;',
};
const statusFlow   = { pending:'confirmed', confirmed:'preparing', preparing:'ready', ready:'completed' };
const nextLabels   = { confirmed:'Confirm', preparing:'Prepare', ready:'Mark Ready', completed:'Complete' };
const nextIcons    = { confirmed:'check-circle', preparing:'fire', ready:'bag-check', completed:'trophy' };

function updateStatus(orderId, url, newStatus, btn) {
  btn.disabled = true;
  btn.style.opacity = '.5';

  fetch(url, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({ status: newStatus }),
  })
  .then(r => {
    if (!r.ok) throw new Error('Request failed');
    return r.json();
  })
  .then(() => {
    // Update status badge
    const badge = document.getElementById('status-badge-' + orderId);
    badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
    badge.style.cssText = 'font-size:.75rem;padding:.2rem .7rem;border-radius:50px;font-weight:600;display:inline-block;' + (statusBadgeStyles[newStatus] || '');

    const actionsCell = btn.closest('td');

    // Remove the advance button
    actionsCell.querySelectorAll('.order-advance-btn').forEach(b => b.remove());

    // If the new status has a next step, insert a new advance button
    const next = statusFlow[newStatus];
    if (next) {
      const newBtn = document.createElement('button');
      newBtn.className = 'btn-bc btn-bc-sm order-advance-btn';
      newBtn.title = nextLabels[next];
      newBtn.dataset.orderId = orderId;
      newBtn.dataset.nextStatus = next;
      newBtn.dataset.url = url;
      newBtn.style.whiteSpace = 'nowrap';
      newBtn.innerHTML = `<i class="bi bi-${nextIcons[next]} me-1"></i>${nextLabels[next]}`;
      newBtn.addEventListener('click', handleAdvance);
      // Insert before cancel button if present
      const cancelBtn = actionsCell.querySelector('.order-cancel-btn');
      actionsCell.querySelector('.d-flex').insertBefore(newBtn, cancelBtn);
    }

    // Remove cancel button if now completed or cancelled
    if (newStatus === 'completed' || newStatus === 'cancelled') {
      actionsCell.querySelectorAll('.order-cancel-btn').forEach(b => b.remove());
    }
  })
  .catch(() => {
    btn.disabled = false;
    btn.style.opacity = '1';
    alert('Failed to update order status. Please try again.');
  });
}

function handleAdvance(e) {
  const btn = e.currentTarget;
  const { orderId, nextStatus, url } = btn.dataset;
  updateStatus(orderId, url, nextStatus, btn);
}

function handleCancel(e) {
  const btn = e.currentTarget;
  const { orderId, url } = btn.dataset;
  if (!confirm('Cancel this order?')) return;
  updateStatus(orderId, url, 'cancelled', btn);
}

document.querySelectorAll('.order-advance-btn').forEach(b => b.addEventListener('click', handleAdvance));
document.querySelectorAll('.order-cancel-btn').forEach(b  => b.addEventListener('click', handleCancel));
</script>
@endpush
