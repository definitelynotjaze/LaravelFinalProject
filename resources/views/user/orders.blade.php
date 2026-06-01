@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<div style="padding-top:var(--nav-h);min-height:100vh;background:var(--milk);">
  <div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
      <div>
        <h2 style="font-size:1.75rem;margin-bottom:.2rem;">My Orders</h2>
        <p style="color:var(--chestnut);font-size:.9rem;">Track your BrewCraft orders</p>
      </div>
      <a href="{{ route('home') }}#menu" class="btn-bc">
        <i class="bi bi-bag-plus"></i> Order Again
      </a>
    </div>

    @if(session('success'))
      <div class="bc-alert bc-alert-success mb-4">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="bc-alert bc-alert-danger mb-4">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
      </div>
    @endif

    @forelse($orders as $order)
    <div class="bc-card mb-3">
      <div class="bc-card-head">
        <div class="d-flex flex-wrap gap-3 align-items-center w-100">
          <div>
            <div style="font-weight:700;font-size:.95rem;color:var(--dark);">Order #{{ str_pad($order->id,4,'0',STR_PAD_LEFT) }}</div>
            <div style="font-size:.78rem;color:var(--chestnut);">{{ $order->created_at->format('F d, Y · g:i A') }}</div>
          </div>
          <span class="badge-status {{ $order->status }} ms-auto">{{ ucfirst($order->status) }}</span>
          <strong style="font-family:var(--ff-serif);font-size:1.1rem;color:var(--mahogany);">₱{{ number_format($order->total,2) }}</strong>
          <span style="font-size:.8rem;background:var(--cream);padding:.2rem .65rem;border-radius:50px;color:var(--chestnut);">
            {{ ucfirst($order->type ?? 'Pickup') }}
          </span>
        </div>
      </div>

      <div class="bc-card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <div style="font-size:.8rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--chestnut);margin-bottom:.75rem;">Items Ordered</div>
            @foreach($order->items as $item)
            <div class="d-flex align-items-center gap-3 mb-2">
              <div style="width:42px;height:42px;border-radius:8px;background:var(--cream);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                {{ $item->menuItem->emoji ?? '☕' }}
              </div>
              <div style="flex:1;">
                <div style="font-size:.9rem;font-weight:500;">{{ $item->menu_item_name }}</div>
              </div>
              <div style="font-size:.85rem;color:var(--chestnut);">× {{ $item->quantity }}</div>
              <div style="font-family:var(--ff-serif);font-size:.95rem;font-weight:700;color:var(--mahogany);">₱{{ number_format($item->subtotal,2) }}</div>
            </div>
            @endforeach
          </div>
          <div class="col-md-4">
            @if($order->notes)
            <div style="background:var(--milk);border:1px solid var(--cream);border-radius:var(--r);padding:.85rem;font-size:.82rem;color:var(--chestnut);">
              <div style="font-weight:600;color:var(--dark);margin-bottom:.3rem;">Notes</div>
              {{ $order->notes }}
            </div>
            @endif

            {{-- Status tracker --}}
            <div class="mt-3">
              @php
                $steps = ['pending','confirmed','preparing','ready','completed'];
                $currentIdx = array_search($order->status, $steps);
              @endphp
              @if($order->status !== 'cancelled')
              <div style="font-size:.78rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--chestnut);margin-bottom:.5rem;">Progress</div>
              <div class="d-flex align-items-center gap-1">
                @foreach($steps as $si => $step)
                <div style="flex:1;height:4px;border-radius:2px;background:{{ $si <= ($currentIdx !== false ? $currentIdx : -1) ? 'var(--mahogany)' : 'var(--cream)' }};"></div>
                @endforeach
              </div>
              <div style="font-size:.78rem;color:var(--caramel);margin-top:.4rem;text-align:center;">
                {{ ucfirst($order->status) }}
              </div>
              @endif
            </div>

            @if(in_array($order->status,['pending','confirmed']))
            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="mt-2">
              @csrf @method('PATCH')
              <button type="submit" class="btn-bc-ghost btn-bc-sm w-100"
                onclick="return confirm('Cancel this order?')">
                <i class="bi bi-x-circle"></i> Cancel Order
              </button>
            </form>
            @endif
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="bc-card">
      <div class="bc-card-body" style="text-align:center;padding:4rem 2rem;">
        <div style="font-size:3.5rem;margin-bottom:1rem;">☕</div>
        <h5 style="color:var(--dark);">No orders yet</h5>
        <p style="color:var(--chestnut);font-size:.9rem;margin-bottom:1.5rem;">Your order history will appear here.</p>
        <a href="{{ route('home') }}#menu" class="btn-bc">Browse Menu</a>
      </div>
    </div>
    @endforelse

    @if($orders->hasPages())
    <div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
</div>
@endsection
