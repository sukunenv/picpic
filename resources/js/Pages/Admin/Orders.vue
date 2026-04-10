<template>
  <AdminLayout>
    <div class="lg:p-6 bg-gray-50 min-h-screen">
      <!-- Toaster -->
      <transition name="toast-slide">
        <div v-if="showToast" class="fixed top-6 right-6 z-[120] bg-white shadow-xl shadow-black/10 rounded-2xl p-4 flex items-center gap-3 border border-gray-100">
          <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
            <BellAlertIcon class="h-5 w-5 text-green-600" />
          </div>
          <div>
            <p class="font-bold text-gray-900 text-sm">Pesanan Baru!</p>
            <p class="text-xs text-gray-500">Ada pesanan baru masuk 🔔</p>
          </div>
        </div>
      </transition>

      <!-- Page Header -->
      <div class="app-card-lg p-5 sm:p-6 flex flex-col md:flex-row md:items-start justify-between gap-4 mb-5 bg-gradient-to-br from-white to-[#f5f6ff]">
        <div>
          <p class="text-[11px] uppercase tracking-[0.16em] font-bold text-primary/50 mb-1">Order Monitoring</p>
          <h2 class="text-xl sm:text-2xl font-black text-gray-900 m-0">Monitoring Pesanan</h2>
          <p class="text-sm text-gray-500 mt-1">Terakhir diperbarui: {{ lastUpdate }}</p>
        </div>
        <div class="flex items-center gap-3 self-start md:self-auto">
          <!-- Refresh Status -->
          <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full border border-gray-200 shadow-sm text-xs font-semibold text-gray-600">
            <div class="relative flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
            </div>
            Auto-refresh aktif (5s)
          </div>
          
          <!-- Manual Refresh -->
          <button @click="manualRefresh" class="bg-white p-2 rounded-full border border-gray-200 shadow-sm text-gray-600 hover:bg-gray-50 transition-colors">
            <ArrowPathIcon class="h-5 w-5" :class="{ 'animate-spin': manualRefreshing }" />
          </button>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="app-card p-4 sm:p-5 mb-5">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <!-- Tabs Scrollable -->
          <div class="flex gap-2 overflow-x-auto pb-2 w-full md:w-auto scrollbar-hide">
            <button
              v-for="tab in tabs" :key="tab.id"
              class="flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap border-2 transition-all duration-200"
              :class="searchState.status === tab.id 
                ? 'border-[#6367FF] bg-[#F0EEFF] text-[#6367FF]' 
                : 'border-transparent bg-white text-gray-500 hover:bg-gray-100'"
              @click="searchState.status = tab.id"
            >
              {{ tab.label }}
              <span v-if="counts[tab.id] > 0" class="bg-[#6367FF] text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ counts[tab.id] }}</span>
            </button>
          </div>
          
          <!-- Search -->
          <div class="w-full md:w-64 relative shrink-0">
            <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
            <input 
              v-model="searchState.search" 
              type="text" 
              placeholder="Cari order # atau nama..." 
              class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-sm focus:border-[#6367FF] focus:ring-1 focus:ring-[#6367FF] shadow-sm bg-white"
            >
          </div>
        </div>

        <!-- Checkbox Only Unpaid -->
        <label class="inline-flex items-center gap-2 cursor-pointer bg-gray-50 px-4 py-2 rounded-xl border border-gray-200 w-fit self-start mt-3">
          <input type="checkbox" v-model="searchState.only_unpaid" class="w-4 h-4 text-[#6367FF] rounded focus:ring-[#6367FF]">
          <span class="text-sm font-bold text-gray-700">Hanya Belum Bayar</span>
        </label>
      </div>

      <!-- Content Area -->
      <div v-if="orders.data.length === 0" class="app-card-lg flex flex-col items-center justify-center py-24">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
          <InboxIcon class="h-10 w-10 text-gray-300" />
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-1">
          {{ hasActiveFilters ? 'Tidak ada pesanan dengan filter ini' : 'Belum ada pesanan' }}
        </h3>
        <p class="text-gray-500 text-sm max-w-xs text-center">
          {{ hasActiveFilters ? 'Coba ubah kata pencarian atau hapus filter status saat ini.' : 'Semua pesanan yang masuk akan otomatis tertampil di sini.' }}
        </p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <!-- Order Cards -->
        <div v-for="order in orders.data" :key="order.id" class="app-card p-5 rounded-[20px] flex flex-col hover:shadow-md transition-shadow">
          <!-- Card Header -->
          <div class="flex items-start justify-between mb-3 border-b border-gray-100 pb-3">
            <div class="flex flex-col">
              <span class="text-[#6367FF] font-black text-lg">#{{ order.id.toString().padStart(5, '0') }}</span>
              <span class="text-xs text-gray-500 font-bold">{{ formatTime(order.created_at) }}</span>
            </div>
            <div class="flex flex-col items-end gap-1.5">
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-md" :class="getPaymentBadgeClass(order.payment_status)">
                {{ order.payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
              </span>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-md uppercase" :class="getStatusBadgeClass(order.status)">
                {{ getStatusLabel(order.status) }}
              </span>
            </div>
          </div>
          
          <!-- Card Body -->
          <div class="flex items-center gap-2 mb-3 bg-gray-50 p-2.5 rounded-xl border border-gray-100/50">
            <UserIcon class="h-4 w-4 text-gray-500 shrink-0" />
            <span class="font-bold text-sm text-gray-800 flex-1 truncate">{{ order.customer_name }}</span>
            <div v-if="order.table_number" class="flex items-center gap-1 bg-white px-2 py-0.5 rounded shadow-sm border border-gray-200 shrink-0">
              <span class="text-[10px] font-black text-gray-600">Meja {{ order.table_number }}</span>
            </div>
          </div>

          <!-- Items List -->
          <div class="flex-1 mb-4 overflow-y-auto max-h-[150px] pr-2 custom-scroll">
            <ul class="space-y-2">
              <li v-for="item in order.order_items" :key="item.id" class="flex gap-2 items-start text-sm">
                <span class="font-black text-[#6367FF] shrink-0">{{ item.quantity }}x</span>
                <div class="flex flex-col flex-1">
                  <span class="font-bold text-gray-700 leading-tight">{{ item.menu_item?.name }}</span>
                  <p v-if="item.note" class="text-[11px] text-red-500 italic font-semibold leading-tight mt-0.5">"{{ item.note }}"</p>
                </div>
              </li>
            </ul>
          </div>
          
          <!-- Card Footer -->
          <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex flex-col">
              <span class="text-xs font-semibold text-gray-500">Total Pembayaran</span>
              <span class="text-[#6367FF] font-black text-lg leading-tight">{{ formatPrice(order.total_price) }}</span>
            </div>
            
            <div class="flex gap-2 sm:w-auto w-full">
              <a 
                :href="route('order.receipt', order.id)" 
                target="_blank"
                class="action-icon-btn print"
              >
                <PrinterIcon class="h-5 w-5" />
              </a>
              <button 
                v-if="order.payment_status === 'unpaid' || order.status === 'pending'" 
                @click="quickConfirmOrder(order)" 
                class="flex-1 sm:flex-none bg-green-500 hover:bg-green-600 text-white font-bold text-xs px-4 py-2 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-1"
              >
                &check; Konfirmasi Bayar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="orders.data.length > 0" class="app-card flex flex-col sm:flex-row justify-between items-center gap-4 p-4 rounded-2xl">
        <span class="text-sm text-gray-500 font-medium font-sans">
          Menampilkan <span class="font-bold text-gray-800">{{ orders.from }}</span>-<span class="font-bold text-gray-800">{{ orders.to }}</span> dari <span class="font-bold text-gray-800">{{ orders.total }}</span> pesanan
        </span>
        <div class="flex items-center gap-1">
          <button 
            @click="goToPage(orders.prev_page_url)"
            :disabled="!orders.prev_page_url"
            class="px-3 py-2 rounded-lg text-sm font-bold border border-gray-200 text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors"
          >
            &larr; Sebelumnya
          </button>
          
          <div class="hidden md:flex gap-1 mx-2">
            <button
              v-for="link in paginationLinks"
              :key="link.label"
              @click="goToPage(link.url)"
              :disabled="!link.url || link.active"
              class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-bold border transition-colors"
              :class="link.active ? 'bg-[#6367FF] border-[#6367FF] text-white' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
            >
              {{ link.label }}
            </button>
          </div>
          
          <button 
            @click="goToPage(orders.next_page_url)"
            :disabled="!orders.next_page_url"
            class="px-3 py-2 rounded-lg text-sm font-bold border border-gray-200 text-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 transition-colors"
          >
            Berikutnya &rarr;
          </button>
        </div>
      </div>

    </div>

    <!-- ── PAYMENT MODAL ────────────────────────────────────── -->
    <transition name="admin-modal">
      <div v-if="showPayModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4" @click.self="showPayModal = false">
        <div class="bg-white w-full max-w-sm rounded-[24px] overflow-hidden shadow-2xl scale-100 transition-transform">
          <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-lg text-gray-900 m-0 leading-none">Konfirmasi Bayar</h3>
            <button @click="showPayModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
          </div>
          <div class="p-5">
            <p class="text-sm text-gray-600 mb-4 leading-relaxed">Pilih metode pembayaran untuk Order <strong class="text-[#6367FF]">#{{ selectedOrder?.id }}</strong> atas nama <strong class="text-gray-800">{{ selectedOrder?.customer_name }}</strong>:</p>
            <div class="flex flex-col gap-2">
              <button v-for="opt in payOptions" :key="opt.id" @click="confirmPayment(opt.id)" class="flex items-center gap-3 p-3 rounded-xl border-2 border-gray-100 bg-white hover:border-[#6367FF] hover:bg-[#F0EEFF] text-left transition-colors text-gray-700 hover:text-[#6367FF] font-bold">
                <component :is="opt.icon" class="h-5 w-5" />
                <span>{{ opt.label }}</span>
              </button>
            </div>
          </div>
          <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button @click="showPayModal = false" class="px-5 py-2 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-200 transition-colors">Batal</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ── SUCCESS PAYMENT MODAL ──────────────────────────────── -->
    <transition name="admin-modal">
      <div v-if="showSuccessModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-gray-900/60 backdrop-blur-md p-4">
        <div class="bg-white w-full max-w-sm rounded-[32px] overflow-hidden shadow-2xl scale-100 p-8 text-center border border-white/20">
          <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <CheckCircleIcon class="h-10 w-10 text-green-600 shadow-sm" />
          </div>
          
          <h3 class="text-2xl font-black text-gray-900 mb-2">Pembayaran Berhasil! ✅</h3>
          
          <div class="bg-gray-50 rounded-2xl p-4 mb-8 border border-gray-100">
            <p class="text-sm font-black text-[#6367FF] mb-1">
              Order #{{ selectedOrder?.id?.toString().padStart(5, '0') }}
            </p>
            <p class="text-base font-bold text-gray-800">
              {{ selectedOrder?.customer_name }}
              <span v-if="selectedOrder?.table_number" class="text-gray-400 mx-1">•</span>
              <span v-if="selectedOrder?.table_number" class="text-gray-500">Meja {{ selectedOrder?.table_number }}</span>
            </p>
            <div class="h-px bg-gray-200 my-3"></div>
            <p class="text-xl font-black text-gray-900">{{ formatPrice(selectedOrder?.total_price || 0) }}</p>
          </div>

          <div class="flex flex-col gap-3">
            <a 
              :href="route('order.receipt', selectedOrder?.id)" 
              target="_blank"
              @click="showSuccessModal = false"
              class="w-full bg-[#6367FF] hover:bg-[#4B3FA0] text-white font-black py-4 rounded-2xl shadow-lg shadow-[#6367FF]/30 transition-all active:scale-95 flex items-center justify-center gap-2"
            >
              <PrinterIcon class="h-5 w-5" />
              Cetak Struk
            </a>
            <button 
              @click="showSuccessModal = false" 
              class="w-full py-4 bg-white border-2 border-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-50 transition-all active:scale-95"
            >
              Lewati
            </button>
          </div>
        </div>
      </div>
    </transition>

  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowPathIcon, UserIcon, HashtagIcon,
  BanknotesIcon, QrCodeIcon, ArrowsRightLeftIcon as TransferIcon,
  MagnifyingGlassIcon, InboxIcon, BellAlertIcon, PrinterIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  orders: Object,
  filters: Object,
  counts: Object
});

const lastUpdate = ref(new Date().toLocaleTimeString('id-ID'));
const manualRefreshing = ref(false);
const showToast = ref(false);
let toastTimeout = null;

// Audio
const playDing = () => {
  const audioContext = new (window.AudioContext || window.webkitAudioContext)();
  const oscillator = audioContext.createOscillator();
  const gainNode = audioContext.createGain();

  oscillator.connect(gainNode);
  gainNode.connect(audioContext.destination);

  oscillator.type = 'sine';
  oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // A5
  oscillator.frequency.exponentialRampToValueAtTime(440, audioContext.currentTime + 0.3); // A4

  gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
  gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

  oscillator.start();
  oscillator.stop(audioContext.currentTime + 0.3);
};

// Filter State
const searchState = reactive({
  status: props.filters?.status || 'semua',
  only_unpaid: props.filters?.only_unpaid === 'true' || false,
  search: props.filters?.search || '',
  per_page: props.filters?.per_page || 9
});

const hasActiveFilters = computed(() => {
  return searchState.status !== 'semua' || searchState.only_unpaid || searchState.search.length > 0;
});

// Watch Filters and Push to Inertia
const fetchOrders = debounce(() => {
  router.get(route('admin.orders'), {
    status: searchState.status,
    only_unpaid: searchState.only_unpaid ? 'true' : 'false',
    search: searchState.search,
    per_page: searchState.per_page,
    page: 1 // reset to page 1 on search
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['orders', 'counts', 'filters'],
    onSuccess: () => { lastUpdate.value = new Date().toLocaleTimeString('id-ID'); }
  });
}, 300);

watch([
  () => searchState.status,
  () => searchState.only_unpaid,
  () => searchState.search
], () => {
  fetchOrders();
});

// Calculate Device Per Page
onMounted(() => {
  const isMobile = window.innerWidth < 1024; // Use lg breakpoint for 9 orders desktop vs fewer
  const targetPerPage = isMobile ? 5 : 9;
  
  if (searchState.per_page != targetPerPage) {
    searchState.per_page = targetPerPage;
    fetchOrders(); // Initial alignment of count requirement
  }
});

// Polling background reload
let pollingInterval = null;
onMounted(() => {
  pollingInterval = setInterval(() => {
    // Determine route manually to preserve existing standard url args (like page).
    // The current pagination page parameter is actually accessible on page url, it's safer to use reload.
    router.reload({
      only: ['orders', 'counts'],
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => { lastUpdate.value = new Date().toLocaleTimeString('id-ID'); }
    });
  }, 5000);
});

onUnmounted(() => {
  clearInterval(pollingInterval);
});

// Monitor new orders to ring bell
watch(() => props.orders?.data, (newVal, oldVal) => {
  if (newVal && oldVal) {
    if (newVal.length > 0 && oldVal.length > 0) {
      if (newVal[0].id > oldVal[0].id) {
        playDing();
        showToast.value = true;
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => { showToast.value = false; }, 3000);
      }
    }
  }
}, { deep: false });

function manualRefresh() {
  manualRefreshing.value = true;
  router.reload({
    only: ['orders', 'counts'],
    preserveScroll: true,
    preserveState: true,
    onFinish: () => { 
      manualRefreshing.value = false; 
      lastUpdate.value = new Date().toLocaleTimeString('id-ID');
    }
  });
}

function goToPage(url) {
  if (!url) return;
  router.get(url, {
    status: searchState.status,
    only_unpaid: searchState.only_unpaid ? 'true' : 'false',
    search: searchState.search,
    per_page: searchState.per_page
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['orders', 'counts', 'filters']
  });
}

const paginationLinks = computed(() => {
  if (!props.orders?.links) return [];
  return props.orders.links.filter(l => !l.label.includes('&laquo;') && !l.label.includes('&raquo;'));
});

// Tabs setup
const tabs = [
  { id: 'semua', label: 'Semua' },
  { id: 'pending', label: 'Menunggu' },
  { id: 'processing', label: 'Diproses' },
  { id: 'done', label: 'Selesai' },
];

// Badge Classes
function getPaymentBadgeClass(status) {
  if (status === 'paid') return 'bg-green-100 text-green-700';
  return 'bg-red-100 text-red-700'; 
}

function getStatusBadgeClass(status) {
  switch(status) {
    case 'pending': return 'bg-yellow-100 text-yellow-700';
    case 'processing': return 'bg-blue-100 text-blue-700';
    case 'done': return 'bg-purple-100 text-purple-700';
    default: return 'bg-gray-100 text-gray-700';
  }
}

function getStatusLabel(status) {
  switch(status) {
    case 'pending': return 'Menunggu';
    case 'processing': return 'Diproses';
    case 'done': return 'Selesai';
    default: return status;
  }
}

function updateStatus(id, status) {
  router.post(route('admin.orders.status', id), { status }, { preserveScroll: true });
}

function quickConfirmOrder(order) {
  if (confirm('Konfirmasi pembayaran order ini?')) {
    router.post(route('admin.orders.quickConfirm', order.id), {}, { preserveScroll: true });
  }
}

const showPayModal = ref(false);
const showSuccessModal = ref(false);
const selectedOrder = ref(null);
const payOptions = [
  { id: 'cash', label: 'Cash / Tunai', icon: BanknotesIcon },
  { id: 'qris', label: 'QRIS (di Kasir)', icon: QrCodeIcon },
  { id: 'transfer', label: 'Transfer Bank', icon: TransferIcon },
];

function openPayModal(order) {
  selectedOrder.value = order;
  showPayModal.value = true;
}

function confirmPayment(method) {
  if (!selectedOrder.value) return;
  router.post(route('admin.orders.payment', selectedOrder.value.id), { 
    payment_status: 'paid',
    payment_method: method
  }, { 
    preserveScroll: true,
    onSuccess: () => {
      showPayModal.value = false;
      showSuccessModal.value = true;
    }
  });
}

function formatPrice(price) { return 'Rp ' + Number(price).toLocaleString('id-ID'); }
function formatTime(dateStr) { return new Date(dateStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }); }
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scroll::-webkit-scrollbar { width: 4px; }
.custom-scroll::-webkit-scrollbar-thumb { background-color: #E5E7EB; border-radius: 4px; }

.toast-slide-enter-active, .toast-slide-leave-active { transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateY(-20px) scale(0.95); }

.admin-modal-enter-active, .admin-modal-leave-active { transition: opacity 0.24s ease; }
.admin-modal-enter-from, .admin-modal-leave-to { opacity: 0; }
.admin-modal-enter-active > div > div { animation: adminModalPop 0.28s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes adminModalPop { 0% { transform: scale(0.95) translateY(10px); opacity: 0; } 100% { transform: scale(1) translateY(0); opacity: 1; } }

.action-icon-btn {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid transparent;
  transition: all 0.2s ease;
}

.action-icon-btn.print {
  background: #f3f4f6;
  color: #4b5563;
  border-color: #e5e7eb;
}

.action-icon-btn.print:hover {
  background: #e5e7eb;
}
</style>
