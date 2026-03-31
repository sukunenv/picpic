<template>
  <AdminLayout title="Dashboard Admin">
    <div class="dashboard-page">
      <!-- ── HEADER ──────────────────────────────────────── -->
      <header class="page-header">
        <h1 class="greeting">Selamat datang, Admin Picpic 👋</h1>
        <p class="current-time">{{ currentDateTime }}</p>
      </header>

      <!-- ── STATS GRID (Always in one grid) ─────────────── -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div v-for="stat in statCards" :key="stat.label" class="stat-card" :class="stat.colorClass">
          <div class="stat-top">
            <p class="text-xs uppercase font-bold tracking-wider" :class="stat.labelColor">{{ stat.label }}</p>
            <div class="stat-icon-circle" :class="stat.iconClass">
              <component :is="stat.icon" class="h-5 w-5" />
            </div>
          </div>
          <p class="text-4xl font-bold mt-2" :class="stat.valueColor">{{ stat.value }}</p>
        </div>
      </div>

      <!-- ── MAIN CONTENT SECTIONS ────────────────────────── -->
      <div class="sections-container">
        <!-- ── PESANAN TERBARU ────────────────────────────── -->
        <section class="content-card">
          <div class="card-header">
            <h2 class="card-title">Pesanan Terbaru</h2>
            <Link href="/admin/orders" class="btn-all">Lihat Semua</Link>
          </div>
          
          <div class="table-outer">
            <table class="data-table">
              <thead class="bg-gray-50/50">
                <tr>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-semibold text-gray-500 border-b border-gray-100">No. Order</th>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-semibold text-gray-500 border-b border-gray-100">Nama</th>
                  <th class="hidden lg:table-cell text-left py-3 px-4 text-[11px] uppercase tracking-wider font-semibold text-gray-500 border-b border-gray-100">Meja</th>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-semibold text-gray-500 border-b border-gray-100">Total</th>
                  <th class="text-left py-3 px-4 text-[11px] uppercase tracking-wider font-semibold text-gray-500 border-b border-gray-100">Status</th>
                  <th class="hidden lg:table-cell text-left py-3 px-4 text-[11px] uppercase tracking-wider font-semibold text-gray-500 border-b border-gray-100">Waktu</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50 transition-colors duration-150">
                  <td class="order-id py-3 px-4">#{{ order.id.toString().padStart(3, '0') }}</td>
                  <td class="cust-name py-3 px-4">{{ order.customer_name }}</td>
                  <td class="hidden lg:table-cell py-3 px-4">{{ order.table_number || '-' }}</td>
                  <td class="total-bold py-3 px-4">{{ formatPrice(order.total_price) }}</td>
                  <td class="py-3 px-4">
                    <span class="badge" :class="order.payment_status === 'paid' ? 'paid' : 'pending'">
                      {{ order.payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                  </td>
                  <td class="hidden lg:table-cell text-gray-400 py-3 px-4">{{ formatTime(order.created_at) }}</td>
                </tr>
                <tr v-if="recentOrders.length === 0">
                  <td colspan="6" class="empty-text">Menunggu pesanan pertama...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- ── MENU TIDAK TERSEDIA ───────────────────────────── -->
        <section class="content-card">
          <h2 class="card-title mb-4">Menu Tidak Tersedia</h2>
          
          <div v-if="unavailableMenus.length > 0" class="unavailable-scroll">
            <div class="unavailable-list">
              <div v-for="menu in unavailableMenus" :key="menu.id" class="menu-mini-card">
                <div class="menu-info">
                  <span class="m-name">{{ menu.name }}</span>
                  <span class="m-cat text-gray-400">{{ menu.category }}</span>
                </div>
                <button class="btn-activate" @click="toggleMenu(menu.id)">
                  Aktifkan
                </button>
              </div>
            </div>
          </div>
          
          <div v-else class="empty-state-illust">
            <div class="illust-circle">
              <CheckBadgeIcon class="h-10 w-10 text-green-500" />
            </div>
            <p class="illust-text">Semua menu siap dipesan</p>
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
  CheckBadgeIcon
} from '@heroicons/vue/24/outline';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
  stats: Object,
  recentOrders: Array,
  unavailableMenus: Array
});

const currentDateTime = ref('');

function updateTime() {
  currentDateTime.value = new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'full',
    timeStyle: 'short'
  }).format(new Date());
}

onMounted(() => {
  updateTime();
  setInterval(updateTime, 60000); // Update every minute
});

const statCards = computed(() => [
  { 
    label: 'Total Order', 
    value: props.stats.total_orders, 
    icon: ClipboardDocumentListIcon,
    colorClass: 'bg-purple-50 border-l-4 border-purple-500',
    iconClass: 'bg-purple-100 text-purple-600',
    labelColor: 'text-purple-600/70',
    valueColor: 'text-purple-900'
  },
  { 
    label: 'Pending', 
    value: props.stats.pending_orders, 
    icon: ClockIcon,
    colorClass: 'bg-yellow-50 border-l-4 border-yellow-500',
    iconClass: 'bg-yellow-100 text-yellow-600',
    labelColor: 'text-yellow-700/70',
    valueColor: 'text-yellow-900'
  },
  { 
    label: 'Pendapatan', 
    value: formatPrice(props.stats.today_revenue), 
    icon: BanknotesIcon,
    colorClass: 'bg-green-50 border-l-4 border-green-500',
    iconClass: 'bg-green-100 text-green-600',
    labelColor: 'text-green-700/70',
    valueColor: 'text-green-900'
  },
  { 
    label: 'Belum Bayar', 
    value: props.stats.unpaid_orders, 
    icon: ExclamationCircleIcon,
    colorClass: 'bg-red-50 border-l-4 border-red-500',
    iconClass: 'bg-red-100 text-red-600',
    labelColor: 'text-red-700/70',
    valueColor: 'text-red-900'
  }
]);

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

/* ── HEADER ──────────────── */
.page-header { margin-bottom: 24px; }
.greeting { 
  font-size: 1.25rem; /* text-xl */
  font-weight: 800; 
  color: #1B1B1B; 
  margin: 0 0 4px;
}
.current-time { font-size: 0.875rem; color: #6B7280; font-weight: 500; }

@media (min-width: 1024px) {
  .greeting { font-size: 1.25rem; }
}

/* ── STATS GRID ──────────── */
/* Grid classes replaced via Tailwind */

.stat-card {
  padding: 16px; /* p-4 */
  border-radius: 1.25rem; /* rounded-2xl */
  box-shadow: 0 1px 3px rgba(0,0,0,0.05); /* shadow-sm */
  min-height: 120px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.stat-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.stat-icon-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Stat specific utilities eliminated, injected via tailwind in template */

/* ── SECTIONS ──────────────── */
.sections-container { display: flex; flex-direction: column; gap: 24px; }

.content-card {
  background: white;
  border-radius: 1.5rem;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.card-title { font-size: 1.125rem; font-weight: 800; color: #1B1B1B; margin: 0; }
.btn-all { font-size: 13px; font-weight: 700; color: #7C6BC4; text-decoration: none; }

/* ── TABLE ──────────────────── */
.table-outer { width: 100%; border-radius: 12px; overflow-x: auto; }

.data-table { width: 100%; border-collapse: collapse; min-width: 500px; }
@media (max-width: 640px) { .data-table { min-width: 450px; } }

/* Table cell layout now mapped to tailwind classes in template */
.data-table td { font-size: 13px; color: #374151; }

.order-id { font-weight: 700; color: #7C6BC4; }
.cust-name { font-weight: 600; }
.total-bold { font-weight: 800; }

.badge { font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 8px; }
.badge.paid { background: #DCFCE7; color: #166534; }
.badge.pending { background: #FEF3C7; color: #92400E; }

/* table cell display handled by Tailwind classes hidden lg:table-cell */

/* ── UNAVAILABLE MENUS ───────── */
.unavailable-scroll { overflow-x: auto; padding-bottom: 8px; }
.unavailable-list { display: flex; gap: 12px; }

.menu-mini-card {
  min-width: 220px;
  background: #F9FAFB;
  border-radius: 16px;
  padding: 14px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid #F3F4F6;
}

.menu-info { display: flex; flex-direction: column; }
.m-name { font-size: 14px; font-weight: 700; color: #1B1B1B; }
.m-cat { font-size: 11px; font-weight: 600; }

.btn-activate {
  padding: 6px 14px;
  background: white;
  border: 1.5px solid #7C6BC4;
  color: #7C6BC4;
  font-size: 12px;
  font-weight: 700;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-activate:hover { background: #F0EEFF; }

/* ── EMPTY STATE ─────────────── */
.empty-state-illust {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 30px 0;
  text-align: center;
}

.illust-circle {
  width: 64px;
  height: 64px;
  background: #F0FDF4;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}

.illust-text { font-size: 14px; color: #4B5563; font-weight: 600; }
</style>
