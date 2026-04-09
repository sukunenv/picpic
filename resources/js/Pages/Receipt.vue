<template>
  <div class="receipt-container">
    <div class="no-print actions">
      <button @click="backToHome" class="btn-back">← Kembali</button>
      <a :href="`rawbt:` + receiptUrl" class="btn-print">
        Cetak Struk
      </a>
    </div>

    <div class="receipt-content" id="printable-receipt">
      <div class="header">
        <img src="/logo.png" class="receipt-logo" alt="Logo">
        <h1 class="shop-name">Kedai PICPIC</h1>
        <p class="tagline">kumpul mencerita</p>
      </div>

      <div class="divider">================================</div>

      <div class="info-section">
        <div class="info-row">
          <span>No: #PIC-{{ order.id.toString().padStart(5, '0') }}</span>
        </div>
        <div class="info-row">
          <span>Tgl: {{ formatDate(order.created_at) }}</span>
        </div>
        <div class="info-row">
          <span>Meja: {{ order.table_number || '-' }}</span>
        </div>
        <div class="info-row">
          <span>Nama: {{ order.customer_name }}</span>
        </div>

      </div>

      <div class="divider">--------------------------------</div>

      <div class="items-section">
        <div v-for="item in order.order_items" :key="item.id" class="item-block">
          <div class="item-main">
            <div class="qty-name">
              <span class="qty">{{ item.quantity }}x</span>
              <span class="name">{{ item.menu_item.name }}</span>
            </div>
            <span class="price-val">{{ formatPrice(item.price * item.quantity) }}</span>
          </div>
          <div v-if="item.note" class="item-note">({{ item.note }})</div>
        </div>
      </div>

      <div class="divider">--------------------------------</div>

      <div class="total-section">
        <div class="total-row">
          <span>Subtotal</span>
          <span>{{ formatPrice(order.total_price) }}</span>
        </div>
        <div class="total-row">
          <span>Biaya Layanan</span>
          <span>Gratis</span>
        </div>
        <div class="divider-thin">--------------------------------</div>
        <div class="total-row grand-total">
          <span>TOTAL</span>
          <span>{{ formatPrice(order.total_price) }}</span>
        </div>
      </div>

      <div class="divider">================================</div>

      <div class="payment-info">
        <p>Pembayaran: {{ order.payment_method.toUpperCase() }}</p>
        <p>Status: {{ order.payment_status === 'paid' ? 'LUNAS ✓' : 'BELUM BAYAR' }}</p>
      </div>

      <div class="divider">================================</div>

      <div class="footer">
        <div class="footer-info">
          <p class="address">Wonosari, Gunungkidul</p>
          <p>IG: @picpic.cafe</p>
          <p class="website">kedaipicpic.com</p>
        </div>
        
        <div class="divider-dot">................................</div>
        
        <div class="footer-thanks">
          <p>Jam buka:</p>
          <p>Minggu-Kamis 16.00-23.00 WIB</p>
          <p>Jumat-Sabtu 16.00-00.00 WIB</p>
          <p>Terima kasih sudah berkunjung!</p>
          <p>Sampai jumpa lagi 😊</p>
        </div>
      </div>

      <div class="divider">================================</div>

      <div class="bottom-spacer"></div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  order: Object
});

const formatPrice = (price) => {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
};

const formatDate = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const receiptUrl = window.location.href;

const backToHome = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/admin/orders');
    }
};

onMounted(() => {
  // Autoprint removed for RawBT to avoid infinite loops/confusion
  // as it requires user interaction for the protocol link
});
</script>

<style scoped>
.receipt-container {
  font-family: 'Courier New', Courier, monospace;
  background: #f9fafb;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  color: black;
}

.receipt-content {
  width: 80mm;
  padding: 4mm 3mm;
  background: white;
  font-size: 11px;
  line-height: 1.4;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.header {
  text-align: center;
  margin-bottom: 8px;
}

.receipt-logo {
  width: 45px;
  height: 45px;
  object-fit: contain;
  margin-bottom: 4px;
}

.shop-name {
  font-size: 16px;
  font-weight: bold;
  margin: 0;
  letter-spacing: 0.5px;
}

.tagline {
  font-size: 10px;
  margin: 0;
  color: #333;
}

.divider {
  text-align: center;
  margin: 6px 0;
  white-space: nowrap;
  overflow: hidden;
  font-weight: bold;
  letter-spacing: -1px;
}

.info-section {
  margin: 8px 0;
}

.info-row {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 1px;
}

.items-section {
  margin: 8px 0;
}

.item-block {
  margin-bottom: 6px;
}

.item-main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 4px;
}

.qty-name {
  display: flex;
  gap: 6px;
  max-width: 70%;
}

.qty {
  font-weight: bold;
  white-space: nowrap;
}

.name {
  text-transform: capitalize;
  word-break: break-word;
}

.price-val {
  text-align: right;
  font-weight: bold;
  white-space: nowrap;
}

.item-note {
  font-size: 9px;
  font-style: italic;
  padding-left: 20px;
  color: #444;
  margin-top: 1px;
}

.total-section {
  margin: 8px 0;
}

.total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 2px;
}

.divider-thin {
  text-align: center;
  margin: 4px 0;
  color: #999;
}

.grand-total {
  font-weight: bold;
  font-size: 14px;
  margin-top: 4px;
}

.payment-info {
  text-align: center;
  font-weight: bold;
  margin: 10px 0;
  font-size: 12px;
}

.footer {
  text-align: center;
  margin: 12px 0;
}

.footer-info {
  font-size: 10px;
  margin-bottom: 10px;
}

.footer-info p {
  margin: 2px 0;
}

.address {
  font-weight: bold;
}

.website {
  font-size: 8.5px;
  color: #555;
  margin-top: 2px;
}

.divider-dot {
  text-align: center;
  margin: 10px 0;
  color: #999;
}

.footer-thanks {
  font-size: 10px;
}

.footer-thanks p {
  margin: 4px 0;
}

.bottom-spacer {
  height: 20mm;
}

/* UI Actions */
.actions {
  padding: 16px;
  display: flex;
  gap: 12px;
  background: white;
  width: 100%;
  justify-content: center;
  border-bottom: 1px solid #e5e7eb;
}

.btn-back {
  background: #f3f4f6;
  border: none;
  padding: 8px 16px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  font-size: 13px;
}

.btn-print {
  background: #6367FF;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  font-size: 13px;
}

@media print {
  @page {
    margin: 0;
    size: 80mm auto;
  }

  body {
    width: 80mm;
    margin: 0;
    padding: 0;
  }

  .receipt-container {
    background: transparent;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: auto;
  }

  .no-print {
    display: none !important;
  }

  .receipt-content {
    width: 80mm;
    max-width: 80mm;
    padding: 2mm 3mm;
    margin: 0 auto;
    box-shadow: none;
  }
}
</style>
