<template>
  <AppLayout>
    <div class="cart-page min-h-screen bg-gray-50 pb-32">

      <!-- ── BACK HEADER ───────────────────────────────────── -->
    <div class="sticky top-0 z-40 bg-white/85 backdrop-blur-md px-5 sm:px-6 pt-12 pb-4 border-b border-gray-100 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100 transition-colors hover:bg-gray-100" @click="goBack">
          <ChevronLeftIcon class="h-6 w-6 text-primary" />
        </button>
        <h1 class="app-page-title text-primary">Keranjang</h1>
      </div>
      <button
        v-if="cartStore.items.length > 0"
        class="text-accent text-xs font-bold uppercase tracking-wide bg-accent/10 px-3 py-1.5 rounded-full"
        @click="confirmClear"
      >
        Hapus Semua
      </button>
    </div>

    <!-- ── EMPTY CART ─────────────────────────────────────── -->
    <div v-if="cartStore.items.length === 0" class="flex flex-col items-center justify-center px-8 py-32 text-center">
      <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-xl shadow-black/5 mb-6">
        <ShoppingCartIcon class="h-16 w-16 text-gray-200" />
      </div>
      <h2 class="app-page-title text-primary mb-2">Keranjang Kosong</h2>
      <p class="text-gray-400 text-sm mb-8 leading-relaxed">Sepertinya kamu belum memilih minuman.<br>Yuk, eksplor menu kami!</p>
      <button class="bg-primary text-white font-bold px-8 py-4 rounded-xl shadow-md shadow-primary/20 w-full max-w-xs transition-transform active:scale-95" @click="goToMenu">
        Lihat Menu Sekarang
      </button>
    </div>

    <!-- ── CART CONTENT ───────────────────────────────────── -->
    <div v-else class="px-5 sm:px-6 mt-5">

      <!-- Cart Items -->
      <div class="space-y-3.5 mb-7">
        <div
          v-for="item in cartStore.items"
          :key="item.id"
          class="app-card p-4 rounded-2xl md:rounded-3xl flex gap-4 pr-3"
        >
          <img :src="item.image" :alt="item.name" class="w-20 h-20 rounded-[18px] object-cover flex-shrink-0" />
          <div class="flex-1 flex flex-col justify-between py-0.5">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-primary font-bold text-sm leading-tight pr-4">{{ item.name }}</h3>
                <p class="text-primary font-black text-sm mt-1">{{ formatPrice(item.price) }}</p>
              </div>
              <button class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-500 hover:bg-red-100 transition-colors" @click="cartStore.removeItem(item.id)">
                <TrashIcon class="h-4 w-4" />
              </button>
            </div>
            
            <div class="flex items-center justify-between mt-3">
              <!-- Note input -->
              <div class="relative w-3/5">
                <PencilSquareIcon class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3 w-3 text-gray-400" />
                <input
                  v-model="item.note"
                  type="text"
                  placeholder="Catatan..."
                  class="w-full bg-gray-50 border-none rounded-xl py-1.5 pl-7 pr-3 text-[11px] text-primary placeholder:text-gray-400 focus:ring-1 focus:ring-accent"
                  @change="cartStore.updateNote(item.id, item.note)"
                />
              </div>
              
              <!-- Qty Control -->
              <div class="flex items-center bg-gray-50 rounded-xl p-1 gap-1 border border-gray-100/50">
                <button class="w-7 h-7 bg-white rounded-lg flex items-center justify-center shadow-sm text-gray-600 active:scale-90 transition-transform" @click="cartStore.decreaseQty(item.id)">
                  <MinusIcon class="h-3 w-3" />
                </button>
                <span class="w-6 text-center text-xs font-bold text-primary">{{ item.quantity }}</span>
                <button class="w-7 h-7 bg-primary rounded-lg flex items-center justify-center shadow-sm text-white active:scale-90 transition-transform" @click="cartStore.increaseQty(item.id)">
                  <PlusIcon class="h-3 w-3" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── INFORMASI ORDER ─────────────────────────────── -->
      <div class="app-card-lg p-6 mb-4">
        <h3 class="app-section-title text-primary mb-4 flex items-center gap-2">
          <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center">
             <IdentificationIcon class="h-4 w-4 text-accent" /> 
          </div>
          Detail Pemesanan
        </h3>
        
        <div class="space-y-5">
          <!-- Nama Pelanggan -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">Atas Nama *</label>
            <input 
              type="text" 
              v-model="customer_name" 
              class="w-full px-4 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-accent/20 transition-all font-medium" 
              placeholder="Masukkan nama Anda"
              :class="{ 'ring-2 ring-red-400': errors.customerName }"
              required
            />
            <p v-if="errors.customerName" class="text-red-500 text-[11px] mt-1 ml-2 font-medium">{{ errors.customerName }}</p>
          </div>

          <!-- Nomor Meja -->
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">Nomor Meja *</label>
            <input 
              type="text" 
              v-model="table_number" 
              class="w-full px-4 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-accent/20 transition-all font-medium" 
              placeholder="Contoh: 05"
              required
            />
          </div>

          <!-- Kontak WhatsApp -->
          <div class="pt-2">
            <label class="block text-sm font-medium text-gray-600 mb-2">Nomor WhatsApp (Opsional)</label>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="text-gray-400 font-bold text-sm">+62</span>
              </div>
              <input 
                type="tel" 
                v-model="customer_phone" 
                class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-accent/20 transition-all font-medium" 
                placeholder="8xxxxxxxxxx"
              />
            </div>
            <p class="text-xs text-gray-400 mt-2 ml-1">Untuk menerima struk digital dan status pesanan via WA</p>
          </div>
        </div>
      </div>

      <!-- ── CHECKOUT PANEL ───────────────────────────────── -->
      <div class="sticky bottom-3 sm:bottom-4 z-20">
        <div class="app-card-lg app-glass p-4 sm:p-5">
          <div class="flex items-center gap-2 mb-3 text-[10px] font-bold uppercase tracking-wider text-primary/60">
            <span class="w-5 h-5 rounded-full bg-primary/15 text-primary flex items-center justify-center">1</span>
            Keranjang
            <span class="w-5 h-[1px] bg-primary/20"></span>
            <span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center">2</span>
            Pembayaran
          </div>
          <h3 class="app-section-title text-primary mb-3">Ringkasan Biaya</h3>
          <div class="space-y-2.5 text-sm mb-4">
            <div class="flex justify-between text-gray-500">
              <span>Subtotal ({{ cartStore.totalItems }} item)</span>
              <span class="font-medium text-primary">{{ formatPrice(cartStore.totalPrice) }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
              <span>Biaya Layanan</span>
              <span class="bg-primary/10 text-primary text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Gratis</span>
            </div>
            <div class="border-t border-dashed border-gray-200 mt-2 mb-1"></div>
            <div class="flex justify-between items-center">
              <span class="text-primary font-semibold text-sm">Total Pembayaran</span>
              <span class="text-2xl font-bold text-[#6367FF]">{{ formatPrice(cartStore.totalPrice) }}</span>
            </div>
          </div>

          <div class="space-y-3">
            <button
              class="app-btn-primary w-full text-[15px] py-4 flex items-center justify-center gap-2 disabled:opacity-70"
              :disabled="submitting"
              @click="showQrisModal = true"
            >
              <QrCodeIcon class="h-5 w-5" /> Bayar Sekarang (QRIS)
            </button>
            
            <button
              class="app-btn-secondary w-full text-[15px] py-3.5 flex items-center justify-center gap-2 disabled:opacity-70"
              :disabled="submitting"
              @click="submitOrder('cash', 'unpaid')"
            >
              <BanknotesIcon class="h-5 w-5" /> Bayar Nanti di Kasir
            </button>
          </div>
        </div>
      </div>

      <!-- ── QRIS MODAL ────────────────────────────────────── -->
      <transition name="modal">
        <div v-if="showQrisModal" class="fixed inset-0 z-50 flex items-center justify-center px-3 sm:px-4 bg-black/40 backdrop-blur-sm" @click.self="showQrisModal = false">
          <div class="bg-white w-full max-w-sm rounded-2xl overflow-hidden shadow-xl modal-content">
            <div class="p-6 text-center">
              <div class="w-12 h-12 bg-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <QrCodeIcon class="h-6 w-6 text-accent" />
              </div>
              <h3 class="app-page-title text-primary mb-1">Scan QRIS untuk Bayar</h3>
              <p class="text-primary/70 text-sm mb-6">Scan dengan m-banking atau e-wallet apapun</p>
              
              <div class="bg-gray-50 p-4 rounded-3xl mb-6 border-2 border-gray-100 flex justify-center">
                <img src="/qris.png" alt="QRIS" class="w-full max-w-[280px] object-contain rounded-xl bg-white p-2" />
              </div>
              
              <div class="mb-4">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total: {{ formatPrice(cartStore.totalPrice) }}</p>
              </div>

              <p class="text-[11px] font-semibold text-gray-500 mb-6 flex justify-center flex-wrap gap-1">
                <span>GoPay</span> &bull; <span>OVO</span> &bull; <span>Dana</span> &bull; <span>ShopeePay</span> &bull; <span>Semua Bank</span>
              </p>
              
              <div class="space-y-3">
                <!-- Tombol Konfirmasi dinonaktifkan sementara sesuai permintaan -->
                <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-4 mb-2">
                  <p class="text-[11px] text-yellow-700 font-bold leading-relaxed">
                    Pesanan akan diproses otomatis setelah Anda menunjukkan bukti bayar ke kasir.
                  </p>
                </div>
                
                <button class="w-full bg-transparent border-2 border-primary text-primary hover:bg-gray-50 transition-colors font-bold py-3.5 rounded-2xl transition-all active:scale-95" @click="showQrisModal = false">
                  Kembali
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>

      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCartStore } from '@/stores/cartStore';
import { 
  ChevronLeftIcon, 
  TrashIcon, 
  ShoppingCartIcon,
  MinusIcon,
  PlusIcon,
  PencilSquareIcon,
  IdentificationIcon,
  QrCodeIcon,
  BanknotesIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline';

const cartStore = useCartStore();
const submitting = ref(false);
const showQrisModal = ref(false);

const customer_name = ref('');
const table_number = ref('');
const customer_phone = ref('');
const payment_method = ref('qris');

const errors = reactive({ customerName: '' });

function validate() {
  errors.customerName = '';
  if (!customer_name.value.trim()) {
    errors.customerName = 'Nama pemesan wajib diisi';
    return false;
  }
  return true;
}

async function submitOrder(method, status) {
  if (!validate()) {
    showQrisModal.value = false;
    // Scroll to top slowly to show the error
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }
  
  const orderData = {
    customer_name: customer_name.value,
    customer_phone: customer_phone.value,
    table_number: table_number.value || null,
    payment_method: method,
    payment_status: status,
    items: cartStore.items.map(i => ({
      menu_item_id: i.id,
      quantity: i.quantity,
      note: i.note || null,
    })),
  };

  router.post('/cart/checkout', orderData, {
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
  if (confirm('Hapus semua item pesanan?')) {
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
  background: var(--background);
  min-height: 100dvh;
}

/* Modal Transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-enter-from .modal-content {
  transform: scale(0.95) translateY(10px);
}

.modal-leave-to .modal-content {
  transform: scale(0.95) translateY(10px);
}
</style>
