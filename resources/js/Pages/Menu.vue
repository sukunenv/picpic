<template>
  <AppLayout>
    <div class="menu-page">

      <!-- ── TOP BAR ───────────────────────────────────────── -->
      <div class="menu-topbar">
        <h1 class="menu-title">Menu</h1>
        <div class="search-wrap">
          <div class="search-bar">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 flex-shrink-0" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari menu…"
              class="search-input"
              @input="debouncedFetch"
            />
            <button v-if="searchQuery" class="clear-btn" @click="clearSearch">✕</button>
          </div>
        </div>
      </div>

      <!-- ── KATEGORI ───────────────────────────────────────── -->
      <div class="category-row">
        <button
          v-for="cat in categories"
          :key="cat.id"
          class="cat-pill"
          :class="{ active: activeCategory === cat.id }"
          @click="setCategory(cat.id)"
        >
          {{ cat.label }}
        </button>
      </div>

      <!-- Result count -->
      <div class="result-info">
        <span v-if="!loading">{{ filteredItems.length }} menu ditemukan</span>
        <span v-else>Memuat menu…</span>
      </div>

      <!-- ── GRID MENU ──────────────────────────────────────── -->
      <div class="menu-grid px-container">

        <!-- Skeleton loading -->
        <template v-if="loading">
          <div v-for="n in 6" :key="n" class="menu-card skeleton-card"></div>
        </template>

        <template v-else>
          <div
            v-for="item in filteredItems"
            :key="item.id"
            class="menu-card"
          >
            <div class="card-img-area">
              <img :src="item.image" :alt="item.name" class="card-img" />
              <span class="cat-badge">{{ item.category }}</span>
              <span v-if="item.is_popular" class="popular-badge">🔥</span>
            </div>
            <div class="card-body">
              <h3 class="card-name">{{ item.name }}</h3>
              <p class="card-desc">{{ item.description }}</p>
              <div class="card-footer">
                <div class="card-rating">
                  <StarIcon class="h-3 w-3 text-yellow-500 fill-current" />
                  <span>{{ item.rating }}</span>
                </div>
                <div class="card-right">
                  <p class="card-price">{{ formatPrice(item.price) }}</p>
                  <button class="add-btn" @click="addToCart(item)">
                    <PlusIcon class="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="filteredItems.length === 0" class="empty-state">
            <span class="empty-emoji">😔</span> <!-- Keeping this one or replacing? Replacing for consistency -->
            <MagnifyingGlassIcon class="h-12 w-12 mx-auto text-gray-300 mb-2" />
            <p>Tidak ada menu yang cocok</p>
            <button class="reset-btn" @click="resetFilter">Reset Filter</button>
          </div>
        </template>
      </div>

      <div style="height: 16px;"></div>
    </div>

    <!-- Snack -->
    <transition name="snack">
      <div v-if="showSnack" class="snackbar">
        <CheckCircleIcon class="h-5 w-5 inline mr-1" /> ✅ {{ snackMsg }}
      </div>
    </transition>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCartStore } from '@/stores/cartStore';
import { 
  MagnifyingGlassIcon, 
  StarIcon, 
  PlusIcon,
  Squares2X2Icon,
  BeakerIcon,
  CakeIcon,
  VariableIcon as MugIcon,
  WrenchScrewdriverIcon as UtensilsIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline';

const cartStore = useCartStore();
const allItems = ref([]);
const loading = ref(true);
const activeCategory = ref('Semua');
const searchQuery = ref('');
const showSnack = ref(false);
const snackMsg = ref('');
let debounceTimer = null;

const categories = [
  { id: 'Semua',    label: 'Semua',    icon: Squares2X2Icon },
  { id: 'Kopi',     label: 'Kopi',     icon: MugIcon },
  { id: 'Non-Kopi', label: 'Non-Kopi', icon: BeakerIcon },
  { id: 'Makanan',  label: 'Makanan',  icon: UtensilsIcon },
  { id: 'Dessert',  label: 'Dessert',  icon: CakeIcon },
];

function getCategoryIcon(cat) {
  const found = categories.find(c => c.id === cat);
  return found ? found.icon : Squares2X2Icon;
}

const filteredItems = computed(() => {
  let items = allItems.value;
  if (activeCategory.value !== 'Semua') {
    items = items.filter(i => i.category === activeCategory.value);
  }
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    items = items.filter(i => i.name.toLowerCase().includes(q));
  }
  return items;
});

async function fetchMenu() {
  loading.value = true;
  try {
    const res = await fetch('/api/menu-items');
    const json = await res.json();
    allItems.value = json.data;
  } finally {
    loading.value = false;
  }
}

function setCategory(cat) { activeCategory.value = cat; }

function clearSearch() { searchQuery.value = ''; }

function resetFilter() {
  activeCategory.value = 'Semua';
  searchQuery.value = '';
}

function debouncedFetch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {}, 300);
}

function addToCart(item) {
  cartStore.addItem(item);
  snackMsg.value = `${item.name} ditambahkan!`;
  showSnack.value = true;
  setTimeout(() => { showSnack.value = false; }, 2000);
}

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}

onMounted(fetchMenu);
</script>

<style scoped>
.menu-page {
  background: #F0EEFF;
  min-height: 100dvh;
}

/* Top Bar */
.menu-topbar {
  background: #ffffff;
  padding: 52px 16px 16px;
  border-bottom: 1px solid #F0F0F0;
}

.menu-title {
  font-size: 24px;
  font-weight: 800;
  color: #7C6BC4;
  margin: 0 0 14px;
}

.search-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #F7F8FA;
  border-radius: 16px;
  padding: 12px 16px;
  border: 1px solid #E5E7EB;
}

.search-icon { color: #9CA3AF; flex-shrink: 0; }

.search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 14px;
  color: #1B1B1B;
  outline: none;
}

.search-input::placeholder { color: #9CA3AF; }

.clear-btn {
  background: none;
  border: none;
  color: #9CA3AF;
  cursor: pointer;
  font-size: 14px;
  padding: 2px 4px;
}

/* Category Row */
.category-row {
  display: flex;
  gap: 8px;
  padding: 14px 16px;
  overflow-x: auto;
  scrollbar-width: none;
  background: #ffffff;
  border-bottom: 1px solid #F3F4F6;
}

.category-row::-webkit-scrollbar { display: none; }

.cat-pill {
  padding: 8px 16px;
  border-radius: 50px;
  border: 1.5px solid #E5E7EB;
  background: #ffffff;
  font-size: 13px;
  font-weight: 500;
  color: #4B5563;
  cursor: pointer;
  white-space: nowrap;
  min-height: 38px;
  transition: all 0.2s ease;
}

.cat-pill:hover { border-color: #9B8FD4; color: #7C6BC4; }

.cat-pill.active {
  background: #7C6BC4;
  border-color: #7C6BC4;
  color: #ffffff;
}

/* Result info */
.result-info {
  padding: 12px 16px 8px;
  font-size: 13px;
  color: #6B7280;
}

/* Grid */
.px-container { padding: 0 16px; }

.menu-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  padding-bottom: 8px;
}

.menu-card {
  background: #ffffff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.menu-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.card-img-area {
  height: 140px;
  position: relative;
}

.card-img {
  width: 100%;
  height: 100%;
  border-radius: 16px;
  object-fit: cover;
}

.cat-badge {
  position: absolute;
  bottom: 8px;
  left: 8px;
  background: rgba(124, 107, 196, 0.85);
  color: white;
  font-size: 9px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.popular-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  font-size: 16px;
}

.card-body { padding: 12px 12px 14px; }

.card-name {
  font-size: 13px;
  font-weight: 700;
  color: #1B1B1B;
  margin: 0 0 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.card-desc {
  font-size: 11px;
  color: #6B7280;
  margin: 0 0 10px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
}

.card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.card-rating {
  display: flex;
  align-items: center;
  gap: 2px;
  font-size: 11px;
  color: #F59E0B;
  font-weight: 600;
}

.card-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.card-price {
  font-size: 12px;
  font-weight: 700;
  color: #7C6BC4;
  margin: 0;
}

.add-btn {
  width: 30px;
  height: 30px;
  border-radius: 10px;
  background: #7C6BC4;
  border: none;
  color: white;
  font-size: 22px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 3px 8px rgba(124, 107, 196, 0.3);
}

.add-btn:hover { background: #5A4DA0; transform: scale(1.1); }
.add-btn:active { transform: scale(0.9); }

/* Skeleton */
.skeleton-card {
  height: 220px;
  background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 20px;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* Empty */
.empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 40px 0;
  color: #9CA3AF;
}

.empty-emoji { font-size: 48px; display: block; margin-bottom: 12px; }
.empty-state p { margin: 0 0 16px; font-size: 15px; }

.reset-btn {
  background: #7C6BC4;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}

/* Snack */
.snackbar {
  position: fixed;
  top: 24px;
  left: 50%;
  transform: translateX(-50%);
  background: #1B1B1B;
  color: white;
  padding: 12px 24px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 500;
  z-index: 999;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  white-space: nowrap;
}

.snack-enter-active, .snack-leave-active { transition: all 0.3s ease; }
.snack-enter-from, .snack-leave-to { opacity: 0; transform: translateX(-50%) translateY(-12px); }
</style>
