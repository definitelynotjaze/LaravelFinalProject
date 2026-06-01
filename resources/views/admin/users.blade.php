@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="bc-page-head d-flex flex-wrap align-items-start justify-content-between gap-3">
  <div>
    <h2>User Management</h2>
    <p>View, search, and manage all registered users.</p>
  </div>
  <button class="btn-bc" onclick="openAddUserModal()">
    <i class="bi bi-person-plus me-1"></i> Add User
  </button>
</div>

<div class="bc-card mb-4">
  <div class="bc-card-body">
    <form method="GET" action="{{ route('admin.users') }}">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <div style="position:relative;flex:1;min-width:200px;">
          <i class="bi bi-search" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--latte);font-size:.9rem;"></i>
          <input type="text" name="search" class="bc-input" style="padding-left:2.4rem;"
            placeholder="Search by name or email…" value="{{ request('search') }}">
        </div>
        <div style="min-width:160px;">
          <select name="role" class="bc-select">
            <option value="">All Roles</option>
            <option value="admin"  {{ request('role') === 'admin'  ? 'selected' : '' }}>Admin</option>
            <option value="staff"  {{ request('role') === 'staff'  ? 'selected' : '' }}>Staff</option>
            <option value="user"   {{ request('role') === 'user'   ? 'selected' : '' }}>User</option>
          </select>
        </div>
        <button type="submit" class="btn-bc" style="white-space:nowrap;">
          <i class="bi bi-funnel me-1"></i> Apply Filter
        </button>
        @if(request('search') || request('role'))
        <a href="{{ route('admin.users') }}" class="btn-bc-ghost" style="white-space:nowrap;">
          <i class="bi bi-x me-1"></i> Clear
        </a>
        @endif
      </div>
      @if(request('search') || request('role'))
      <div style="margin-top:.6rem;font-size:.78rem;color:var(--caramel);">
        <i class="bi bi-funnel-fill me-1"></i>
        Filters active:
        @if(request('search')) <strong>Name/Email:</strong> "{{ request('search') }}" @endif
        @if(request('role')) <strong>Role:</strong> {{ ucfirst(request('role')) }} @endif
      </div>
      @endif
    </form>
  </div>
</div>

<div class="bc-card">
  <div class="bc-card-head">
    <h5><i class="bi bi-people me-2"></i>All Users <span style="font-size:.78rem;font-weight:400;color:var(--chestnut);">({{ $users->total() }})</span></h5>
  </div>
  <div class="bc-table-wrap">
    <table class="bc-table" style="table-layout:fixed;width:100%;">
      <colgroup>
        <col style="width:50px;">
        <col style="width:180px;">
        <col style="width:200px;">
        <col style="width:130px;">
        <col style="width:90px;">
        <col style="width:110px;">
        <col style="width:70px;">
        <col style="width:100px;">
      </colgroup>
      <thead>
        <tr>
          <th style="text-align:center;">#</th>
          <th>User</th>
          <th>Email</th>
          <th>Phone</th>
          <th style="text-align:center;">Role</th>
          <th style="text-align:center;">Joined</th>
          <th style="text-align:center;">Orders</th>
          <th style="text-align:center;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td style="color:var(--chestnut);font-size:.8rem;text-align:center;">{{ $user->id }}</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-sm" style="flex-shrink:0;">
                @if($user->profile_photo)
                  <img src="{{ asset('images/profiles/'.$user->profile_photo) }}" alt="">
                @else
                  {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
              </div>
              <div style="overflow:hidden;">
                <div style="font-weight:600;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $user->name }}</div>
                @if($user->id === auth()->id())
                  <div style="font-size:.72rem;color:var(--caramel);">That's you</div>
                @endif
              </div>
            </div>
          </td>
          <td style="font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $user->email }}">{{ $user->email }}</td>
          <td style="font-size:.85rem;color:var(--chestnut);">{{ $user->phone ?? '—' }}</td>
          <td style="text-align:center;">
            <span class="badge-role {{ $user->role }}">{{ ucfirst($user->role) }}</span>
          </td>
          <td style="font-size:.82rem;color:var(--chestnut);text-align:center;">{{ $user->created_at->format('M d, Y') }}</td>
          <td style="font-size:.88rem;text-align:center;">{{ $user->orders_count ?? 0 }}</td>
          <td style="text-align:center;">
            @if($user->id !== auth()->id())
            <div class="d-flex gap-1 justify-content-center">
              <button type="button"
                onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->first_name) }}', '{{ addslashes($user->last_name) }}', '{{ addslashes($user->email) }}', '{{ addslashes($user->phone ?? '') }}', '{{ $user->role }}')"
                class="btn-bc-ghost btn-bc-sm" title="Edit">
                <i class="bi bi-pencil"></i>
              </button>
              <button type="button"
                onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                class="btn-bc-danger btn-bc-sm" title="Delete">
                <i class="bi bi-trash"></i>
              </button>
            </div>
            @else
            <span style="font-size:.75rem;color:var(--chestnut);">—</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align:center;padding:3rem;color:var(--chestnut);">
            No users found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
  <div class="p-3 d-flex justify-content-end" style="border-top:1px solid var(--cream);">
    {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
  </div>
  @endif
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:var(--r);border:none;">
      <div class="modal-header" style="border-bottom:1px solid var(--cream);">
        <h5 class="modal-title" style="font-family:var(--font-serif);"><i class="bi bi-person-plus me-2"></i>Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="bc-form-group">
              <label class="bc-label">First Name</label>
              <input type="text" id="add_first_name" class="bc-input" placeholder="Juan">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="bc-form-group">
              <label class="bc-label">Last Name</label>
              <input type="text" id="add_last_name" class="bc-input" placeholder="dela Cruz">
            </div>
          </div>
          <div class="col-12">
            <div class="bc-form-group">
              <label class="bc-label">Email</label>
              <input type="email" id="add_email" class="bc-input" placeholder="juan@email.com">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="bc-form-group">
              <label class="bc-label">Phone</label>
              <input type="text" id="add_phone" class="bc-input" placeholder="+63 917 000 0000">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="bc-form-group">
              <label class="bc-label">Role</label>
              <select id="add_role" class="bc-select">
                <option value="user">User</option>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <div class="bc-form-group">
              <label class="bc-label">Password</label>
              <input type="password" id="add_password" class="bc-input" placeholder="Min 8 characters">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--cream);">
        <button type="button" class="btn-bc-ghost" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn-bc" onclick="submitAddUser()">
          <i class="bi bi-person-plus me-1"></i> Create User
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:var(--r);border:none;">
      <div class="modal-header" style="border-bottom:1px solid var(--cream);">
        <h5 class="modal-title" style="font-family:var(--font-serif);"><i class="bi bi-pencil me-2"></i>Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_user_id">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="bc-form-group">
              <label class="bc-label">First Name</label>
              <input type="text" id="edit_first_name" class="bc-input">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="bc-form-group">
              <label class="bc-label">Last Name</label>
              <input type="text" id="edit_last_name" class="bc-input">
            </div>
          </div>
          <div class="col-12">
            <div class="bc-form-group">
              <label class="bc-label">Email</label>
              <input type="email" id="edit_email" class="bc-input">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="bc-form-group">
              <label class="bc-label">Phone</label>
              <input type="text" id="edit_phone" class="bc-input">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="bc-form-group">
              <label class="bc-label">Role</label>
              <select id="edit_role" class="bc-select">
                <option value="user">User</option>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <div class="bc-form-group">
              <label class="bc-label">New Password <span style="font-size:.78rem;color:var(--chestnut);">(leave blank to keep current)</span></label>
              <input type="password" id="edit_password" class="bc-input" placeholder="Min 8 characters">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--cream);">
        <button type="button" class="btn-bc-ghost" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn-bc" onclick="submitEditUser()">
          <i class="bi bi-check2 me-1"></i> Save Changes
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function openAddUserModal() {
  document.getElementById('add_first_name').value = '';
  document.getElementById('add_last_name').value = '';
  document.getElementById('add_email').value = '';
  document.getElementById('add_phone').value = '';
  document.getElementById('add_role').value = 'user';
  document.getElementById('add_password').value = '';
  new bootstrap.Modal(document.getElementById('addUserModal')).show();
}

function openEditUserModal(id, firstName, lastName, email, phone, role) {
  document.getElementById('edit_user_id').value = id;
  document.getElementById('edit_first_name').value = firstName;
  document.getElementById('edit_last_name').value = lastName;
  document.getElementById('edit_email').value = email;
  document.getElementById('edit_phone').value = phone;
  document.getElementById('edit_role').value = role;
  document.getElementById('edit_password').value = '';
  new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function apiRequest(url, method, body) {
  const upperMethod = method.toUpperCase();
  const needsOverride = upperMethod === 'PATCH' || upperMethod === 'DELETE';
  const separator = url.includes('?') ? '&' : '?';
  const resolvedUrl = needsOverride ? url + separator + '_method=' + upperMethod : url;
  const fullUrl = (window.BC_BASE_URL || '').replace(/\/$/, '') + '/' + resolvedUrl.replace(/^\//, '');
  const httpMethod = needsOverride ? 'POST' : upperMethod;
  const opts = {
    method: httpMethod,
    headers: {
      'X-CSRF-TOKEN':           csrfToken,
      'Accept':                 'application/json',
      'X-Requested-With':       'XMLHttpRequest',
      'X-HTTP-Method-Override': upperMethod,
    },
  };
  if (body) {
    opts.headers['Content-Type'] = 'application/json';
    opts.body = JSON.stringify(body);
  }
  return fetch(fullUrl, opts).then(r => {
    if (r.status === 419) return Promise.reject(new Error('Session expired. Please refresh the page.'));
    if (r.status === 403) return Promise.reject(new Error('Access denied.'));
    return r.text().then(text => {
      try {
        const data = JSON.parse(text);
        return { status: r.status, ok: r.ok, data };
      } catch (_) {
        const preview = text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 200);
        return Promise.reject(new Error('Server error (' + r.status + '): ' + (preview || 'No details.')));
      }
    });
  });
}

function submitAddUser() {
  const payload = {
    first_name: document.getElementById('add_first_name').value.trim(),
    last_name:  document.getElementById('add_last_name').value.trim(),
    email:      document.getElementById('add_email').value.trim(),
    phone:      document.getElementById('add_phone').value.trim(),
    role:       document.getElementById('add_role').value,
    password:   document.getElementById('add_password').value,
  };

  if (!payload.first_name || !payload.last_name || !payload.email || !payload.password) {
    bcToast('Please fill in all required fields.', 'error');
    return;
  }

  apiRequest('/admin/users', 'POST', payload)
    .then(({ ok, data }) => {
      if (ok && data.success) {
        bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
        bcToast(data.message || 'User created.');
        setTimeout(() => location.reload(), 1200);
      } else {
        const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to create user.');
        bcToast(msg, 'error');
      }
    })
    .catch(e => bcToast(e.message, 'error'));
}

function submitEditUser() {
  const id = document.getElementById('edit_user_id').value;
  const payload = {
    first_name: document.getElementById('edit_first_name').value.trim(),
    last_name:  document.getElementById('edit_last_name').value.trim(),
    email:      document.getElementById('edit_email').value.trim(),
    phone:      document.getElementById('edit_phone').value.trim(),
    role:       document.getElementById('edit_role').value,
    password:   document.getElementById('edit_password').value || null,
  };

  if (!payload.first_name || !payload.last_name || !payload.email) {
    bcToast('Please fill in all required fields.', 'error');
    return;
  }

  apiRequest(`/admin/users/${id}`, 'PATCH', payload)
    .then(({ ok, data }) => {
      if (ok && data.success) {
        bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
        bcToast(data.message || 'User updated.');
        setTimeout(() => location.reload(), 1200);
      } else {
        const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to update user.');
        bcToast(msg, 'error');
      }
    })
    .catch(e => bcToast(e.message, 'error'));
}

function deleteUser(id, name) {
  if (!confirm(`Delete "${name}"? This cannot be undone.`)) return;

  apiRequest(`/admin/users/${id}`, 'DELETE')
    .then(({ ok, data }) => {
      if (ok && data.success) {
        bcToast(data.message || 'User deleted.');
        setTimeout(() => location.reload(), 1200);
      } else {
        bcToast(data.message || 'Failed to delete user.', 'error');
      }
    })
    .catch(e => bcToast(e.message, 'error'));
}
</script>
@endpush
