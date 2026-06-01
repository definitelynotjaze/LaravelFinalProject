@extends('layouts.admin')
@section('title', 'Menu Items')
@section('page-title', 'Menu Items')

@section('content')
@php $isAdmin = auth()->user()->role === 'admin'; @endphp
<div class="bc-page-head d-flex flex-wrap align-items-start justify-content-between gap-3">
  <div>
    <h2>Menu Management</h2>
    <p>{{ $isAdmin ? 'Add, edit, or remove items from the public menu.' : 'View current menu items.' }}</p>
  </div>
  @if($isAdmin)
  <a href="{{ route('admin.menu.create') }}" class="btn-bc">
    <i class="bi bi-plus-circle"></i> Add New Item
  </a>
  @endif
</div>

{{-- Filter --}}
<div class="bc-card mb-4">
  <div class="bc-card-body">
    <form method="GET" action="{{ route($isAdmin ? 'admin.menu' : 'staff.menu') }}">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <div style="position:relative;flex:1;min-width:200px;">
          <i class="bi bi-search" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--latte);font-size:.9rem;"></i>
          <input type="text" name="search" class="bc-input" style="padding-left:2.4rem;"
            placeholder="Search items…" value="{{ request('search') }}">
        </div>
        <div style="min-width:160px;">
          <select name="category" class="bc-select">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn-bc" style="white-space:nowrap;">
          <i class="bi bi-funnel me-1"></i> Apply Filter
        </button>
        @if(request()->hasAny(['search','category']))
        <a href="{{ route($isAdmin ? 'admin.menu' : 'staff.menu') }}" class="btn-bc-ghost" style="white-space:nowrap;">
          <i class="bi bi-x me-1"></i> Clear
        </a>
        @endif
      </div>
      @if(request()->hasAny(['search','category']))
      <div style="margin-top:.6rem;font-size:.78rem;color:var(--caramel);">
        <i class="bi bi-funnel-fill me-1"></i>
        Filters active:
        @if(request('search')) <strong>Name:</strong> "{{ request('search') }}" @endif
        @if(request('category')) <strong>Category:</strong> {{ request('category') }} @endif
      </div>
      @endif
    </form>
  </div>
</div>

<div class="bc-card">
  <div class="bc-card-head">
    <h5><i class="bi bi-cup-hot me-2"></i>Menu Items <span style="font-size:.78rem;font-weight:400;color:var(--chestnut);">({{ $items->total() }})</span></h5>
  </div>
  <div class="bc-table-wrap">
    <table class="bc-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Available</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
        <tr>
          <td>
            @if($item->image)
              <img src="{{ asset('images/menu/'.$item->image) }}" alt="{{ $item->name }}"
                style="width:52px;height:52px;object-fit:cover;border-radius:8px;border:1px solid var(--cream);">
            @else
              <div style="width:52px;height:52px;border-radius:8px;background:var(--cream);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                {{ $item->emoji ?? '☕' }}
              </div>
            @endif
          </td>
          <td>
            <strong style="font-size:.92rem;">{{ $item->name }}</strong>
            <div style="font-size:.78rem;color:var(--chestnut);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->description }}</div>
          </td>
          <td><span style="font-size:.8rem;background:var(--cream);padding:.2rem .6rem;border-radius:50px;color:var(--chestnut);">{{ $item->category }}</span></td>
          <td><strong style="font-family:var(--ff-serif);">₱{{ number_format($item->price, 2) }}</strong></td>
          <td>
            @if($isAdmin)
            <form action="{{ route('admin.menu.toggle', $item) }}" method="POST">
              @csrf @method('PATCH')
              <button type="submit" style="background:none;border:none;cursor:pointer;font-size:1.3rem;line-height:1;">
                @if($item->is_available)
                  <span title="Click to mark unavailable" style="color:var(--success);">●</span>
                @else
                  <span title="Click to mark available" style="color:var(--danger);">○</span>
                @endif
              </button>
            </form>
            @else
              @if($item->is_available)
                <span style="font-size:1.3rem;color:var(--success);">●</span>
              @else
                <span style="font-size:1.3rem;color:var(--danger);">○</span>
              @endif
            @endif
          </td>
          <td>
            @if($isAdmin)
            <form action="{{ route('admin.menu.feature', $item) }}" method="POST">
              @csrf @method('PATCH')
              <button type="submit" style="background:none;border:none;cursor:pointer;font-size:1.2rem;line-height:1;">
                @if($item->is_featured)
                  <i class="bi bi-star-fill" style="color:var(--gold);" title="Remove from featured"></i>
                @else
                  <i class="bi bi-star" style="color:var(--latte);" title="Mark as featured"></i>
                @endif
              </button>
            </form>
            @else
              @if($item->is_featured)
                <i class="bi bi-star-fill" style="color:var(--gold);font-size:1.2rem;"></i>
              @else
                <i class="bi bi-star" style="color:var(--latte);font-size:1.2rem;"></i>
              @endif
            @endif
          </td>
          <td>
            @if($isAdmin)
            <div class="d-flex gap-2">
              <a href="{{ route('admin.menu.edit', $item) }}" class="btn-bc-outline btn-bc-sm">
                <i class="bi bi-pencil"></i>
              </a>
              <form id="del-menu-{{ $item->id }}" action="{{ route('admin.menu.destroy', $item) }}" method="POST">
                @csrf @method('DELETE')
                <button type="button" class="btn-bc-danger btn-bc-sm"
                  onclick="confirmDelete('del-menu-{{ $item->id }}','{{ addslashes($item->name) }}')">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
            @else
              <span style="font-size:.78rem;color:var(--chestnut);">View only</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:3rem;color:var(--chestnut);">
            No menu items yet.@if($isAdmin) <a href="{{ route('admin.menu.create') }}" style="color:var(--mahogany);">Add the first one →</a>@endif
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($items->hasPages())
  <div class="p-3 d-flex justify-content-end" style="border-top:1px solid var(--cream);">
    {{ $items->withQueryString()->links('pagination::bootstrap-5') }}
  </div>
  @endif
</div>
@endsection
