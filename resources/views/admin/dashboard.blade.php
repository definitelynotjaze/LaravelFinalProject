@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php $ordersRoute = auth()->user()->role === 'admin' ? 'admin.orders' : 'staff.orders'; @endphp
<div class="bc-page-head">
  <h2>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->first_name ?? auth()->user()->name }} ☕</h2>
  <p>Here's what's happening at BrewCraft.</p>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon br"><i class="bi bi-people-fill"></i></div>
      <h3>{{ $totalUsers }}</h3>
      <p>Total Users</p>
      <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> +{{ $newUsersThisWeek }} this week</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon gd"><i class="bi bi-receipt"></i></div>
      <h3>{{ $totalOrders }}</h3>
      <p>Total Orders</p>
      <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $ordersToday }} today</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon gn"><i class="bi bi-cash-stack"></i></div>
      <h3>₱{{ number_format($totalRevenue, 0) }}</h3>
      <p>Total Revenue</p>
      <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> ₱{{ number_format($revenueToday, 0) }} today</div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon bl"><i class="bi bi-cup-hot"></i></div>
      <h3>{{ $totalMenuItems }}</h3>
      <p>Menu Items</p>
      <div class="stat-trend"><i class="bi bi-dot"></i> {{ $availableItems }} available</div>
    </div>
  </div>
</div>

<div class="row g-4">
  {{-- Recent Orders --}}
  <div class="col-lg-8">
    <div class="bc-card">
      <div class="bc-card-head">
        <h5><i class="bi bi-receipt me-2"></i>Recent Orders</h5>
        <a href="{{ route($ordersRoute) }}" class="btn-bc-outline btn-bc-sm">View All</a>
      </div>
      <div class="bc-table-wrap">
        <table class="bc-table">
          <thead>
            <tr>
              <th>Order #</th>
              <th>Customer</th>
              <th>Items</th>
              <th>Total</th>
              <th>Status</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentOrders as $order)
            <tr>
              <td><strong>#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar-sm">{{ strtoupper(substr($order->user->name ?? 'G', 0, 2)) }}</div>
                  <span>{{ $order->user->name ?? 'Guest' }}</span>
                </div>
              </td>
              <td>{{ $order->items->count() }} item(s)</td>
              <td><strong>₱{{ number_format($order->total, 2) }}</strong></td>
              <td><span class="badge-status {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
              <td style="color:var(--chestnut);font-size:.8rem;">{{ $order->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:var(--chestnut);padding:2rem;">No orders yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Quick Actions + Pending --}}
  <div class="col-lg-4">
    {{-- Pending orders alert --}}
    @if($pendingCount > 0)
    <div class="bc-alert bc-alert-warn mb-3">
      <i class="bi bi-clock me-2"></i>
      <strong>{{ $pendingCount }} pending</strong> order(s) need attention.
      <a href="{{ route($ordersRoute) }}" style="color:inherit;font-weight:700;"> View →</a>
    </div>
    @endif

    {{-- Quick Actions --}}
    @if(auth()->user()->role === 'admin')
    <div class="bc-card mb-4">
      <div class="bc-card-head"><h5>Quick Actions</h5></div>
      <div class="bc-card-body d-flex flex-column gap-2">
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.menu.create') }}" class="btn-bc w-100 justify-content-center">
          <i class="bi bi-plus-circle"></i> Add Menu Item
        </a>
        <a href="{{ route('admin.users') }}" class="btn-bc-outline w-100 justify-content-center">
          <i class="bi bi-people"></i> Manage Users
        </a>
        <a href="{{ route('admin.contacts') }}" class="btn-bc-ghost w-100 justify-content-center">
          <i class="bi bi-envelope"></i> View Messages
          @if($unreadContacts > 0)
            <span style="background:var(--danger);color:#fff;border-radius:50px;padding:.1rem .45rem;font-size:.72rem;margin-left:.35rem;">{{ $unreadContacts }}</span>
          @endif
        </a>
        @endif
      </div>
    </div>
    @endif

    {{-- Top Items --}}
    <div class="bc-card">
      <div class="bc-card-head"><h5>Top Selling Items</h5></div>
      <div class="bc-card-body">
        @forelse($topItems as $item)
        <div class="d-flex align-items-center gap-3 mb-3">
          <div style="width:40px;height:40px;border-radius:8px;background:var(--cream);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
            {{ $item->emoji ?? '☕' }}
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:.88rem;font-weight:600;color:var(--dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->name }}</div>
            <div style="font-size:.75rem;color:var(--chestnut);">{{ $item->total_sold ?? 0 }} sold</div>
          </div>
          <div style="font-family:var(--ff-serif);font-weight:700;color:var(--mahogany);font-size:.95rem;">₱{{ number_format($item->price, 0) }}</div>
        </div>
        @empty
        <p style="color:var(--chestnut);font-size:.85rem;text-align:center;">No sales data yet.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
