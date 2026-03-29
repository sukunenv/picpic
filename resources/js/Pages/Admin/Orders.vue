<template>
  <AdminLayout title="Kelola Pesanan">
    <div class="orders-page">
      <div class="page-header">
        <div>
          <h2 class="page-title">Monitoring Pesanan</h2>
          <p class="page-sub">Terakhir diperbarui: {{ lastUpdate }}</p>
        </div>
        <div class="header-actions">
          <div class="refresh-indicator" :class="{ refreshing }">
            <ArrowPathIcon class="h-4 w-4" :class="{ 'animate-spin': refreshing }" />
            <span>Auto-refresh aktif (5s)</span>
          </div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="filters-container">
        <div class="tabs-row">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            class="filter-tab"
            :class="{ active: activeTab === tab.id }"
            @click="activeTab = tab.id"
          >
            {{ tab.label }}
            <span class="tab-badge" v-if="getCount(tab.id) > 0">{{ getCount(tab.id) }}</span>
          </button>
        </div>
        
        <div class="toggle-row">
          <label class="toggle-label">
            <input type="checkbox" v-model="onlyUnpaid" />
            <span class="toggle-text">Hanya Belum Bayar</span>
          </label>
        </div>
      </div>

      <!-- Orders List -->
      <div class="orders-grid">
        <div v-if="filteredOrders.length === 0" class="empty-orders">
          <div class="empty-icon">☕</div>
          <h3>Tidak ada pesanan</h3>
          <p>Belum ada pesanan yang sesuai dengan filter saat ini.</p>
        </div>

        <div
          v-for="order in filteredOrders"
          :key="order.id"
          class="order-card"
          :class="{ 'border-pending': order.status === 'pending' }"
        >
          <div class="order-card-header">
            <div class="order-meta">
              <span class="order-num">#{{ order.id.toString().padStart(5, '0') }}</span>
              <span class="order-time">{{ formatTime(order.created_at) }}</span>
            </div>
            <div class="order-badges">
              <span class="badge-payment" :class="order.payment_status">
                {{ order.payment_status === 'paid' ? 'LUNAS' : 'BAYAR DI KASIR' }}
              </span>
              <span class="badge-status" :class="order.status">
                {{ order.status.toUpperCase() }}
              </span>
            </div>
          </div>

          <div class="order-customer">
            <div class="cust-row">
              <UserIcon class="h-4 w-4 text-gray-400" />
              <span class="cust-name">{{ order.customer_name }}</span>
            </div>
            <div class="cust-row" v-if="order.table_number">
              <HashtagIcon class="h-4 w-4 text-gray-400" />
              <span class="cust-table">Meja: {{ order.table_number }}</span>
            </div>
          </div>

          <div class="order-items-list">
            <div v-for="item in order.order_items" :key="item.id" class="order-item-row">
              <span class="item-qty">{{ item.quantity }}x</span>
              <span class="item-name">{{ item.menu_item?.name }}</span>
              <p v-if="item.note" class="item-note">"{{ item.note }}"</p>
            </div>
          </div>

          <div class="order-card-footer">
            <div class="order-total">
              <span class="total-label">Total:</span>
              <span class="total-val">{{ formatPrice(order.total_price) }}</span>
            </div>
            
            <div class="order-actions">
              <!-- Action: Tandai Bayar -->
              <button
                v-if="order.payment_status === 'unpaid'"
                class="btn-action pay"
                @click="openPayModal(order)"
              >
                Tandai Dibayar
              </button>

              <!-- Action: Proses -->
              <button
                v-if="order.status === 'pending'"
                class="btn-action process"
                @click="updateStatus(order.id, 'processing')"
              >
                Proses
              </button>

              <!-- Action: Selesai -->
              <button
                v-if="order.status === 'processing'"
                class="btn-action done"
                @click="updateStatus(order.id, 'done')"
              >
                Selesai
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── PAYMENT MODAL ────────────────────────────────────── -->
    <transition name="modal">
      <div v-if="showPayModal" class="modal-overlay" @click.self="showPayModal = false">
        <div class="admin-modal">
          <div class="modal-header">
            <h3>Konfirmasi Pembayaran</h3>
            <button @click="showPayModal = false" class="close-btn">×</button>
          </div>
          <div class="modal-body">
            <p class="modal-info">Pilih metode pembayaran untuk Order <strong>#{{ selectedOrder?.id }}</strong> ({{ selectedOrder?.customer_name }}):</p>
            
            <div class="pay-options">
              <button 
                v-for="opt in payOptions" 
                :key="opt.id"
                class="pay-opt-btn"
                @click="confirmPayment(opt.id)"
              >
                <component :is="opt.icon" class="h-5 w-5" />
                <span>{{ opt.label }}</span>
              </button>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="showPayModal = false" class="btn-cancel">Batal</button>
          </div>
        </div>
      </div>
    </transition>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowPathIcon, 
  UserIcon, 
  HashtagIcon,
  CheckCircleIcon,
  BanknotesIcon,
  QrCodeIcon,
  ArrowsRightLeftIcon as TransferIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  orders: Array
});

const activeTab = ref('semua');
const onlyUnpaid = ref(false);
const refreshing = ref(false);
const lastUpdate = ref(new Date().toLocaleTimeString());

// Modal State
const showPayModal = ref(false);
const selectedOrder = ref(null);

const payOptions = [
  { id: 'cash',     label: 'Cash / Tunai',     icon: BanknotesIcon },
  { id: 'qris',     label: 'QRIS (di Kasir)',   icon: QrCodeIcon },
  { id: 'transfer', label: 'Transfer Bank',   icon: TransferIcon },
];

const tabs = [
  { id: 'semua',     label: 'Semua' },
  { id: 'pending',   label: 'Pending' },
  { id: 'processing',label: 'Diproses' },
  { id: 'done',      label: 'Selesai' },
];

const filteredOrders = computed(() => {
  let list = props.orders;
  if (activeTab.value !== 'semua') {
    list = list.filter(o => o.status === activeTab.value);
  }
  if (onlyUnpaid.value) {
    list = list.filter(o => o.payment_status === 'unpaid');
  }
  return list;
});

function getCount(status) {
  if (status === 'semua') return props.orders.length;
  return props.orders.filter(o => o.status === status).length;
}

// ── AUDIO NOTIFIKASI ──────────────────────────────────
function playDing() {
  const audioContext = new (window.AudioContext || window.webkitAudioContext)();
  const oscillator = audioContext.createOscillator();
  const gainNode = audioContext.createGain();

  oscillator.connect(gainNode);
  gainNode.connect(audioContext.destination);

  oscillator.type = 'sine';
  oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // A5
  oscillator.frequency.exponentialRampToValueAtTime(440, audioContext.currentTime + 0.5); // A4

  gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
  gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

  oscillator.start();
  oscillator.stop(audioContext.currentTime + 0.5);
}

// ── AUTO REFRESH ──────────────────────────────────────
let refreshInterval = null;

function refreshData() {
  refreshing.value = true;
  router.reload({
    only: ['orders'],
    onFinish: () => {
      refreshing.value = false;
      lastUpdate.value = new Date().toLocaleTimeString();
    }
  });
}

// Track new orders for sound
watch(() => props.orders.length, (newVal, oldVal) => {
  if (newVal > oldVal && oldVal !== 0) {
    playDing();
  }
});

onMounted(() => {
  refreshInterval = setInterval(refreshData, 5000);
});

onUnmounted(() => {
  clearInterval(refreshInterval);
});

// ── ACTIONS ───────────────────────────────────────────
function updateStatus(id, status) {
  router.post(route('admin.orders.status', id), { status }, {
    preserveScroll: true
  });
}

function openPayModal(order) {
  selectedOrder.value = order;
  showPayModal.value = true;
}

function confirmPayment(method) {
  if (!selectedOrder.value) return;
  
  updatePayment(selectedOrder.value.id, 'paid', method);
  showPayModal.value = false;
}

function updatePayment(id, payment_status, payment_method = null) {
  router.post(route('admin.orders.payment', id), { 
    payment_status,
    payment_method
  }, {
    preserveScroll: true
  });
}

// ── FORMATTERS ────────────────────────────────────────
function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}

function formatTime(dateStr) {
  return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
</script>

<style scoped>
.orders-page {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title { font-size: 24px; font-weight: 800; color: #1B1B1B; margin: 0; }
.page-sub { font-size: 13px; color: #6B7280; margin: 4px 0 0; }

.refresh-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  background: white;
  padding: 8px 16px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 600;
  color: #7C6BC4;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  transition: all 0.3s;
}

.refresh-indicator.refreshing {
  background: #F0EEFF;
  color: #9B8FD4;
}

/* Filters */
.filters-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}

.tabs-row {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 4px;
  scrollbar-width: none;
}

.tabs-row::-webkit-scrollbar { display: none; }

.filter-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: white;
  border: 2px solid transparent;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  color: #6B7280;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.filter-tab.active {
  border-color: #7C6BC4;
  color: #7C6BC4;
  background: #F0EEFF;
}

.tab-badge {
  background: #7C6BC4;
  color: white;
  font-size: 11px;
  padding: 2px 6px;
  border-radius: 6px;
}

.toggle-row {
  display: flex;
  align-items: center;
}

.toggle-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  background: white;
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.toggle-label input { width: 18px; height: 18px; accent-color: #7C6BC4; }

/* Grid Orders */
.orders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
}

@media (max-width: 640px) {
  .orders-grid { grid-template-columns: 1fr; }
}

.order-card {
  background: white;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.04);
  display: flex;
  flex-direction: column;
  border-top: 5px solid transparent;
  transition: all 0.3s;
}

.order-card.border-pending { border-top-color: #D1D5DB; }
.order-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.order-meta { display: flex; flex-direction: column; }
.order-num { font-size: 18px; font-weight: 800; color: #7C6BC4; }
.order-time { font-size: 12px; color: #9CA3AF; font-weight: 600; }

.order-badges { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }

.badge-payment {
  font-size: 9px;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 4px;
}

.badge-payment.unpaid { background: #FEF3C7; color: #92400E; } /* Kuning */
.badge-payment.paid { background: #DCFCE7; color: #166534; }   /* Hijau */

.badge-status {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 4px;
  background: #F3F4F6;
  color: #6B7280;
}

.badge-status.processing { background: #E0F2FE; color: #0369A1; }
.badge-status.done { background: #EDE9FE; color: #5B21B6; }

.order-customer {
  background: #F9FAFB;
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 16px;
}

.cust-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.cust-row:last-child { margin-bottom: 0; }
.cust-name { font-weight: 700; font-size: 14px; color: #1B1B1B; }
.cust-table { font-weight: 600; font-size: 12px; color: #6B7280; }

.order-items-list {
  flex: 1;
  margin-bottom: 20px;
}

.order-item-row {
  padding: 6px 0;
  border-bottom: 1px dashed #F0F0F0;
}

.order-item-row:last-child { border-bottom: none; }

.item-qty { font-weight: 800; color: #7C6BC4; margin-right: 8px; font-size: 14px; }
.item-name { font-size: 14px; font-weight: 600; color: #374151; }
.item-note { font-size: 11px; color: #ef4444; font-weight: 500; margin: 2px 0 0 24px; font-style: italic; }

.order-card-footer {
  border-top: 1px solid #F3F4F6;
  padding-top: 16px;
}

.order-total {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
}

.total-label { font-size: 14px; font-weight: 600; color: #6B7280; }
.total-val { font-size: 18px; font-weight: 800; color: #7C6BC4; }

.order-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.btn-action {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-action.pay { background: #FDE68A; color: #92400E; }
.btn-action.pay:hover { background: #FCD34D; }

.btn-action.process { background: #7C6BC4; color: white; }
.btn-action.process:hover { background: #5A4DA0; }

.btn-action.done { background: #9B8FD4; color: white; }
.btn-action.done:hover { background: #7C6BC4; }

/* Admin Modals */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.admin-modal {
  background: white;
  width: 100%;
  max-width: 400px;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header {
  padding: 20px;
  border-bottom: 1px solid #F3F4F6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 { margin: 0; font-size: 18px; font-weight: 800; color: #1B1B1B; }

.close-btn { background: none; border: none; font-size: 24px; color: #9CA3AF; cursor: pointer; }

.modal-body { padding: 20px; }
.modal-info { font-size: 14px; color: #6B7280; margin-bottom: 20px; line-height: 1.5; }

.pay-options { display: flex; flex-direction: column; gap: 10px; }

.pay-opt-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  border: 2px solid #F3F4F6;
  border-radius: 12px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 700;
  color: #374151;
  text-align: left;
}

.pay-opt-btn:hover {
  border-color: #7C6BC4;
  background: #F0EEFF;
  color: #7C6BC4;
}

.modal-footer { padding: 16px 20px; border-top: 1px solid #F3F4F6; display: flex; justify-content: flex-end; }
.btn-cancel { background: #F9FAFB; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; color: #6B7280; cursor: pointer; }

.modal-enter-active, .modal-leave-active { transition: opacity 0.3s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
