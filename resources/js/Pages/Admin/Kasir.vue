<template>
  <AdminLayout title="Kasir POS">
    <div class="max-w-[1600px] mx-auto">
      
      <!-- ── STEP 1: INPUT ORDER ───────────────────────────── -->
      <div v-if="step === 1" class="flex flex-col lg:flex-row gap-6">
        
        <!-- Left: Menu Grid -->
        <div class="flex-1">
          <div class="app-card-lg p-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <h2 class="text-2xl font-black text-gray-900">Kasir Picpic</h2>
              <p class="text-sm text-gray-500 mt-1">Pilih menu untuk pesanan baru</p>
            </div>
            <div class="relative w-full md:w-64">
              <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Cari menu..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#6367FF] focus:ring-1 focus:ring-[#6367FF] shadow-sm bg-white font-medium"
              >
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-3">
            <div 
              v-for="item in filteredMenuItems" 
              :key="item.id"
              class="app-card flex p-3 rounded-2xl gap-3 cursor-pointer hover:shadow-md transition-all active:scale-[0.98] items-center"
              @click="addToCart(item)"
            >
              <img :src="item.image" :alt="item.name" class="w-16 h-16 rounded-xl object-cover flex-shrink-0" />
              <div class="flex-1 min-w-0 py-1">
                <h4 class="font-bold text-gray-800 text-sm leading-tight mb-1 truncate">{{ item.name }}</h4>
                <p class="text-[#6367FF] font-black text-sm">{{ formatPrice(item.price) }}</p>
              </div>
              <button class="w-8 h-8 flex-shrink-0 rounded-xl bg-primary/10 text-primary flex items-center justify-center transition-colors hover:bg-primary/20 pointer-events-none">
                <PlusIcon class="w-4 h-4 stroke-2" />
              </button>
            </div>
          </div>
        </div>

        <!-- Right: Cart Sidebar -->
        <div class="w-full lg:w-[400px] shrink-0">
          <div class="app-card-lg p-6 sticky top-6 shadow-xl border-[#6367FF]/10">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
              <ShoppingCartIcon class="w-6 h-6 text-[#6367FF]" />
              <h3 class="text-lg font-black text-gray-900">Keranjang</h3>
              <span class="ml-auto bg-[#6367FF] text-white text-xs font-black px-2.5 py-1 rounded-full">{{ cart.length }} Item</span>
            </div>

            <!-- Empty Cart -->
            <div v-if="cart.length === 0" class="py-12 text-center">
              <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <InboxIcon class="w-10 h-10 text-gray-200" />
              </div>
              <p class="text-gray-400 font-medium text-sm">Belum ada item dipilih</p>
            </div>

            <!-- Cart Items -->
            <div v-else class="space-y-4 mb-8 max-h-[400px] overflow-y-auto pr-2 custom-scroll">
              <div v-for="(item, index) in cart" :key="index" class="bg-gray-50 rounded-2xl p-3 border border-gray-100">
                <div class="flex justify-between items-start mb-2">
                  <div class="flex-1">
                    <h5 class="font-bold text-gray-800 text-sm leading-tight">{{ item.name }}</h5>
                    <p class="text-xs text-[#6367FF] font-bold mt-0.5">{{ formatPrice(item.price) }}</p>
                  </div>
                  <button @click="removeFromCart(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>

                <div class="flex items-center gap-3">
                  <!-- Qty -->
                  <div class="flex items-center bg-white rounded-lg border border-gray-200 p-1">
                    <button @click="item.quantity > 1 ? item.quantity-- : removeFromCart(index)" class="p-1 hover:bg-gray-50 rounded">
                      <MinusIcon class="w-3 h-3 text-gray-600" />
                    </button>
                    <span class="w-8 text-center text-xs font-black text-gray-800">{{ item.quantity }}</span>
                    <button @click="item.quantity++" class="p-1 hover:bg-gray-50 rounded">
                      <PlusIcon class="w-3 h-3 text-gray-600" />
                    </button>
                  </div>
                  <!-- Note -->
                  <div class="relative flex-1">
                    <PencilSquareIcon class="w-3 h-3 text-gray-400 absolute left-2 top-1/2 -translate-y-1/2" />
                    <input 
                      v-model="item.note" 
                      type="text" 
                      placeholder="Catatan..." 
                      class="w-full bg-white border-gray-200 rounded-lg py-1.5 pl-7 pr-2 text-[10px] focus:ring-[#6367FF]"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Info -->
            <div class="space-y-3 mb-6">
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-gray-500 uppercase tracking-wider">Nama Pelanggan (Opsional)</label>
                <input v-model="form.customer_name" type="text" placeholder="Contoh: Budi" class="app-input-admin text-sm px-4 py-3 rounded-xl" />
              </div>
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-black text-gray-500 uppercase tracking-wider">Nomor Meja (Opsional)</label>
                <input v-model="form.table_number" type="text" placeholder="Contoh: 05" class="app-input-admin text-sm px-4 py-3 rounded-xl" />
              </div>
            </div>

            <!-- Total -->
            <div class="border-t-2 border-dashed border-gray-100 pt-5 mb-6">
              <div class="flex justify-between items-center">
                <span class="text-gray-500 font-bold">Total Harga</span>
                <span class="text-2xl font-black text-[#6367FF]">{{ formatPrice(totalPrice) }}</span>
              </div>
            </div>

            <button 
              @click="goToPayment" 
              :disabled="cart.length === 0"
              class="app-btn-primary w-full py-4 text-base shadow-xl shadow-[#6367FF]/30 disabled:opacity-50 disabled:shadow-none"
            >
              Lanjut ke Pembayaran
            </button>
          </div>
        </div>

      </div>

      <!-- ── STEP 2: PEMBAYARAN ───────────────────────────── -->
      <div v-else-if="step === 2" class="max-w-xl mx-auto py-8">
        <div class="app-card-lg p-0 overflow-hidden">
          
          <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-xl font-black text-gray-900">Pilih Pembayaran</h3>
            <button @click="step = 1" class="text-gray-400 hover:text-gray-600 transition-colors">
              <span class="text-sm font-bold">&larr; Kembali</span>
            </button>
          </div>

          <div class="p-8">
            <!-- Order Summary Short -->
            <div class="bg-[#F0EEFF] p-5 rounded-2xl mb-8 border border-[#6367FF]/10 text-center">
              <p class="text-sm font-bold text-[#6367FF] uppercase tracking-widest mb-1">Total Bayar</p>
              <h2 class="text-4xl font-black text-gray-900">{{ formatPrice(totalPrice) }}</h2>
              <div v-if="form.customer_name" class="mt-2 text-xs font-bold text-gray-500">Pelanggan: {{ form.customer_name }}</div>
            </div>

            <!-- Tabs -->
            <div class="flex p-1.5 bg-gray-100 rounded-2xl mb-8">
              <button 
                @click="paymentMethod = 'cash'"
                class="flex-1 py-3 px-4 rounded-xl text-sm font-black transition-all"
                :class="paymentMethod === 'cash' ? 'bg-white text-[#6367FF] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
              >
                <BanknotesIcon class="w-5 h-5 mx-auto mb-1" />
                TUNAI
              </button>
              <button 
                @click="paymentMethod = 'qris'"
                class="flex-1 py-3 px-4 rounded-xl text-sm font-black transition-all"
                :class="paymentMethod === 'qris' ? 'bg-white text-[#6367FF] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
              >
                <QrCodeIcon class="w-5 h-5 mx-auto mb-1" />
                QRIS
              </button>
            </div>

            <!-- TUNAI SECTION -->
            <div v-if="paymentMethod === 'cash'" class="space-y-6">
              <div>
                <label class="block text-sm font-black text-gray-700 mb-2">Uang Diterima (Rp)</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-black">Rp</span>
                  <input 
                    v-model="cashReceived" 
                    type="number" 
                    class="w-full pl-12 pr-4 py-4 bg-white border-2 border-gray-100 rounded-2xl text-xl font-black focus:border-[#6367FF] outline-none"
                    placeholder="Masukkan nominal"
                  />
                </div>
                <!-- Suggested Amounts -->
                <div class="flex flex-wrap gap-2 mt-3">
                  <button 
                    v-for="amount in suggestedAmounts" 
                    :key="amount" 
                    @click="cashReceived = amount"
                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-bold transition-all"
                  >
                    {{ formatPrice(amount) }}
                  </button>
                </div>
              </div>

              <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                <div class="flex justify-between items-center">
                  <span class="text-sm font-bold text-gray-500">Kembalian</span>
                  <span class="text-2xl font-black" :class="cashChange >= 0 ? 'text-green-600' : 'text-red-500'">
                    {{ cashChange >= 0 ? formatPrice(cashChange) : 'Dana Kurang' }}
                  </span>
                </div>
              </div>

              <button 
                @click="submitOrder"
                :disabled="cashChange < 0 || processing"
                class="app-btn-primary w-full py-5 text-lg shadow-xl shadow-[#6367FF]/30 disabled:opacity-50"
              >
                {{ processing ? 'Memproses...' : 'Konfirmasi Lunas' }}
              </button>
            </div>

            <!-- QRIS SECTION -->
            <div v-else class="text-center py-4">
              <div class="bg-gray-50 p-8 rounded-3xl mb-8 border-2 border-dashed border-gray-200 flex flex-col items-center">
                <QrCodeIcon class="w-24 h-24 text-gray-300 mb-4" />
                <p class="text-gray-900 font-black text-lg">Silakan scan QRIS</p>
                <p class="text-gray-500 text-sm mt-1">Pastikan pembayaran berhasil di perangkat pelanggan</p>
              </div>

              <button 
                @click="submitOrder"
                :disabled="processing"
                class="app-btn-primary w-full py-5 text-lg shadow-xl shadow-[#6367FF]/30"
              >
                {{ processing ? 'Memproses...' : 'Konfirmasi Sudah Bayar' }}
              </button>
            </div>

          </div>
        </div>
      </div>

      <!-- ── STEP 3: SUCCESS ──────────────────────────────── -->
      <div v-else-if="step === 3" class="max-w-md mx-auto py-12 text-center">
          <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
            <CheckCircleIcon class="w-12 h-12 text-green-600" />
          </div>
          
          <h2 class="text-3xl font-black text-gray-900 mb-2">Order Berhasil!</h2>
          <p class="text-gray-500 mb-8">Order <span class="font-bold text-[#6367FF]">#{{ lastOrderId }}</span> telah tersimpan sebagai LUNAS.</p>

          <div class="app-card-lg p-6 mb-10 bg-gray-50 border-gray-100">
             <div class="flex justify-between items-center mb-4">
               <span class="text-gray-500 text-sm font-bold">Pelanggan</span>
               <span class="text-gray-900 font-bold">{{ form.customer_name || 'Pelanggan Umum' }}</span>
             </div>
             <div class="flex justify-between items-center mb-4">
               <span class="text-gray-500 text-sm font-bold">Total Bayar</span>
               <span class="text-gray-900 font-black text-xl">{{ formatPrice(totalPrice) }}</span>
             </div>
             <div class="h-px bg-gray-200 my-4"></div>
             <div class="flex justify-between items-center">
               <span class="text-gray-500 text-sm font-bold">Metode</span>
               <span class="text-sm font-black text-[#6367FF] uppercase">{{ paymentMethod }}</span>
             </div>
          </div>

          <div class="flex flex-col gap-3">
            <a 
              :href="route('order.receipt', lastOrderId)" 
              target="_blank"
              class="app-btn-primary w-full py-5 flex items-center justify-center gap-2 text-lg shadow-xl shadow-[#6367FF]/30"
            >
              <PrinterIcon class="w-6 h-6" /> Cetak Struk
            </a>
            <button 
              @click="resetForm" 
              class="w-full py-5 bg-white border-2 border-gray-100 text-gray-600 rounded-2xl font-black hover:bg-gray-50 transition-all"
            >
               Order Baru
            </button>
          </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  MagnifyingGlassIcon, PlusIcon, MinusIcon, TrashIcon, 
  ShoppingCartIcon, InboxIcon, PencilSquareIcon,
  BanknotesIcon, QrCodeIcon, CheckCircleIcon, PrinterIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  menuItems: Array
});

// Layout State
const step = ref(1);
const processing = ref(false);
const searchQuery = ref('');

// Cart State
const cart = ref([]);
const form = ref({
  customer_name: '',
  table_number: '',
  customer_phone: ''
});

// Payment State
const paymentMethod = ref('cash');
const cashReceived = ref('');
const lastOrderId = ref(null);

// Computed
const filteredMenuItems = computed(() => {
  if (!searchQuery.value) return props.menuItems;
  const q = searchQuery.value.toLowerCase();
  return props.menuItems.filter(i => i.name.toLowerCase().includes(q));
});

const totalPrice = computed(() => {
  return cart.value.reduce((acc, item) => acc + (item.price * item.quantity), 0);
});

const cashChange = computed(() => {
  if (!cashReceived.value) return -1;
  return cashReceived.value - totalPrice.value;
});

const suggestedAmounts = computed(() => {
  const total = totalPrice.value;
  if (total <= 0) return [];
  
  // Bulatkan ke kelipatan terdekat (5rb, 10rb, 50rb, 100rb)
  const suggestions = new Set([total]);
  [5000, 10000, 20000, 50000, 100000].forEach(denom => {
    if (total < denom) suggestions.add(denom);
    if (total > denom) {
        let next = Math.ceil(total / denom) * denom;
        suggestions.add(next);
    }
  });
  
  return Array.from(suggestions).sort((a,b) => a-b).filter(a => a >= total).slice(0, 5);
});

// Methods
const addToCart = (item) => {
  const existing = cart.value.find(i => i.id === item.id);
  if (existing) {
    existing.quantity++;
  } else {
    cart.value.push({
      ...item,
      menu_item_id: item.id,
      quantity: 1,
      note: ''
    });
  }
};

const removeFromCart = (index) => {
  cart.value.splice(index, 1);
};

const goToPayment = () => {
  if (cart.value.length === 0) return;
  step.value = 2;
  cashReceived.value = '';
};

const submitOrder = () => {
  if (processing.value) return;
  processing.value = true;

  const data = {
    ...form.value,
    items: cart.value.map(i => ({
      menu_item_id: i.menu_item_id,
      quantity: i.quantity,
      note: i.note
    })),
    payment_method: paymentMethod.value,
    payment_status: 'paid',
    status: 'done' // Auto Selesai sesuai permintaan
  };

  // Gunakan existing CartController@store API
  router.post(route('cart.checkout'), data, {
    onSuccess: (page) => {
      // Kita perlu ID order terakhir. Karena store me-redirect ke order.status, 
      // kita tangkap ID dari URL redirect tersebut atau via flash.
      // Cara termudah: capture dari path params status page yang baru dibuka.
      const url = page.url; // /order/{id}/status
      const match = url.match(/\/order\/(\d+)\/status/);
      if (match) lastOrderId.value = match[1];
      
      step.value = 3;
      processing.value = false;
    },
    onError: (err) => {
      console.error(err);
      alert('Terjadi kesalahan saat menyimpan pesanan');
      processing.value = false;
    }
  });
};

const resetForm = () => {
  cart.value = [];
  form.value = { customer_name: '', table_number: '', customer_phone: '' };
  step.value = 1;
  cashReceived.value = '';
  lastOrderId.value = null;
};

const formatPrice = (price) => {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
};
</script>

<style scoped>
.app-card {
  background: white;
  border-radius: 20px;
  border: 1px solid rgba(0,0,0,0.04);
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.custom-scroll::-webkit-scrollbar {
  width: 4px;
}
.custom-scroll::-webkit-scrollbar-thumb {
  background: #E5E7EB;
  border-radius: 4px;
}

.app-input-admin {
  background: #f9fafb;
  border: 1.5px solid transparent;
  transition: all 0.2s;
}
.app-input-admin:focus {
  border-color: #6367FF;
  background: white;
  box-shadow: 0 0 0 4px rgba(99, 103, 255, 0.1);
}
</style>
