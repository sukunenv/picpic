<template>
  <AppLayout title="Status Pesanan">
    <div class="status-page">
      <!-- ── SUCCESS ANIMATION ────────────────────────────── -->
      <div class="header-status">
        <div class="check-animation">
          <div class="circle"></div>
          <CheckIcon class="check-icon" />
        </div>
        <h1 class="success-title">Pesanan Berhasil!</h1>
        <p class="order-id-label">Nomor Order: <span class="order-id">#PIC-{{ order.id.toString().padStart(3, '0') }}</span></p>
      </div>

      <!-- ── PAYMENT BADGE ────────────────────────────────── -->
      <div class="badge-container">
        <div 
          class="payment-badge" 
          :class="order.payment_status === 'paid' ? 'lunas' : 'kasir'"
        >
          {{ order.payment_status === 'paid' ? 'LUNAS' : 'BAYAR DI KASIR' }}
        </div>
      </div>

      <!-- ── MESSAGE ──────────────────────────────────────── -->
      <div class="message-card">
        <p v-if="order.payment_method === 'qris'">
          Pembayaran diterima! <br>Pesanan sedang diproses 🎉
        </p>
        <p v-else>
          Pesanan masuk! <br>Nikmati dan bayar di kasir saat pulang ya 😊
        </p>
      </div>

      <!-- ── ORDER SUMMARY ────────────────────────────────── -->
      <div class="summary-card">
        <h3 class="summary-title">Ringkasan Pesanan</h3>
        <div class="items-list">
          <div v-for="item in order.order_items" :key="item.id" class="item-row">
            <span class="qty">{{ item.quantity }}x</span>
            <span class="name">{{ item.menu_item?.name }}</span>
            <span class="price">{{ formatPrice(item.price * item.quantity) }}</span>
          </div>
        </div>
        <div class="summary-divider"></div>
        <div class="total-row">
          <span>Total Pembayaran</span>
          <span class="total-val">{{ formatPrice(order.total_price) }}</span>
        </div>
      </div>

      <!-- ── ACTIONS ──────────────────────────────────────── -->
      <div class="actions">
        <Link href="/" class="btn-home">Kembali ke Beranda</Link>
        <p class="footer-note">Tunjukkan halaman ini ke kasir jika diperlukan</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { CheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  order: Object
});

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}
</script>

<style scoped>
.status-page {
  padding: 40px 20px;
  background: #F0EEFF;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Animation */
.header-status { text-align: center; margin-bottom: 24px; }

.check-animation {
  position: relative;
  width: 80px;
  height: 80px;
  margin: 0 auto 16px;
}

.circle {
  width: 100%;
  height: 100%;
  background: #10B981;
  border-radius: 50%;
  animation: circleScale 0.5s ease-out forwards;
}

.check-icon {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 40px;
  height: 40px;
  color: white;
  stroke-width: 3.5;
  animation: checkDraw 0.4s 0.3s ease-out both;
}

@keyframes circleScale {
  0% { transform: scale(0); opacity: 0; }
  70% { transform: scale(1.1); }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes checkDraw {
  from { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
  to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
}

.success-title { font-size: 22px; font-weight: 800; color: #1B1B1B; margin: 0 0 4px; }
.order-id-label { font-size: 14px; color: #6B7280; margin: 0; }
.order-id { color: #7C6BC4; font-weight: 800; }

/* Badge */
.badge-container { margin-bottom: 20px; }
.payment-badge {
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: 0.5px;
}
.payment-badge.lunas { background: #10B981; color: white; }
.payment-badge.kasir { background: #FBBF24; color: #1B1B1B; }

/* Message */
.message-card {
  background: white;
  width: 100%;
  padding: 20px;
  border-radius: 20px;
  text-align: center;
  margin-bottom: 20px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.04);
}
.message-card p { margin: 0; font-size: 15px; font-weight: 600; color: #374151; line-height: 1.6; }

/* Summary */
.summary-card {
  background: white;
  width: 100%;
  padding: 20px;
  border-radius: 20px;
  margin-bottom: 32px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.04);
}
.summary-title { font-size: 15px; font-weight: 800; color: #1B1B1B; margin: 0 0 16px; }
.items-list { display: flex; flex-direction: column; gap: 10px; }
.item-row { display: flex; align-items: flex-start; font-size: 14px; }
.qty { font-weight: 800; color: #7C6BC4; width: 30px; }
.name { flex: 1; color: #4B5563; }
.price { font-weight: 600; color: #1B1B1B; }

.summary-divider { height: 1px; background: #F3F4F6; margin: 16px 0; border: none; }
.total-row { display: flex; justify-content: space-between; font-weight: 800; font-size: 16px; }
.total-val { color: #7C6BC4; }

/* Actions */
.actions { width: 100%; text-align: center; }
.btn-home {
  display: block;
  width: 100%;
  padding: 16px;
  background: #7C6BC4;
  color: white;
  text-decoration: none;
  border-radius: 16px;
  font-weight: 700;
  margin-bottom: 16px;
  box-shadow: 0 4px 12px rgba(124, 107, 196, 0.3);
}
.footer-note { font-size: 12px; color: #9CA3AF; margin: 0; }
</style>
