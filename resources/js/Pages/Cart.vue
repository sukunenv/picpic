<template>
  <div class="cart-page">

    <!-- ── BACK HEADER ───────────────────────────────────── -->
    <div class="cart-header">
      <button class="back-btn" @click="goBack">
        <ChevronLeftIcon class="h-6 w-6" />
      </button>
      <h1 class="cart-title">Keranjang</h1>
      <button
        v-if="cartStore.items.length > 0"
        class="clear-all-btn"
        @click="confirmClear"
      >
        <TrashIcon class="h-4 w-4 inline mr-1" /> Hapus semua
      </button>
      <div v-else style="width:80px;"></div>
    </div>

    <!-- ── EMPTY CART ─────────────────────────────────────── -->
    <div v-if="cartStore.items.length === 0" class="empty-cart">
      <div class="empty-illustration">
        <ShoppingCartIcon class="h-20 w-20 text-gray-300 mx-auto" />
      </div>
      <h2 class="empty-title">Keranjang Kosong</h2>
      <p class="empty-desc">Belum ada item yang ditambahkan.<br>Yuk pilih menu dulu!</p>
      <button class="browse-btn" @click="goToMenu">Lihat Menu</button>
    </div>

    <!-- ── CART CONTENT ───────────────────────────────────── -->
    <div v-else class="cart-content">

      <!-- Cart Items -->
      <div class="px-container">
        <div
          v-for="item in cartStore.items"
          :key="item.id"
          class="cart-item"
        >
          <div class="item-img-wrap">
            <img :src="item.image" :alt="item.name" class="item-img" />
          </div>
          <div class="item-info">
            <p class="item-name">{{ item.name }}</p>
            <p class="item-price">{{ formatPrice(item.price) }}</p>
            <!-- Note input -->
            <div class="note-wrap">
              <PencilSquareIcon class="h-3 w-3 text-gray-400 absolute left-2 top-2.5" />
              <input
                v-model="item.note"
                type="text"
                placeholder="Tambah catatan…"
                class="note-input pl-6"
                @change="cartStore.updateNote(item.id, item.note)"
              />
            </div>
          </div>
          <div class="item-actions">
            <button class="delete-btn" @click="cartStore.removeItem(item.id)">
              <TrashIcon class="h-4 w-4" />
            </button>
            <div class="qty-control">
              <button class="qty-btn minus" @click="cartStore.decreaseQty(item.id)">
                <MinusIcon class="h-4 w-4" />
              </button>
              <span class="qty-num">{{ item.quantity }}</span>
              <button class="qty-btn plus" @click="cartStore.increaseQty(item.id)">
                <PlusIcon class="h-4 w-4" />
              </button>
            </div>
            <p class="item-subtotal">{{ formatPrice(item.price * item.quantity) }}</p>
          </div>
        </div>
      </div>

      <!-- ── INFORMASI ORDER ─────────────────────────────── -->
      <div class="px-container">
        <div class="info-section">
          <h3 class="info-title">
            <IdentificationIcon class="h-5 w-5 inline mr-1.5 text-[#7C6BC4]" /> 
            Informasi Pemesanan
          </h3>
          <div class="form-group">
            <label class="form-label">Nama Pemesan *</label>
            <input
              v-model="form.customerName"
              type="text"
              placeholder="Masukkan nama kamu"
              class="form-input"
              :class="{ error: errors.customerName }"
            />
            <p v-if="errors.customerName" class="error-msg">{{ errors.customerName }}</p>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Meja</label>
            <input
              v-model="form.tableNumber"
              type="text"
              placeholder="Contoh: Meja 5 / Takeaway"
              class="form-input"
            />
          </div>
        </div>
      </div>

      <!-- ── RINGKASAN ───────────────────────────────────── -->
      <div class="px-container">
        <div class="summary-section">
          <div class="summary-row">
            <span>Subtotal ({{ cartStore.totalItems }} item)</span>
            <span>{{ formatPrice(cartStore.totalPrice) }}</span>
          </div>
          <div class="summary-row">
            <span>Biaya layanan</span>
            <span class="free-badge">Gratis</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row total">
            <span>Total</span>
            <span class="total-price">{{ formatPrice(cartStore.totalPrice) }}</span>
          </div>
        </div>
      </div>

      <!-- ── ACTION BUTTONS ──────────────────────────────── -->
      <div class="px-container action-section">
        <button
          class="btn-pay-now"
          :disabled="submitting"
          @click="showQrisModal = true"
        >
          <QrCodeIcon class="h-5 w-5 inline mr-1" /> Bayar Sekarang via QRIS
        </button>
        
        <div class="pay-later-wrap">
          <button
            class="btn-pay-later"
            :disabled="submitting"
            @click="submitOrder('cash', 'unpaid')"
          >
            <BanknotesIcon class="h-5 w-5 inline mr-1" /> Bayar di Kasir
          </button>
          <p class="pay-later-note">Bayar cash/QRIS saat mau pulang</p>
        </div>
      </div>

      <!-- ── QRIS MODAL ────────────────────────────────────── -->
      <transition name="modal">
        <div v-if="showQrisModal" class="modal-overlay" @click.self="showQrisModal = false">
          <div class="qris-modal">
            <div class="qris-header">
              <h3>Pembayaran QRIS</h3>
              <button class="close-modal" @click="showQrisModal = false">×</button>
            </div>
            
            <div class="qris-content">
              <div class="qris-img-container">
                <img src="/qris_dummy.png" alt="QRIS Dummy" class="qris-img" />
              </div>
              <div class="qris-nominal">
                <p class="nom-label">Total Bayar</p>
                <p class="nom-val">{{ formatPrice(cartStore.totalPrice) }}</p>
              </div>
              <p class="qris-instr">
                Scan QR ini dengan aplikasi bank atau e-wallet kamu
              </p>
            </div>
            
            <div class="qris-footer">
              <button 
                class="btn-confirm-pay" 
                :disabled="submitting"
                @click="submitOrder('qris', 'paid')"
              >
                {{ submitting ? '⏳ Memproses…' : 'Konfirmasi Sudah Bayar' }}
              </button>
              <button class="btn-cancel-pay" @click="showQrisModal = false">Batal</button>
            </div>
          </div>
        </div>
      </transition>

      <div style="height: 20px;"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/cartStore';
import { 
  ChevronLeftIcon, 
  TrashIcon, 
  ShoppingCartIcon,
  MinusIcon,
  PlusIcon,
  PencilSquareIcon,
  IdentificationIcon,
  CreditCardIcon,
  QrCodeIcon,
  BanknotesIcon,
  DocumentTextIcon,
  HandRaisedIcon,
  Squares2X2Icon,
  BeakerIcon,
  CakeIcon,
  VariableIcon as MugIcon,
  WrenchScrewdriverIcon as UtensilsIcon
} from '@heroicons/vue/24/outline';

const cartStore = useCartStore();
const submitting = ref(false);
const showQrisModal = ref(false);

const form = reactive({
  customerName: '',
  tableNumber: '',
});

const errors = reactive({ customerName: '' });


function validate() {
  errors.customerName = '';
  if (!form.customerName.trim()) {
    errors.customerName = 'Nama pemesan wajib diisi';
    return false;
  }
  return true;
}

async function submitOrder(method, status) {
  if (!validate()) return;
  
  const payload = {
    customer_name: form.customerName,
    table_number: form.tableNumber || null,
    payment_method: method,
    payment_status: status,
    items: cartStore.items.map(i => ({
      menu_item_id: i.id,
      quantity: i.quantity,
      note: i.note || null,
    })),
  };

  router.post('/cart/checkout', payload, {
    onStart: () => { submitting.value = true; },
    onFinish: () => { 
      submitting.value = false;
      showQrisModal.value = false;
    },
    onSuccess: () => {
      cartStore.clearCart();
    },
    onError: (err) => {
      alert('Gagal memproses pesanan: ' + (err.message || 'Coba lagi.'));
    }
  });
}

function confirmClear() {
  if (confirm('Hapus semua item di keranjang?')) {
    cartStore.clearCart();
  }
}

function goBack() { router.visit('/'); }
function goToMenu() { router.visit('/menu'); }

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}
</script>

<style scoped>
.cart-page {
  background: #F0EEFF;
  min-height: 100dvh;
}

/* Header */
.cart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 52px 16px 16px;
  background: #ffffff;
  border-bottom: 1px solid #F0F0F0;
}

.back-btn {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  border: 1.5px solid #E5E7EB;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #1B1B1B;
  transition: all 0.2s;
}

.back-btn:hover { background: #F3F4F6; }

.cart-title {
  font-size: 18px;
  font-weight: 800;
  color: #1B1B1B;
  margin: 0;
}

.clear-all-btn {
  font-size: 13px;
  color: #EF4444;
  background: none;
  border: none;
  cursor: pointer;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 8px;
}

/* Empty */
.empty-cart {
  text-align: center;
  padding: 80px 32px;
}

.empty-icon { font-size: 72px; display: block; margin-bottom: 16px; }
.empty-title { font-size: 20px; font-weight: 700; color: #1B1B1B; margin: 0 0 8px; }
.empty-desc { font-size: 14px; color: #6B7280; margin: 0 0 24px; line-height: 1.6; }

.browse-btn {
  background: #7C6BC4;
  color: white;
  border: none;
  padding: 13px 32px;
  border-radius: 14px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.browse-btn:hover { background: #5A4DA0; }

/* Content */
.cart-content { padding-top: 16px; }
.px-container { padding: 0 16px; }

/* Cart Item */
.cart-item {
  display: flex;
  gap: 12px;
  background: white;
  border-radius: 16px;
  padding: 14px;
  margin-bottom: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.item-img-wrap {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  overflow: hidden;
  flex-shrink: 0;
}

.item-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-info { flex: 1; min-width: 0; }

.item-name {
  font-size: 14px;
  font-weight: 600;
  color: #1B1B1B;
  margin: 0 0 4px;
}

.item-price {
  font-size: 13px;
  color: #6B7280;
  margin: 0 0 8px;
}

.note-input {
  width: 100%;
  border: 1.5px solid #E5E7EB;
  border-radius: 10px;
  padding: 7px 10px;
  font-size: 12px;
  color: #1B1B1B;
  outline: none;
  background: #F9FAFB;
  transition: border-color 0.2s;
}

.note-input:focus { border-color: #7C6BC4; background: white; }
.note-input::placeholder { color: #9CA3AF; }

.item-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
  flex-shrink: 0;
}

.delete-btn {
  background: #FEF2F2;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 10px;
  color: #EF4444;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.delete-btn:hover { background: #FEE2E2; }

.qty-control {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #F3F4F6;
  border-radius: 12px;
  padding: 4px 8px;
}

.qty-btn {
  width: 28px;
  height: 28px;
  border: none;
  border-radius: 8px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  min-height: 28px;
}

.qty-btn.minus { background: #E5E7EB; color: #374151; }
.qty-btn.minus:hover { background: #D1D5DB; }
.qty-btn.plus { background: #7C6BC4; color: white; }
.qty-btn.plus:hover { background: #5A4DA0; }
.qty-btn:active { transform: scale(0.9); }

.qty-num {
  font-size: 15px;
  font-weight: 700;
  color: #1B1B1B;
  min-width: 20px;
  text-align: center;
}

.item-subtotal {
  font-size: 13px;
  font-weight: 700;
  color: #7C6BC4;
  margin: 0;
}

/* Sections */
.info-section {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin-top: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.info-title {
  font-size: 15px;
  font-weight: 700;
  color: #1B1B1B;
  margin: 0 0 14px;
}

.form-group { margin-bottom: 12px; }

.form-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}

.form-input {
  width: 100%;
  border: 1.5px solid #E5E7EB;
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 14px;
  color: #1B1B1B;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
  background: #F9FAFB;
}

.form-input:focus { border-color: #7C6BC4; background: white; }
.form-input.error { border-color: #EF4444; }
.error-msg { font-size: 12px; color: #EF4444; margin: 4px 0 0; }

/* Payment */
.payment-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.payment-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 14px;
  border: 2px solid #E5E7EB;
  border-radius: 14px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 13px;
  font-weight: 500;
  color: #374151;
  min-height: 48px;
}

.payment-btn:hover { border-color: #9B8FD4; }

.payment-btn.active {
  border-color: #7C6BC4;
  background: #F0EEFF;
  color: #7C6BC4;
  font-weight: 700;
}

.pay-icon { font-size: 18px; }

/* Summary */
.summary-section {
  background: white;
  border-radius: 16px;
  padding: 16px;
  margin-top: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  color: #374151;
  padding: 6px 0;
}

.free-badge {
  background: #EDE9FE;
  color: #5B21B6;
  font-size: 12px;
  font-weight: 600;
  padding: 2px 10px;
  border-radius: 20px;
}

.summary-divider {
  height: 1px;
  background: #F0F0F0;
  margin: 8px 0;
}

.summary-row.total {
  font-size: 16px;
  font-weight: 700;
  color: #1B1B1B;
}

.total-price { color: #7C6BC4; font-size: 18px; }

/* Buttons */
.action-section { margin-top: 16px; display: flex; flex-direction: column; gap: 10px; }

.btn-pay-now {
  width: 100%;
  padding: 16px;
  border-radius: 16px;
  background: #7C6BC4;
  border: none;
  color: white;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  min-height: 54px;
  transition: all 0.2s;
  box-shadow: 0 4px 16px rgba(124, 107, 196, 0.35);
}

.btn-pay-now:hover { background: #5A4DA0; transform: translateY(-1px); }
.btn-pay-now:active { transform: translateY(0); }
.btn-pay-now:disabled { opacity: 0.7; cursor: not-allowed; }

.pay-later-wrap {
  text-align: center;
}

.btn-pay-later {
  width: 100%;
  padding: 16px;
  border-radius: 16px;
  background: transparent;
  border: 2px solid #7C6BC4;
  color: #7C6BC4;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  min-height: 54px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-pay-later:hover { background: #F0EEFF; }
.btn-pay-later:disabled { opacity: 0.7; cursor: not-allowed; }

.pay-later-note {
  font-size: 11px;
  color: #6B7280;
  margin-top: 6px;
  font-weight: 500;
}

/* Modal Styles */
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

.qris-modal {
  background: white;
  width: 100%;
  max-width: 360px;
  border-radius: 24px;
  overflow: hidden;
  animation: modalScale 0.3s cubic-bezier(0.18, 0.89, 0.32, 1.28);
}

@keyframes modalScale {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

.qris-header {
  padding: 16px 20px;
  border-bottom: 1px solid #F3F4F6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.qris-header h3 { margin: 0; font-size: 16px; font-weight: 800; color: #1B1B1B; }

.close-modal {
  background: none;
  border: none;
  font-size: 24px;
  color: #9CA3AF;
  cursor: pointer;
}

.qris-content {
  padding: 24px 20px;
  text-align: center;
}

.qris-img-container {
  width: 180px;
  height: 180px;
  margin: 0 auto 16px;
  background: #F9FAFB;
  padding: 10px;
  border-radius: 16px;
  border: 4px solid #F3F4F6;
}

.qris-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.qris-nominal { margin-bottom: 16px; }
.nom-label { font-size: 12px; color: #6B7280; margin: 0; }
.nom-val { font-size: 24px; font-weight: 800; color: #7C6BC4; margin: 0; }

.qris-instr { font-size: 13px; color: #374151; margin: 0; line-height: 1.5; font-weight: 500; }

.qris-footer {
  padding: 16px 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-confirm-pay {
  background: #10B981;
  color: white;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
}

.btn-confirm-pay:disabled { opacity: 0.7; cursor: not-allowed; }

.btn-cancel-pay {
  background: #F3F4F6;
  color: #6B7280;
  border: none;
  padding: 12px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
}

/* Transitions */
.modal-enter-active, .modal-leave-active { transition: opacity 0.3s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>

