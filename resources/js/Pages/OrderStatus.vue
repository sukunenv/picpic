<template>
  <AppLayout title="Status Pesanan">
    <div class="status-page min-h-screen bg-gray-50 pb-20">

      <!-- ── SUCCESS ANIMATION ────────────────────────────── -->
      <div class="pt-16 pb-8 text-center relative">
        <!-- Confetti-like particles -->
        <div class="confetti">
          <div v-for="n in 12" :key="n" class="particle" :style="{ '--i': n }"></div>
        </div>

        <div class="check-ring mx-auto mb-6">
          <div class="check-circle shadow-lg shadow-[#6367FF]/30">
            <CheckIcon class="check-icon" />
          </div>
        </div>
        
        <div class="px-8 mb-6">
          <h1 class="text-primary text-2xl font-black mb-1">Pesanan Masuk! 🎉</h1>
          <p class="text-gray-400 text-sm font-bold tracking-wider">Nomor Order: <span class="text-accent">#PIC-{{ order.id.toString().padStart(3, '0') }}</span></p>
        </div>
      </div>

      <!-- ── STATUS BADGE ────────────────────────────────── -->
      <div class="px-8 mb-8 text-center">
        <div 
          class="inline-block px-6 py-2.5 rounded-full text-[11px] font-black tracking-[0.1em] shadow-sm uppercase"
          :class="order.payment_status === 'paid' 
            ? 'bg-green-100 text-green-700' 
            : 'bg-yellow-100 text-yellow-700'"
        >
          {{ order.payment_status === 'paid' ? 'LUNAS ✓' : 'BAYAR DI KASIR' }}
        </div>
      </div>

      <!-- ── ESTIMATE ──────────────────────────────────────── -->
      <div class="px-8 mb-6">
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-gray-100 flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-[#6367FF]/10 flex items-center justify-center shrink-0">
            <ClockIcon class="h-6 w-6 text-[#6367FF]" />
          </div>
          <div>
            <p class="text-primary font-black text-sm leading-tight mb-0.5">⏰ Pesanan masuk!</p>
            <p class="text-gray-400 text-xs font-bold">Estimasi ±15 menit</p>
          </div>
        </div>
      </div>

      <!-- ── ORDER SUMMARY ────────────────────────────────── -->
      <div class="px-8 mb-8">
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-gray-100">
          <h3 class="text-primary font-black text-sm uppercase tracking-wider mb-5">Ringkasan Pesanan</h3>
          <div class="space-y-4">
            <div v-for="item in order.order_items" :key="item.id" class="flex items-center gap-4">
              <div class="w-8 h-8 bg-[#6367FF]/5 rounded-xl flex items-center justify-center text-[11px] font-black text-[#6367FF] shrink-0">
                {{ item.quantity }}x
              </div>
              <span class="flex-1 text-sm text-gray-700 font-bold truncate">{{ item.menu_item?.name }}</span>
              <span class="text-sm font-black text-primary shrink-0">{{ formatPrice(item.price * item.quantity) }}</span>
            </div>
          </div>
          
          <div class="border-t border-dashed border-gray-200 my-5"></div>
          
          <div class="flex justify-between items-center">
            <span class="text-gray-500 font-bold text-xs uppercase tracking-wide">Total Pembayaran</span>
            <span class="text-xl font-black text-[#6367FF]">{{ formatPrice(order.total_price) }}</span>
          </div>
        </div>
      </div>

      <!-- ── ACTIONS ──────────────────────────────────────── -->
      <div class="px-6 space-y-3 mt-8 pb-12">
        <button 
          @click="router.visit('/')" 
          class="w-full py-4 bg-[#6367FF] text-white rounded-2xl font-semibold text-base shadow-lg shadow-[#6367FF]/20 transition-all active:scale-95"
        >
          Kembali ke Beranda
        </button>
        <p class="text-center text-gray-400 text-xs mt-4 font-normal">
          Tunjukkan halaman ini ke kasir untuk pembayaran
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  CheckIcon, 
  ClockIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  order: Object
});

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
  width: 90px;
  height: 90px;
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
  animation: circleScale 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.check-icon {
  width: 44px;
  height: 44px;
  color: white;
  stroke-width: 3;
  animation: checkDraw 0.4s 0.3s ease-out both;
}

@keyframes circleScale {
  0% { transform: scale(0); opacity: 0; }
  70% { transform: scale(1.15); }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes checkDraw {
  from { opacity: 0; transform: scale(0.3); }
  to { opacity: 1; transform: scale(1); }
}

/* Confetti particles */
.confetti {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  pointer-events: none;
}

.particle {
  position: absolute;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  animation: confetti-burst 1.2s calc(var(--i) * 0.05s) ease-out forwards;
  opacity: 0;
}

.particle:nth-child(odd) { background: #C9BEFF; }
.particle:nth-child(even) { background: #6367FF; }
.particle:nth-child(3n) { background: #8494FF; width: 6px; height: 6px; border-radius: 2px; }

@keyframes confetti-burst {
  0% { opacity: 1; transform: translate(0, 0) scale(0); }
  50% { opacity: 1; transform: translate(calc(cos(calc(var(--i) * 30deg)) * 60px), calc(sin(calc(var(--i) * 30deg)) * 60px)) scale(1); }
  100% { opacity: 0; transform: translate(calc(cos(calc(var(--i) * 30deg)) * 90px), calc(sin(calc(var(--i) * 30deg)) * 90px)) scale(0.5); }
}
</style>
