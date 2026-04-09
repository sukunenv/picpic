<template>
  <AppLayout title="Status Pesanan">
    <div class="status-page min-h-screen bg-gray-50 pb-32">

      <!-- ── RECEIPT CARD ─────────────────────────────────── -->
      <div class="px-5 sm:px-6 pt-16 pb-7">
        <div class="app-card-lg p-6">
          <div class="text-center mb-6">
            <div class="check-ring mx-auto mb-4">
              <div class="check-circle shadow-lg shadow-[#6367FF]/20">
                <CheckIcon class="check-icon" />
              </div>
            </div>

            <h1 class="app-page-title text-primary mb-1">Pesanan Berhasil Dibuat</h1>
            <p class="text-gray-400 text-sm font-bold tracking-wider">
              Nomor Order: <span class="text-accent">#PIC-{{ order.id.toString().padStart(3, '0') }}</span>
            </p>
          </div>

          <div class="bg-[#f7f8ff] border border-primary/10 rounded-2xl p-4 mb-5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#6367FF]/10 flex items-center justify-center shrink-0">
                <ClockIcon class="h-5 w-5 text-[#6367FF]" />
              </div>
              <div>
                <p class="text-primary font-semibold text-sm leading-tight">Pesanan sedang diproses</p>
                <p class="text-gray-500 text-xs">Estimasi 15 menit</p>
              </div>
            </div>
            <div
              class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border"
              :class="order.payment_status === 'paid'
                ? 'bg-green-50 border-green-100 text-green-700'
                : 'bg-yellow-50 border-yellow-100 text-yellow-700'"
            >
              {{ order.payment_status === 'paid' ? 'Lunas' : 'Kasir' }}
            </div>
          </div>

          <div class="flex items-center justify-between text-[11px] text-gray-400 mb-4 px-1">
            <span>{{ new Date().toLocaleDateString('id-ID') }}</span>
            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
            <span>{{ new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}</span>
          </div>

          <h3 class="text-primary/50 font-black text-[11px] uppercase tracking-[0.2em] mb-5 flex items-center gap-2">
            Ringkasan Pesanan
            <div class="h-px flex-1 bg-gray-100"></div>
          </h3>

          <div class="space-y-4">
            <div v-for="item in order.order_items" :key="item.id" class="flex items-start gap-4">
              <div class="w-8 h-8 bg-[#6367FF]/5 rounded-xl flex items-center justify-center text-[11px] font-black text-[#6367FF] shrink-0 border border-[#6367FF]/10">
                {{ item.quantity }}x
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-sm text-primary font-bold block truncate leading-tight">{{ item.menu_item?.name }}</span>
                <p v-if="item.note" class="text-[10px] text-gray-400 italic mt-1">"{{ item.note }}"</p>
              </div>
              <span class="text-sm font-black text-primary shrink-0">{{ formatPrice(item.price * item.quantity) }}</span>
            </div>
          </div>

          <div class="relative my-5 py-1">
            <div class="border-t border-dashed border-gray-200"></div>
            <span class="absolute -left-6 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-gray-50 border border-gray-100"></span>
            <span class="absolute -right-6 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-gray-50 border border-gray-100"></span>
          </div>

          <div class="flex justify-between items-center bg-[#F8F9FA] -mx-1 p-3 rounded-2xl border border-gray-100/50">
            <span class="text-gray-400 font-bold text-[11px] uppercase tracking-wider ml-1">Total Pembayaran</span>
            <span class="text-xl font-black text-[#6367FF]">{{ formatPrice(order.total_price) }}</span>
          </div>
        </div>
      </div>

      <!-- ── ACTIONS ──────────────────────────────────────── -->
      <div class="px-5 sm:px-6 pb-12 sticky bottom-3 sm:bottom-4">
        <button 
          @click="goHome" 
          class="app-btn-primary w-full py-4 text-base flex items-center justify-center gap-2"
        >
          <HomeIcon class="h-5 w-5" />
          Kembali ke Beranda
        </button>
        <p class="text-center text-gray-400 text-[11px] font-bold mt-6 leading-relaxed">
          Tunjukkan halaman ini ke kasir<br>untuk melakukan pembayaran
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  CheckIcon, 
  ClockIcon,
  HomeIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  order: Object
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const goHome = () => {
  if (user.value && user.value.is_admin) {
    router.visit('/admin/dashboard');
  } else {
    router.visit('/');
  }
};

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}
</script>

<style scoped>
.status-page {
  background: #f9fafb;
}

/* Check Animation */
.check-ring {
  width: 100px;
  height: 100px;
  position: relative;
}

.check-circle {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #6367FF, #8494FF);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: circleScale 0.35s ease-out forwards;
}

.check-icon {
  width: 48px;
  height: 48px;
  color: white;
  stroke-width: 3.5;
  animation: checkDraw 0.25s 0.15s ease-out both;
}

@keyframes circleScale {
  0% { transform: scale(0.9); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes checkDraw {
  from { opacity: 0; transform: scale(0.85); }
  to { opacity: 1; transform: scale(1); }
}
</style>
