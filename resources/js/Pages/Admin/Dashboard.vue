<template>
  <AdminLayout title="Dashboard Admin">
    <div class="pb-6">
      <header class="app-card-lg p-5 sm:p-6 mb-5 bg-gradient-to-br from-white to-[#f5f6ff]">
        <div class="flex items-start justify-between gap-4">
          <div>
            <p class="text-[11px] uppercase tracking-[0.16em] font-bold text-primary/50 mb-1">Dashboard Admin</p>
            <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">
              Selamat datang, Admin Picpic <span class="inline-block">👋</span>
            </h1>
            <p class="text-sm text-gray-500 font-medium mt-1">{{ currentDateTime }}</p>
          </div>
          <div class="hidden sm:flex items-center gap-3">
            <div class="text-right">
              <p class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Progress Lunas</p>
              <p class="text-sm font-black text-primary">{{ paidRate }}%</p>
            </div>
            <div class="relative w-11 h-11 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
              <SparklesIcon class="h-5 w-5" />
              <svg viewBox="0 0 36 36" class="absolute inset-0 w-11 h-11 -rotate-90">
                <circle cx="18" cy="18" r="15.5" fill="none" stroke="rgba(99,103,255,0.18)" stroke-width="2.6" />
                <circle
                  cx="18"
                  cy="18"
                  r="15.5"
                  fill="none"
                  stroke="#6367FF"
                  stroke-width="2.6"
                  stroke-linecap="round"
                  :stroke-dasharray="`${paidRate}, 100`"
                  pathLength="100"
                />
              </svg>
            </div>
          </div>
        </div>
      </header>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
        <div v-for="stat in statCards" :key="stat.label" class="app-card p-4 sm:p-5">
          <div class="flex items-start justify-between gap-3">
            <p class="text-[10px] sm:text-[11px] uppercase font-bold tracking-[0.14em]" :class="stat.labelColor">{{ stat.label }}</p>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" :class="stat.iconClass">
              <component :is="stat.icon" class="h-5 w-5" />
            </div>
          </div>
          <p class="text-2xl sm:text-3xl font-black mt-3 leading-none" :class="stat.valueColor">{{ stat.value }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <section class="app-card-lg p-5 sm:p-6 xl:col-span-2">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-gray-900">Pesanan Terbaru</h2>
            <Link href="/admin/orders" class="text-[13px] font-bold text-primary hover:text-primary-dark transition-colors">
              Lihat Semua
            </Link>
          </div>

          <div class="rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full min-w-[620px] border-collapse">
                <thead class="bg-gray-50/70">
                <tr>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-bold text-gray-500 border-b border-gray-100">No. Order</th>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-bold text-gray-500 border-b border-gray-100">Nama</th>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-bold text-gray-500 border-b border-gray-100">Meja</th>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-bold text-gray-500 border-b border-gray-100">Total</th>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-bold text-gray-500 border-b border-gray-100">Status</th>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-bold text-gray-500 border-b border-gray-100">Waktu</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50/70 transition-colors">
                  <td class="py-3 px-4 font-bold text-primary">#{{ order.id.toString().padStart(3, '0') }}</td>
                  <td class="py-3 px-4 font-semibold text-gray-800">{{ order.customer_name }}</td>
                  <td class="py-3 px-4 text-gray-600">{{ order.table_number || '-' }}</td>
                  <td class="py-3 px-4 font-bold text-gray-900">{{ formatPrice(order.total_price) }}</td>
                  <td class="py-3 px-4">
                    <span class="inline-flex text-[10px] font-bold px-2.5 py-1 rounded-lg" :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                      {{ order.payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-gray-400">{{ formatTime(order.created_at) }}</td>
                </tr>
                <tr v-if="recentOrders.length === 0">
                  <td colspan="6" class="text-center text-sm text-gray-400 py-8">Menunggu pesanan pertama...</td>
                </tr>
              </tbody>
            </table>
          </div>
          </div>
        </section>

        <section class="app-card-lg p-5 sm:p-6">
          <h2 class="text-lg font-black text-gray-900 mb-4">Insight Hari Ini</h2>
          <div class="space-y-3 mb-5">
            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-3.5">
              <div class="flex items-center justify-between text-xs font-semibold mb-2">
                <span class="text-gray-500">Order Selesai</span>
                <span class="text-primary">{{ paidOrders }} / {{ props.stats.total_orders }}</span>
              </div>
              <div class="h-2.5 rounded-full bg-white border border-gray-100 overflow-hidden">
                <div class="h-full bg-primary rounded-full" :style="{ width: `${paidRate}%` }"></div>
              </div>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-3.5">
              <div class="flex items-center justify-between text-xs font-semibold mb-2">
                <span class="text-gray-500">Pending</span>
                <span class="text-yellow-700">{{ pendingRate }}%</span>
              </div>
              <div class="h-2.5 rounded-full bg-white border border-gray-100 overflow-hidden">
                <div class="h-full bg-yellow-400 rounded-full" :style="{ width: `${pendingRate}%` }"></div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 rounded-2xl border border-gray-100 p-3.5 mb-5">
            <div class="flex items-center justify-between mb-3">
              <p class="text-xs font-semibold text-gray-500">Tren 7 Hari (Simulasi)</p>
              <p class="text-[11px] font-bold text-primary/70">Order</p>
            </div>
            <div class="flex items-end justify-between gap-1.5 h-16">
              <div v-for="point in sparklineData" :key="point.day" class="flex-1 flex flex-col items-center justify-end gap-1">
                <div class="w-full rounded-md bg-primary/20 overflow-hidden">
                  <div class="w-full bg-primary rounded-md" :style="{ height: `${point.height}px` }"></div>
                </div>
                <span class="text-[9px] font-semibold text-gray-400">{{ point.day }}</span>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-gray-900">Menu Tidak Tersedia</h2>
            <span class="text-xs font-bold text-gray-400">{{ unavailableMenus.length }} item</span>
          </div>

          <div v-if="unavailableMenus.length > 0" class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
            <div v-for="menu in unavailableMenus" :key="menu.id" class="bg-gray-50 border border-gray-100 rounded-2xl p-3.5 flex items-center justify-between gap-3">
              <div class="min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate">{{ menu.name }}</p>
                <p class="text-[11px] text-gray-400 font-semibold">{{ menu.category }}</p>
              </div>
              <button class="shrink-0 text-xs font-bold px-3 py-1.5 border border-primary text-primary rounded-lg hover:bg-primary/5 transition-colors" @click="toggleMenu(menu.id)">
                Aktifkan
              </button>
            </div>
          </div>

          <div v-else class="h-[260px] rounded-2xl border border-gray-100 bg-gray-50/60 flex flex-col items-center justify-center text-center px-4">
            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-3">
              <CheckBadgeIcon class="h-9 w-9 text-green-500" />
            </div>
            <p class="text-sm font-semibold text-gray-500">Semua menu siap dipesan</p>
          </div>
        </section>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  ClipboardDocumentListIcon,
  ClockIcon,
  BanknotesIcon,
  ExclamationCircleIcon,
  CheckBadgeIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  stats: Object,
  recentOrders: Array,
  unavailableMenus: Array
});

const currentDateTime = ref('');
let timeInterval = null;

function updateTime() {
  currentDateTime.value = new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'full',
    timeStyle: 'short'
  }).format(new Date());
}

onMounted(() => {
  updateTime();
  timeInterval = setInterval(updateTime, 60000);
});

onUnmounted(() => {
  if (timeInterval) clearInterval(timeInterval);
});

const statCards = computed(() => [
  {
    label: 'Total Order',
    value: props.stats.total_orders,
    icon: ClipboardDocumentListIcon,
    iconClass: 'bg-purple-100 text-purple-600',
    labelColor: 'text-purple-600/70',
    valueColor: 'text-purple-900'
  },
  {
    label: 'Pending',
    value: props.stats.pending_orders,
    icon: ClockIcon,
    iconClass: 'bg-yellow-100 text-yellow-600',
    labelColor: 'text-yellow-700/70',
    valueColor: 'text-yellow-900'
  },
  {
    label: 'Pendapatan',
    value: formatPrice(props.stats.today_revenue),
    icon: BanknotesIcon,
    iconClass: 'bg-green-100 text-green-600',
    labelColor: 'text-green-700/70',
    valueColor: 'text-green-900'
  },
  {
    label: 'Belum Bayar',
    value: props.stats.unpaid_orders,
    icon: ExclamationCircleIcon,
    iconClass: 'bg-red-100 text-red-600',
    labelColor: 'text-red-700/70',
    valueColor: 'text-red-900'
  }
]);

const safeTotalOrders = computed(() => Math.max(Number(props.stats.total_orders || 0), 0));
const paidOrders = computed(() => Math.max(safeTotalOrders.value - Number(props.stats.unpaid_orders || 0), 0));
const paidRate = computed(() => {
  if (safeTotalOrders.value === 0) return 0;
  return Math.min(100, Math.round((paidOrders.value / safeTotalOrders.value) * 100));
});
const pendingRate = computed(() => {
  if (safeTotalOrders.value === 0) return 0;
  return Math.min(100, Math.round((Number(props.stats.pending_orders || 0) / safeTotalOrders.value) * 100));
});

const sparklineData = computed(() => {
  const total = safeTotalOrders.value || 1;
  const paid = paidOrders.value || 0;
  const pending = Number(props.stats.pending_orders || 0);
  const unpaid = Number(props.stats.unpaid_orders || 0);

  const baseSeries = [
    Math.max(1, Math.round(total * 0.4)),
    Math.max(1, Math.round(total * 0.55 + pending * 0.2)),
    Math.max(1, Math.round(total * 0.5 + paid * 0.15)),
    Math.max(1, Math.round(total * 0.7 + unpaid * 0.1)),
    Math.max(1, Math.round(total * 0.65 + paid * 0.2)),
    Math.max(1, Math.round(total * 0.8 + pending * 0.1)),
    Math.max(1, Math.round(total * 0.75 + paid * 0.25))
  ];

  const maxVal = Math.max(...baseSeries, 1);
  const days = ['S', 'S', 'R', 'K', 'J', 'S', 'M'];

  return baseSeries.map((value, i) => ({
    day: days[i],
    height: Math.max(10, Math.round((value / maxVal) * 48))
  }));
});

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}

function formatTime(dateStr) {
  return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function toggleMenu(id) {
  router.post(`/admin/menu/${id}/toggle`, {}, {
    preserveScroll: true
  });
}
</script>

<style scoped>
.dashboard-page {
  padding-bottom: 20px;
}
</style>
