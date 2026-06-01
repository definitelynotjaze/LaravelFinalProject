@extends('layouts.app')
@section('title', 'My Cart')

@section('content')
<div class="cart-wrap">
  <div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
      <a href="{{ route('home') }}#menu" style="color:var(--chestnut);text-decoration:none;font-size:.9rem;">
        <i class="bi bi-arrow-left me-1"></i> Continue Shopping
      </a>
      <div style="flex:1;height:1px;background:var(--cream);"></div>
      <h2 style="font-size:1.5rem;margin:0;">Your Cart</h2>
    </div>

    <div class="row g-4">
      {{-- Cart items --}}
      <div class="col-lg-8">
        <div class="bc-card">
          <div class="bc-card-head">
            <h5><i class="bi bi-bag me-2"></i>Cart Items</h5>
            <button onclick="BrewCart.clear()" class="btn-bc-ghost btn-bc-sm" id="clearCartBtn">
              <i class="bi bi-trash"></i> Clear Cart
            </button>
          </div>

          {{-- Empty state --}}
          <div id="cartEmpty" style="text-align:center;padding:4rem 2rem;display:none;">
            <div style="font-size:4rem;margin-bottom:1rem;">🛒</div>
            <h5 style="color:var(--dark);margin-bottom:.5rem;">Your cart is empty</h5>
            <p style="color:var(--chestnut);font-size:.9rem;margin-bottom:1.5rem;">
              Browse our menu and add items to get started.
            </p>
            <a href="{{ route('home') }}#menu" class="btn-bc">
              <i class="bi bi-cup-hot"></i> Browse Menu
            </a>
          </div>

          {{-- Items list --}}
          <div id="cartItems"></div>
        </div>

        {{-- Order notes --}}
        <div class="bc-card mt-4">
          <div class="bc-card-head"><h5>Order Notes</h5></div>
          <div class="bc-card-body">
            <textarea id="orderNotes" class="bc-textarea" rows="3"
              placeholder="Special requests, dietary notes, pick-up time preferences…"></textarea>
          </div>
        </div>
      </div>

      {{-- Summary --}}
      <div class="col-lg-4">
        <div id="cartSummaryWrap" style="display:none;">
          <div class="cart-summary mb-3">
            <div class="cart-summary-head">
              <h5>Order Summary</h5>
            </div>
            <div class="cart-summary-body">
              <div class="cart-row">
                <span>Subtotal</span>
                <span id="cartSubtotal">₱0.00</span>
              </div>
              <div class="cart-row">
                <span>Delivery Fee</span>
                <span id="cartDelivery">₱50.00</span>
              </div>
              <div class="cart-row total">
                <span>Total</span>
                <span id="cartTotal">₱0.00</span>
              </div>
            </div>
          </div>

          {{-- Order type --}}
          <div class="bc-card mb-3">
            <div class="bc-card-head"><h5>Order Type</h5></div>
            <div class="bc-card-body d-flex gap-2">
              <label style="flex:1;cursor:pointer;">
                <input type="radio" name="orderType" value="pickup" id="typePickup" checked style="display:none;">
                <div id="labelPickup" style="border:2px solid var(--mahogany);border-radius:var(--r);padding:.75rem;text-align:center;background:var(--mahogany);color:var(--white);">
                  <i class="bi bi-bag-check" style="font-size:1.2rem;display:block;"></i>
                  <div style="font-size:.82rem;font-weight:600;margin-top:.25rem;">Pick-up</div>
                </div>
              </label>
              <label style="flex:1;cursor:pointer;">
                <input type="radio" name="orderType" value="delivery" id="typeDelivery" style="display:none;">
                <div id="labelDelivery" style="border:2px solid var(--cream);border-radius:var(--r);padding:.75rem;text-align:center;color:var(--chestnut);">
                  <i class="bi bi-truck" style="font-size:1.2rem;display:block;"></i>
                  <div style="font-size:.82rem;font-weight:600;margin-top:.25rem;">Delivery</div>
                </div>
              </label>
            </div>
          </div>

          @auth
          <div class="bc-card mb-3">
  <div class="bc-card-head"><h5>Payment Method</h5></div>
  <div class="bc-card-body d-flex flex-column gap-2">

    <label style="cursor:pointer;">
      <input type="radio" name="paymentMethod" value="cod">
      Cash on Delivery (CoD)
    </label>

    <label style="cursor:pointer;">
      <input type="radio" name="paymentMethod" value="gcash">
      GCash
    </label>

    <label style="cursor:pointer;">
      <input type="radio" name="paymentMethod" value="bank">
      Bank Transfer
    </label>

    <input type="text"
           id="paymentReference"
           class="bc-input mt-2"
           placeholder="Reference Number (GCash / Bank Transfer)"
           style="display:none;">
  </div>
</div>

          <button onclick="placeOrder()" class="btn-bc btn-bc-lg w-100">
            <i class="bi bi-check-circle"></i> Place Order
          </button>
          <p style="text-align:center;font-size:.78rem;color:var(--chestnut);margin-top:.75rem;">
            <i class="bi bi-shield-check me-1"></i>Secure checkout · Easy cancellation
          </p>
          @else
          <a href="{{ route('login') }}" class="btn-bc btn-bc-lg w-100">
            <i class="bi bi-box-arrow-in-right"></i> Login to Place Order
          </a>
          @endauth
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Payment Receipt Modal --}}
<div id="receiptModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.55);align-items:center;justify-content:center;padding:1rem;">
  <div id="receiptBox" style="background:#fff;border-radius:12px;max-width:480px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25);">
    <div style="padding:2rem 2rem 1rem;text-align:center;border-bottom:2px dashed #e8ddd0;">
      <div style="font-size:2rem;margin-bottom:.25rem;">☕</div>
      <div style="font-family:Georgia,serif;font-size:1.5rem;font-weight:700;color:#4a2c1a;">Brew<em>Craft</em></div>
      <div style="font-size:.78rem;color:#888;margin-top:.2rem;">Order Confirmation Receipt</div>
    </div>

    <div style="padding:1.25rem 2rem;">
      <div style="display:flex;justify-content:space-between;font-size:.82rem;color:#666;margin-bottom:.5rem;">
        <span id="rcptOrderNo" style="font-weight:700;color:#4a2c1a;font-size:.95rem;"></span>
        <span id="rcptDate"></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:.82rem;color:#666;margin-bottom:1rem;">
        <span id="rcptType" style="background:#f5ede4;padding:.15rem .6rem;border-radius:50px;font-size:.75rem;"></span>
        <span id="rcptPayment" style="color:#7a5230;font-size:.78rem;"></span>
      </div>

      <div style="border-top:1px solid #e8ddd0;padding-top:.85rem;margin-bottom:.85rem;">
        <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#999;margin-bottom:.6rem;">Items Ordered</div>
        <div id="rcptItems"></div>
      </div>

      <div style="border-top:2px dashed #e8ddd0;padding-top:.85rem;">
        <div style="display:flex;justify-content:space-between;font-size:.85rem;color:#666;margin-bottom:.3rem;">
          <span>Subtotal</span><span id="rcptSubtotal"></span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.85rem;color:#666;margin-bottom:.5rem;">
          <span>Delivery Fee</span><span id="rcptDelivery"></span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:700;color:#4a2c1a;font-family:Georgia,serif;">
          <span>Total</span><span id="rcptTotal"></span>
        </div>
        <div id="rcptRefWrap" style="display:none;margin-top:.5rem;font-size:.78rem;color:#888;">
          Reference: <span id="rcptRef" style="font-weight:600;color:#4a2c1a;"></span>
        </div>
      </div>

      <div style="text-align:center;margin-top:1.25rem;padding:.85rem;background:#f9f5f0;border-radius:8px;font-size:.78rem;color:#7a5230;">
        <i class="bi bi-clock me-1"></i> Your order is being processed. You'll be notified when it's ready.
      </div>
    </div>

    <div style="padding:1rem 2rem 1.5rem;display:flex;gap:.75rem;" id="rcptActions">
      <button onclick="printReceipt()" class="btn-bc" style="flex:1;display:flex;align-items:center;justify-content:center;gap:.4rem;">
        <i class="bi bi-printer"></i> Print Receipt
      </button>
      <a href="{{ route('orders.index') }}" class="btn-bc-ghost" style="flex:1;display:flex;align-items:center;justify-content:center;gap:.4rem;text-decoration:none;">
        <i class="bi bi-bag-check"></i> My Orders
      </a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Order type toggle styling
document.querySelectorAll('input[name="orderType"]').forEach(radio => {
  radio.addEventListener('change', function () {
    const labels = { pickup: document.getElementById('labelPickup'), delivery: document.getElementById('labelDelivery') };
    Object.entries(labels).forEach(([val, el]) => {
      if (val === this.value) {
        el.style.borderColor  = 'var(--mahogany)';
        el.style.background   = 'var(--mahogany)';
        el.style.color        = 'var(--white)';
      } else {
        el.style.borderColor  = 'var(--cream)';
        el.style.background   = '';
        el.style.color        = 'var(--chestnut)';
      }
    });
  });
});

// Show/hide reference number field based on payment method
document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
  radio.addEventListener('change', function () {
    const refField = document.getElementById('paymentReference');
    if (refField) {
      refField.style.display = (this.value === 'gcash' || this.value === 'bank') ? '' : 'none';
      if (this.value === 'cod') refField.value = '';
    }
  });
});

// Set default payment method
document.addEventListener('DOMContentLoaded', function () {
  const defaultPay = document.querySelector('input[name="paymentMethod"][value="cod"]');
  if (defaultPay) defaultPay.checked = true;
});

function applyPromo() {
  const code = document.getElementById('promoCode')?.value.trim();
  if (!code) return;
  bcToast('Promo code feature coming soon!', 'info');
}

// Override placeOrder to include payment fields + show receipt
window.placeOrder = function () {
  BrewCart.load();
  if (!BrewCart.items.length) { bcToast('Cart is empty!', 'error'); return; }

  const token            = document.querySelector('meta[name="csrf-token"]').content;
  const type             = document.querySelector('input[name="orderType"]:checked')?.value || 'pickup';
  const notes            = document.getElementById('orderNotes')?.value || '';
  const paymentMethod    = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'cod';
  const paymentReference = document.getElementById('paymentReference')?.value || '';

  if ((paymentMethod === 'gcash' || paymentMethod === 'bank') && !paymentReference.trim()) {
    bcToast('Please enter your payment reference number.', 'error');
    return;
  }

  // Snapshot cart before clearing
  const cartSnapshot = [...BrewCart.items];

  fetch(appUrl('/orders'), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token,
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({
      items: cartSnapshot,
      notes,
      type,
      payment_method:    paymentMethod,
      payment_reference: paymentReference,
    })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      BrewCart.clear();
      showReceipt(d, cartSnapshot, type, paymentMethod, paymentReference);
    } else {
      bcToast(d.message || 'Could not place order.', 'error');
    }
  })
  .catch(() => bcToast('Network error.', 'error'));
};

// Store receipt data so printReceipt() can rebuild cleanly from data, not DOM
let _rcptData = {};

function showReceipt(data, items, type, paymentMethod, paymentRef) {
  const delivery = type === 'delivery' ? 50 : 0;
  const subtotal = items.reduce((s, i) => s + (i.price * i.qty), 0);
  const total    = subtotal + delivery;
  const orderId  = String(data.order_id).padStart(4, '0');
  const now      = new Date();
  const dateStr  = now.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' })
                 + ' \u00b7 ' + now.toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit' });
  const methodLabels = { cod: 'Cash on Delivery', gcash: 'GCash', bank: 'Bank Transfer' };

  // Save snapshot for print
  _rcptData = { orderId, dateStr, type, paymentMethod, paymentRef, items, subtotal, delivery, total,
                methodLabel: methodLabels[paymentMethod] || paymentMethod };

  // Populate modal DOM
  document.getElementById('rcptOrderNo').textContent  = 'Order #' + orderId;
  document.getElementById('rcptDate').textContent     = dateStr;
  document.getElementById('rcptType').textContent     = type === 'delivery' ? '\uD83D\uDE9A Delivery' : '\uD83C\uDFEA Pick-up';
  document.getElementById('rcptSubtotal').textContent = '\u20b1' + subtotal.toFixed(2);
  document.getElementById('rcptDelivery').textContent = delivery > 0 ? '\u20b1' + delivery.toFixed(2) : 'Free';
  document.getElementById('rcptTotal').textContent    = '\u20b1' + total.toFixed(2);
  document.getElementById('rcptPayment').textContent  = _rcptData.methodLabel;

  if (paymentRef) {
    document.getElementById('rcptRefWrap').style.display = '';
    document.getElementById('rcptRef').textContent = paymentRef;
  } else {
    document.getElementById('rcptRefWrap').style.display = 'none';
  }

  document.getElementById('rcptItems').innerHTML = items.map(i =>
    '<div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:.35rem;">' +
      '<span>' + i.name + ' <span style="color:#999;">\u00d7' + i.qty + '</span></span>' +
      '<span style="font-weight:600;color:#4a2c1a;">\u20b1' + (i.price * i.qty).toFixed(2) + '</span>' +
    '</div>'
  ).join('');

  document.getElementById('receiptModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function printReceipt() {
  const d = _rcptData;
  if (!d.orderId) return;

  const itemRows = d.items.map(i =>
    '<tr>' +
      '<td style="padding:.35rem 0;font-size:.88rem;">' + i.name + '</td>' +
      '<td style="padding:.35rem 0;font-size:.88rem;text-align:center;color:#999;">' + i.qty + '</td>' +
      '<td style="padding:.35rem 0;font-size:.88rem;text-align:right;">&#8369;' + i.price.toFixed(2) + '</td>' +
      '<td style="padding:.35rem 0;font-size:.88rem;text-align:right;font-weight:600;color:#4a2c1a;">&#8369;' + (i.price * i.qty).toFixed(2) + '</td>' +
    '</tr>'
  ).join('');

  const refRow = d.paymentRef
    ? '<tr><td colspan="4" style="padding-top:.5rem;font-size:.78rem;color:#888;">Reference: <strong style="color:#4a2c1a;">' + d.paymentRef + '</strong></td></tr>'
    : '';

  const html =
    '<!DOCTYPE html><html><head>' +
    '<meta charset="utf-8">' +
    '<title>BrewCraft Receipt \u2014 Order #' + d.orderId + '</title>' +
    '<style>' +
    '* { box-sizing: border-box; margin: 0; padding: 0; }' +
    'body { font-family: Arial, sans-serif; background: #fff; color: #333; display: flex; justify-content: center; padding: 2rem 1rem; }' +
    '.receipt { width: 100%; max-width: 420px; }' +
    '.header { text-align: center; padding-bottom: 1rem; border-bottom: 2px dashed #e8ddd0; margin-bottom: 1rem; }' +
    '.header .emoji { font-size: 2rem; margin-bottom: .2rem; }' +
    '.header .brand { font-family: Georgia, serif; font-size: 1.5rem; font-weight: 700; color: #4a2c1a; }' +
    '.header .brand em { font-style: italic; }' +
    '.header .sub { font-size: .75rem; color: #888; margin-top: .15rem; }' +
    '.meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: .4rem; font-size: .82rem; color: #666; }' +
    '.meta .order-no { font-weight: 700; color: #4a2c1a; font-size: .95rem; }' +
    '.meta .badge { background: #f5ede4; padding: .15rem .6rem; border-radius: 50px; font-size: .75rem; color: #7a5230; }' +
    '.meta .payment { color: #7a5230; font-size: .78rem; }' +
    '.section-label { font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #999; margin-bottom: .5rem; padding-top: .85rem; border-top: 1px solid #e8ddd0; }' +
    'table { width: 100%; border-collapse: collapse; }' +
    'thead th { font-size: .7rem; color: #aaa; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; padding-bottom: .35rem; border-bottom: 1px solid #f0e8e0; text-align: left; }' +
    'thead th:nth-child(2) { text-align: center; }' +
    'thead th:nth-child(3), thead th:nth-child(4) { text-align: right; }' +
    '.totals { border-top: 2px dashed #e8ddd0; margin-top: .75rem; padding-top: .85rem; }' +
    '.totals table td { padding: .25rem 0; font-size: .85rem; color: #666; }' +
    '.totals table td:last-child { text-align: right; }' +
    '.totals .grand td { font-size: 1.05rem; font-weight: 700; color: #4a2c1a; font-family: Georgia, serif; padding-top: .5rem; }' +
    '.notice { margin-top: 1rem; padding: .75rem; background: #f9f5f0; border-radius: 8px; font-size: .76rem; color: #7a5230; text-align: center; }' +
    '@media print { body { padding: 0; } }' +
    '</style></head><body>' +
    '<div class="receipt">' +
      '<div class="header">' +
        '<div class="emoji">&#9749;</div>' +
        '<div class="brand">Brew<em>Craft</em></div>' +
        '<div class="sub">Order Confirmation Receipt</div>' +
      '</div>' +
      '<div class="meta"><span class="order-no">Order #' + d.orderId + '</span><span>' + d.dateStr + '</span></div>' +
      '<div class="meta"><span class="badge">' + (d.type === 'delivery' ? '&#128666; Delivery' : '&#127978; Pick-up') + '</span><span class="payment">' + d.methodLabel + '</span></div>' +
      '<div class="section-label">Items Ordered</div>' +
      '<table>' +
        '<thead><tr><th>Item</th><th style="text-align:center;">Qty</th><th style="text-align:right;">Price</th><th style="text-align:right;">Total</th></tr></thead>' +
        '<tbody>' + itemRows + '</tbody>' +
      '</table>' +
      '<div class="totals">' +
        '<table>' +
          '<tr><td>Subtotal</td><td>&#8369;' + d.subtotal.toFixed(2) + '</td></tr>' +
          '<tr><td>Delivery Fee</td><td>' + (d.delivery > 0 ? '&#8369;' + d.delivery.toFixed(2) : 'Free') + '</td></tr>' +
          '<tr class="grand"><td>Total</td><td>&#8369;' + d.total.toFixed(2) + '</td></tr>' +
          refRow +
        '</table>' +
      '</div>' +
      '<div class="notice">&#9200; Your order is being processed. You\'ll be notified when it\'s ready.</div>' +
    '</div>' +
    '</body></html>';

  const blob = new Blob([html], { type: 'text/html' });
  const url  = URL.createObjectURL(blob);
  const win  = window.open(url, '_blank', 'width=560,height=720');
  win.addEventListener('load', function () {
    URL.revokeObjectURL(url);
    setTimeout(function () { win.print(); win.close(); }, 300);
  });
}
</script>
@endpush
