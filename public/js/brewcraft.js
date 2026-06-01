// Active nav highlight on scroll
(function () {
  const links    = document.querySelectorAll('.bc-nav-links a[href^="#"]');
  const sections = document.querySelectorAll('.scroll-target[id]');
  if (!links.length) return;
  function update() {
    let cur = '';
    sections.forEach(s => {
      if (window.scrollY >= s.offsetTop - 120) cur = s.id;
    });
    links.forEach(l => {
      l.classList.toggle('active', l.getAttribute('href') === '#' + cur);
    });
  }
  window.addEventListener('scroll', update, { passive: true });
  update();
})();

// Mobile sidebar toggle
document.addEventListener('DOMContentLoaded', function () {
  const tog     = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('bcSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (!tog) return;
  function openSidebar()  { sidebar?.classList.add('show');    overlay?.classList.add('show');    document.body.style.overflow = 'hidden'; }
  function closeSidebar() { sidebar?.classList.remove('show'); overlay?.classList.remove('show'); document.body.style.overflow = ''; }
  tog.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);
});

// Menu category filter tabs
document.addEventListener('DOMContentLoaded', function () {
  const tabs  = document.querySelectorAll('.menu-tab');
  const items = document.querySelectorAll('.menu-item-wrap');
  tabs.forEach(tab => {
    tab.addEventListener('click', function () {
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      const cat = this.dataset.cat;
      items.forEach(item => {
        item.classList.toggle('menu-item-hidden', cat !== 'all' && item.dataset.cat !== cat);
      });
    });
  });
});

function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function appUrl(path) {
  const base = (window.BC_BASE_URL || '').replace(/\/$/, '');
  return base + '/' + path.replace(/^\//, '');
}

function bcToast(msg, type = 'success') {
  let el = document.getElementById('bc-toast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'bc-toast';
    document.body.appendChild(el);
  }
  const icon = type === 'success' ? '☕' : type === 'error' ? '✕' : 'ℹ';
  el.innerHTML = `<span>${icon}</span> ${msg}`;
  el.style.borderLeftColor = type === 'error' ? '#B03A2E' : '#C8A84B';
  requestAnimationFrame(() => {
    el.style.transform = 'translateY(0)';
    el.style.opacity   = '1';
  });
  clearTimeout(el._t);
  el._t = setTimeout(() => {
    el.style.transform = 'translateY(20px)';
    el.style.opacity   = '0';
  }, 2800);
}

// Cart — user-scoped via localStorage
// Key format: brewcart_v1_{userId} — userId is injected by blade into window.BC_USER_ID
const BrewCart = {
  _key() {
    const uid = window.BC_USER_ID || 'guest';
    return 'brewcart_v1_' + uid;
  },
  items: [],

  load() {
    try { this.items = JSON.parse(localStorage.getItem(this._key())) || []; }
    catch { this.items = []; }
    return this;
  },
  save() {
    localStorage.setItem(this._key(), JSON.stringify(this.items));
    return this;
  },

  clearOtherUsers() {
    const currentKey = this._key();
    Object.keys(localStorage).forEach(k => {
      if (k.startsWith('brewcart_v1_') && k !== currentKey) {
        localStorage.removeItem(k);
      }
    });
  },

  add(id, name, price, img) {
    this.load();
    const existing = this.items.find(i => i.id === id);
    if (existing) existing.qty += 1;
    else this.items.push({ id: parseInt(id), name, price: parseFloat(price), img: img || '', qty: 1 });
    this.save().updateBadge();
    bcToast(`<strong>${name}</strong> added to cart!`);
  },

  remove(id) {
    this.load();
    this.items = this.items.filter(i => i.id !== parseInt(id));
    this.save().updateBadge().renderPage();
  },

  setQty(id, qty) {
    this.load();
    const item = this.items.find(i => i.id === parseInt(id));
    if (!item) return;
    item.qty = Math.max(1, parseInt(qty) || 1);
    this.save().updateBadge().renderPage();
  },

  clear() {
    this.items = [];
    this.save().updateBadge().renderPage();
  },

  subtotal() { return this.load().items.reduce((s, i) => s + i.price * i.qty, 0); },
  count()    { return this.load().items.reduce((s, i) => s + i.qty, 0); },

  updateBadge() {
    const badge = document.getElementById('cartBadge');
    if (!badge) return this;
    const n = this.count();
    badge.textContent = n;
    badge.style.display = n > 0 ? 'flex' : 'none';
    return this;
  },

  renderPage() {
    const listEl    = document.getElementById('cartItems');
    const subEl     = document.getElementById('cartSubtotal');
    const delivEl   = document.getElementById('cartDelivery');
    const totalEl   = document.getElementById('cartTotal');
    const emptyEl   = document.getElementById('cartEmpty');
    const summaryEl = document.getElementById('cartSummaryWrap');
    if (!listEl) return this;

    this.load();
    const delivery = 50;

    if (this.items.length === 0) {
      listEl.innerHTML = '';
      if (emptyEl)   emptyEl.style.display   = 'block';
      if (summaryEl) summaryEl.style.display  = 'none';
    } else {
      if (emptyEl)   emptyEl.style.display   = 'none';
      if (summaryEl) summaryEl.style.display  = '';

      listEl.innerHTML = this.items.map(item => {
        const imgContent = item.img
          ? `<img src="${item.img}" alt="${item.name}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`
          : '☕';
        return `
        <div class="cart-item" data-id="${item.id}">
          <div class="cart-item-img">${imgContent}</div>
          <div class="cart-item-info">
            <strong>${item.name}</strong>
            <span>₱${item.price.toFixed(2)} each</span>
          </div>
          <div class="cart-qty">
            <button class="cart-qty-btn" onclick="BrewCart.setQty(${item.id}, ${item.qty - 1})" ${item.qty <= 1 ? 'disabled style="opacity:.4;"' : ''}>−</button>
            <span class="cart-qty-num">${item.qty}</span>
            <button class="cart-qty-btn" onclick="BrewCart.setQty(${item.id}, ${item.qty + 1})">+</button>
          </div>
          <div class="cart-item-price">₱${(item.price * item.qty).toFixed(2)}</div>
          <button class="cart-remove" onclick="BrewCart.remove(${item.id})" title="Remove">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>`;
      }).join('');
    }

    const sub = this.subtotal();
    if (subEl)   subEl.textContent   = '₱' + sub.toFixed(2);
    if (delivEl) delivEl.textContent = sub > 0 ? '₱' + delivery.toFixed(2) : '₱0.00';
    if (totalEl) totalEl.textContent = sub > 0 ? '₱' + (sub + delivery).toFixed(2) : '₱0.00';
    return this;
  }
};

document.addEventListener('DOMContentLoaded', () => {
  if (window.BC_JUST_LOGGED_IN) {
    BrewCart.clearOtherUsers();
  }
  BrewCart.load().updateBadge().renderPage();
});

function updateRole(userId, sel) {
  fetch(appUrl(`/admin/users/${userId}/role?_method=PATCH`), {
    method: 'POST',
    headers: {
      'Content-Type':           'application/json',
      'X-CSRF-TOKEN':           getCsrfToken(),
      'Accept':                 'application/json',
      'X-Requested-With':       'XMLHttpRequest',
      'X-HTTP-Method-Override': 'PATCH',
    },
    body: JSON.stringify({ role: sel.value })
  })
  .then(r => {
    if (r.status === 419) throw new Error('Session expired. Please refresh the page.');
    return r.json();
  })
  .then(d => {
    if (d.success) bcToast('Role updated to ' + sel.value);
    else bcToast(d.message || 'Failed to update role', 'error');
  })
  .catch(e => bcToast(e.message || 'Network error', 'error'));
}

function updateOrderStatus(orderId, sel) {
  fetch(appUrl(`/admin/orders/${orderId}/status?_method=PATCH`), {
    method: 'POST',
    headers: {
      'Content-Type':           'application/json',
      'X-CSRF-TOKEN':           getCsrfToken(),
      'Accept':                 'application/json',
      'X-Requested-With':       'XMLHttpRequest',
      'X-HTTP-Method-Override': 'PATCH',
    },
    body: JSON.stringify({ status: sel.value })
  })
  .then(r => {
    if (r.status === 419) throw new Error('Session expired. Please refresh the page.');
    return r.json();
  })
  .then(d => {
    if (d.success) bcToast('Order #' + orderId + ' → ' + sel.value);
    else bcToast('Update failed', 'error');
  })
  .catch(e => bcToast(e.message || 'Network error', 'error'));
}

// Image upload preview
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.img-upload-zone').forEach(zone => {
    const input   = zone.querySelector('input[type="file"]');
    const preview = zone.querySelector('.img-preview');
    if (!input || !preview) return;
    zone.addEventListener('click', () => input.click());
    input.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
      reader.readAsDataURL(file);
    });
  });
});

// Profile picture preview
document.addEventListener('DOMContentLoaded', function () {
  const pfpInput   = document.getElementById('pfpInput');
  const pfpPreview = document.getElementById('pfpPreview');
  if (pfpInput && pfpPreview) {
    pfpInput.addEventListener('change', function () {
      const reader = new FileReader();
      reader.onload = e => { pfpPreview.src = e.target.result; pfpPreview.style.display = 'block'; };
      reader.readAsDataURL(this.files[0]);
    });
  }
});

function confirmDelete(formId, label) {
  if (confirm('Delete "' + label + '"? This cannot be undone.')) {
    document.getElementById(formId)?.submit();
  }
}

// Mark contact as read via AJAX and toggle message body
function toggleMsg(id) {
  const body    = document.getElementById('msg-body-' + id);
  const chevron = document.getElementById('chevron-' + id);
  const open    = body.style.display === 'none';
  body.style.display      = open ? '' : 'none';
  chevron.style.transform = open ? 'rotate(180deg)' : '';

  if (open) {
    fetch(appUrl(`/admin/contacts/${id}/mark-read`), {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN':     getCsrfToken(),
        'Accept':           'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      }
    }).catch(() => {});
  }
}

function placeOrder() {
    BrewCart.load();

    if (!BrewCart.items.length) {
        bcToast('Cart is empty!', 'error');
        return;
    }

    const type = document.querySelector('input[name="orderType"]:checked')?.value || 'pickup';
    const notes = document.getElementById('orderNotes')?.value || '';

    // 🆕 payment fields
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'cod';
    const paymentReference = document.getElementById('paymentReference')?.value || '';

    fetch(appUrl('/orders'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            items: BrewCart.items,
            notes,
            type,
            payment_method: paymentMethod,
            payment_reference: paymentReference
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            BrewCart.clear();
            bcToast('Order placed successfully ☕');
            setTimeout(() => window.location.href = '/orders', 1500);
        } else {
            bcToast(d.message || 'Could not place order.', 'error');
        }
    })
    .catch(() => bcToast('Network error.', 'error'));
}

// Add-to-cart buttons (replaces inline onclick)
document.addEventListener('click', function (e) {
    const button = e.target.closest('.add-to-cart-btn');

    if (!button) return;

    const id    = button.dataset.id;
    const name  = button.dataset.name;
    const price = button.dataset.price;
    const img   = button.dataset.image || '';

    BrewCart.add(id, name, price, img);
});

// Flash messages
document.addEventListener('DOMContentLoaded', function () {
    const success = document.getElementById('flash-success');
    const error = document.getElementById('flash-error');

    if (success) {
        bcToast(success.dataset.message);
    }

    if (error) {
        bcToast(error.dataset.message, 'error');
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const config = document.getElementById('app-config');

    window.BC_JUST_LOGGED_IN = config
        ? config.dataset.justLoggedIn === '1'
        : false;

    if (window.BC_JUST_LOGGED_IN) {
        BrewCart.clearOtherUsers();
    }
});