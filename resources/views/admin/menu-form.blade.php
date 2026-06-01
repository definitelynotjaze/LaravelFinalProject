@extends('layouts.admin')
@section('title', isset($item) ? 'Edit Item' : 'Add Menu Item')
@section('page-title', isset($item) ? 'Edit Menu Item' : 'Add Menu Item')

@section('content')
<div class="bc-page-head">
  <h2>{{ isset($item) ? 'Edit: '.$item->name : 'Add New Menu Item' }}</h2>
  <p>{{ isset($item) ? 'Update this item\'s details, image, and availability.' : 'Fill in the details for the new menu item.' }}</p>
</div>

<form action="{{ isset($item) ? route('admin.menu.update', $item) : route('admin.menu.store') }}"
  method="POST" enctype="multipart/form-data">
  @csrf
  @if(isset($item)) @method('PUT') @endif

  <div class="row g-4">
    {{-- Left: Main details --}}
    <div class="col-lg-8">
      <div class="bc-card mb-4">
        <div class="bc-card-head"><h5>Item Details</h5></div>
        <div class="bc-card-body">
          <div class="row g-3">
            <div class="col-sm-8">
              <div class="bc-form-group">
                <label class="bc-label">Item Name <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" class="bc-input @error('name') is-invalid @enderror"
                  value="{{ old('name', $item->name ?? '') }}" placeholder="e.g. Caramel Macchiato" required>
                @error('name')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-sm-4">
              <div class="bc-form-group">
                <label class="bc-label">Emoji Icon</label>
                <input type="text" name="emoji" class="bc-input"
                  value="{{ old('emoji', $item->emoji ?? '☕') }}" placeholder="☕" maxlength="4">
              </div>
            </div>
            <div class="col-12">
              <div class="bc-form-group">
                <label class="bc-label">Description</label>
                <textarea name="description" rows="3" class="bc-textarea @error('description') is-invalid @enderror"
                  placeholder="A brief description of this item…">{{ old('description', $item->description ?? '') }}</textarea>
                @error('description')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-sm-4">
              <div class="bc-form-group">
                <label class="bc-label">Price (₱) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="price" step="0.01" min="0" class="bc-input @error('price') is-invalid @enderror"
                  value="{{ old('price', $item->price ?? '') }}" placeholder="0.00" required>
                @error('price')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-sm-4">
              <div class="bc-form-group">
                <label class="bc-label">Category <span style="color:var(--danger);">*</span></label>
                <select name="category" class="bc-select @error('category') is-invalid @enderror" required>
                  <option value="">Select category</option>
                  @foreach(['Hot Coffee','Iced Coffee','Non-Coffee','Tea & Others','Pastries','Snacks','Meals','Desserts'] as $cat)
                    <option value="{{ $cat }}" {{ old('category', $item->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                  @endforeach
                </select>
                @error('category')<div class="bc-invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-sm-4">
              <div class="bc-form-group">
                <label class="bc-label">Preparation Time</label>
                <div style="position:relative;">
                  <input type="number" name="prep_time" min="1" class="bc-input"
                    value="{{ old('prep_time', $item->prep_time ?? '5') }}" placeholder="5">
                  <span style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);font-size:.78rem;color:var(--latte);">min</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Customizations --}}
      <div class="bc-card">
        <div class="bc-card-head"><h5>Additional Info</h5></div>
        <div class="bc-card-body">
          <div class="bc-form-group">
            <label class="bc-label">Allergen Info</label>
            <input type="text" name="allergens" class="bc-input"
              value="{{ old('allergens', $item->allergens ?? '') }}"
              placeholder="e.g. Contains milk, nuts">
          </div>
          <div class="bc-form-group">
            <label class="bc-label">Calories</label>
            <input type="number" name="calories" class="bc-input"
              value="{{ old('calories', $item->calories ?? '') }}" placeholder="e.g. 250">
          </div>
        </div>
      </div>
    </div>

    {{-- Right: Image + Status --}}
    <div class="col-lg-4">
      {{-- Image upload --}}
      <div class="bc-card mb-4">
        <div class="bc-card-head"><h5>Item Image</h5></div>
        <div class="bc-card-body">
          <div class="img-upload-zone">
            <input type="file" name="image" accept="image/*">
            @if(isset($item) && $item->image)
              <img src="{{ asset('images/menu/'.$item->image) }}" class="img-preview" style="display:block;" id="menuImgPreview">
              <div style="font-size:.78rem;color:var(--chestnut);margin-top:.75rem;">Click to replace image</div>
            @else
              <img class="img-preview" id="menuImgPreview" alt="">
              <i class="bi bi-cloud-upload" style="font-size:2rem;color:var(--latte);"></i>
              <div style="font-size:.85rem;color:var(--chestnut);margin-top:.5rem;">Click to upload image</div>
              <div style="font-size:.75rem;color:var(--latte);">JPG, PNG up to 2MB</div>
            @endif
          </div>
        </div>
      </div>

      {{-- Status --}}
      <div class="bc-card mb-4">
        <div class="bc-card-head"><h5>Status</h5></div>
        <div class="bc-card-body d-flex flex-column gap-3">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div style="font-weight:600;font-size:.9rem;">Available</div>
              <div style="font-size:.78rem;color:var(--chestnut);">Show on public menu</div>
            </div>
            <label style="cursor:pointer;position:relative;display:inline-block;width:44px;height:24px;">
              <input type="checkbox" name="is_available" value="1" style="opacity:0;width:0;height:0;"
                {{ old('is_available', $item->is_available ?? true) ? 'checked' : '' }}>
              <span style="position:absolute;inset:0;background:var(--cream);border-radius:50px;transition:.3s;"></span>
            </label>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div style="font-weight:600;font-size:.9rem;">Featured</div>
              <div style="font-size:.78rem;color:var(--chestnut);">Show in hero & highlights</div>
            </div>
            <label style="cursor:pointer;position:relative;display:inline-block;width:44px;height:24px;">
              <input type="checkbox" name="is_featured" value="1" style="opacity:0;width:0;height:0;"
                {{ old('is_featured', $item->is_featured ?? false) ? 'checked' : '' }}>
              <span style="position:absolute;inset:0;background:var(--cream);border-radius:50px;transition:.3s;"></span>
            </label>
          </div>
        </div>
      </div>

      {{-- Actions --}}
      <div class="d-flex flex-column gap-2">
        <button type="submit" class="btn-bc btn-bc-lg w-100">
          <i class="bi bi-{{ isset($item) ? 'check-circle' : 'plus-circle' }}"></i>
          {{ isset($item) ? 'Save Changes' : 'Add Menu Item' }}
        </button>
        <a href="{{ route('admin.menu') }}" class="btn-bc-ghost w-100 justify-content-center">
          Cancel
        </a>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
// Image preview for this form
document.querySelector('.img-upload-zone input')?.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;
  const preview = document.getElementById('menuImgPreview');
  const reader = new FileReader();
  reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
  reader.readAsDataURL(file);
});

// Toggle switch styling
document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
  const span = cb.nextElementSibling;
  if (!span || !span.style.background) return;
  function update() {
    span.style.background = cb.checked ? 'var(--mahogany)' : 'var(--cream)';
  }
  update();
  cb.addEventListener('change', update);
});
</script>
@endpush
