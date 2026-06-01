@extends('layouts.admin')
@section('title', 'Analytics')
@section('page-title', 'Analytics')

@section('content')
<div class="bc-page-head">
  <h2>Analytics Overview</h2>
  <p>Sales trends, top items, and customer insights.</p>
</div>

{{-- Summary stats --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon gn"><i class="bi bi-cash-stack"></i></div>
      <h3>₱{{ number_format($monthRevenue,0) }}</h3>
      <p>This Month's Revenue</p>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon gd"><i class="bi bi-receipt"></i></div>
      <h3>{{ $monthOrders }}</h3>
      <p>Orders This Month</p>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon br"><i class="bi bi-graph-up"></i></div>
      <h3>₱{{ number_format($avgOrderValue,2) }}</h3>
      <p>Avg. Order Value</p>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon bl"><i class="bi bi-people"></i></div>
      <h3>{{ $newUsers }}</h3>
      <p>New Customers This Month</p>
    </div>
  </div>
</div>

<div class="row g-4">
  {{-- Revenue chart --}}
  <div class="col-lg-8">
    <div class="bc-card">
      <div class="bc-card-head">
        <h5><i class="bi bi-bar-chart-line me-2"></i>Revenue — Last 7 Days</h5>
      </div>
      <div class="bc-card-body">
        <canvas id="revenueChart" height="90"></canvas>
      </div>
    </div>
  </div>

  {{-- Order status breakdown --}}
  <div class="col-lg-4">
    <div class="bc-card">
      <div class="bc-card-head"><h5>Orders by Status</h5></div>
      <div class="bc-card-body">
        <canvas id="statusChart" height="200"></canvas>
      </div>
    </div>
  </div>

  {{-- Top menu items --}}
  <div class="col-lg-6">
    <div class="bc-card">
      <div class="bc-card-head"><h5><i class="bi bi-trophy me-2"></i>Top Selling Items</h5></div>
      <div class="bc-card-body">
        @foreach($topItems as $i => $item)
        <div class="d-flex align-items-center gap-3 mb-3">
          <div style="width:28px;height:28px;border-radius:50%;background:var(--cream);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;color:var(--mahogany);flex-shrink:0;">{{ $i+1 }}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-weight:600;font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->name }}</div>
            <div style="background:var(--cream);height:6px;border-radius:3px;margin-top:.35rem;">
              <div style="background:var(--mahogany);height:100%;border-radius:3px;width:{{ $item->total_sold ? min(100,($item->total_sold/$topItems->first()->total_sold)*100) : 0 }}%;"></div>
            </div>
          </div>
          <div style="font-size:.82rem;font-weight:600;color:var(--chestnut);white-space:nowrap;">{{ $item->total_sold ?? 0 }} sold</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Category breakdown --}}
  <div class="col-lg-6">
    <div class="bc-card">
      <div class="bc-card-head"><h5>Sales by Category</h5></div>
      <div class="bc-card-body">
        <canvas id="categoryChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const brewColors = ['#4A2512','#6B3A1F','#9B622A','#C4916A','#C8A84B','#E8D5BE','#3A7D44','#2471A3'];

// Revenue last 7 days
new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($dailyLabels) !!},
    datasets: [{
      label: 'Revenue (₱)',
      data: {!! json_encode($dailyRevenue) !!},
      backgroundColor: 'rgba(74,37,18,.75)',
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { ticks: { callback: v => '₱'+v.toLocaleString() }, grid: { color: '#E8D5BE' } },
      x: { grid: { display: false } }
    }
  }
});

// Status doughnut
new Chart(document.getElementById('statusChart'), {
  type: 'doughnut',
  data: {
    labels: {!! json_encode($statusLabels) !!},
    datasets: [{ data: {!! json_encode($statusData) !!}, backgroundColor: brewColors, borderWidth: 0 }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }, cutout: '65%' }
});

// Category bar
new Chart(document.getElementById('categoryChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($categoryLabels) !!},
    datasets: [{
      label: 'Units Sold',
      data: {!! json_encode($categoryData) !!},
      backgroundColor: brewColors.slice(0,6),
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    indexAxis: 'y',
    plugins: { legend: { display: false } },
    scales: { x: { grid: { color: '#E8D5BE' } }, y: { grid: { display: false } } }
  }
});
</script>
@endpush
