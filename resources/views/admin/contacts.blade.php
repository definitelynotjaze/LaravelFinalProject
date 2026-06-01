@extends('layouts.admin')
@section('title', 'Contact Messages')
@section('page-title', 'Contact Messages')

@section('content')
<div class="bc-page-head d-flex flex-wrap align-items-start justify-content-between gap-3">
  <div>
    <h2>Contact Messages</h2>
    <p>Messages submitted through the contact form on the website.</p>
  </div>
  @if($unreadCount > 0)
    <form action="{{ route('admin.contacts.read-all') }}" method="POST">
      @csrf
      <button class="btn-bc-outline"><i class="bi bi-check2-all"></i> Mark All as Read</button>
    </form>
  @endif
</div>

{{-- Tabs --}}
<div class="d-flex gap-2 mb-4">
  <a href="{{ route('admin.contacts') }}"
    class="{{ !request('filter') ? 'btn-bc' : 'btn-bc-ghost' }} btn-bc-sm">
    All <span style="opacity:.7;">({{ $totalCount }})</span>
  </a>
  <a href="{{ route('admin.contacts', ['filter'=>'unread']) }}"
    class="{{ request('filter')==='unread' ? 'btn-bc' : 'btn-bc-ghost' }} btn-bc-sm">
    Unread <span style="opacity:.7;">({{ $unreadCount }})</span>
  </a>
</div>

@forelse($contacts as $msg)
<div class="bc-card mb-3" style="{{ !$msg->is_read ? 'border-left:3px solid var(--gold);' : '' }}">
  <div class="bc-card-head" style="cursor:pointer;" onclick="toggleMsg({{ $msg->id }})">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="avatar-sm">{{ strtoupper(substr($msg->name,0,2)) }}</div>
      <div style="flex:1;min-width:0;">
        <div style="font-weight:600;font-size:.92rem;color:var(--dark);">
          {{ $msg->name }}
          @if(!$msg->is_read)
            <span style="background:var(--gold);color:var(--espresso);font-size:.65rem;font-weight:700;padding:.1rem .45rem;border-radius:50px;margin-left:.4rem;">NEW</span>
          @endif
        </div>
        <div style="font-size:.78rem;color:var(--chestnut);">{{ $msg->email }}</div>
      </div>
      <div style="flex:1;min-width:120px;">
        <div style="font-size:.88rem;font-weight:600;color:var(--dark);">{{ $msg->subject }}</div>
      </div>
      <div style="font-size:.78rem;color:var(--chestnut);white-space:nowrap;">
        {{ $msg->created_at->format('M d, Y · g:i A') }}
      </div>
      <i class="bi bi-chevron-down" style="color:var(--latte);" id="chevron-{{ $msg->id }}"></i>
    </div>
  </div>

  <div id="msg-body-{{ $msg->id }}" style="display:none;">
    <div class="bc-card-body">
      <div style="background:var(--milk);border-radius:var(--r);padding:1.25rem;font-size:.92rem;line-height:1.75;color:var(--dark);border:1px solid var(--cream);">
        {{ $msg->message }}
      </div>
      <div class="d-flex gap-2 mt-3">
        @if(!$msg->is_read)
        <form action="{{ route('admin.contacts.mark-read', $msg) }}" method="POST">
          @csrf @method('PATCH')
          <button class="btn-bc-outline btn-bc-sm"><i class="bi bi-check2"></i> Mark Read</button>
        </form>
        @endif
        <a href="mailto:{{ $msg->email }}?subject=Re: {{ urlencode($msg->subject) }}" class="btn-bc btn-bc-sm">
          <i class="bi bi-reply"></i> Reply via Email
        </a>
        <form id="del-contact-{{ $msg->id }}" action="{{ route('admin.contacts.destroy', $msg) }}" method="POST">
          @csrf @method('DELETE')
          <button type="button" class="btn-bc-danger btn-bc-sm"
            onclick="confirmDelete('del-contact-{{ $msg->id }}','message from {{ addslashes($msg->name) }}')">
            <i class="bi bi-trash"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@empty
<div class="bc-card">
  <div class="bc-card-body" style="text-align:center;padding:3rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">📬</div>
    <p style="color:var(--chestnut);">No messages yet.</p>
  </div>
</div>
@endforelse

@if($contacts->hasPages())
  <div class="mt-3 d-flex justify-content-end">
    {{ $contacts->withQueryString()->links('pagination::bootstrap-5') }}
  </div>
@endif

@endsection

@push('scripts')
<script>
function toggleMsg(id) {
  const body    = document.getElementById('msg-body-' + id);
  const chevron = document.getElementById('chevron-' + id);
  const open    = body.style.display === 'none';
  body.style.display    = open ? '' : 'none';
  chevron.style.transform = open ? 'rotate(180deg)' : '';

  if (open) {
    fetch(`/admin/contacts/${id}/mark-read`, {
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
  }
}
</script>
@endpush
