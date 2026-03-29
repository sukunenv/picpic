<template>
  <AppLayout>
    <div class="home-page">

      <!-- ── HEADER ─────────────────────────────────────────── -->
      <div class="home-header">
        <div class="greeting">
          <p class="greeting-sub">Selamat datang di</p>
          <div class="brand-row">
            <span class="brand-line-logo">
              <img src="/logo.png" class="w-8 h-8 rounded-full">
            </span>
            <h1 class="brand-name">Picpic</h1>
          </div>
        </div>
        <button class="notif-btn" aria-label="Notifikasi">
          <BellIcon class="h-6 w-6" />
        </button>
      </div>

      <!-- ── SEARCH ─────────────────────────────────────────── -->
      <div class="px-container">
        <div class="search-bar" @click="goToMenu">
          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
          <span class="search-placeholder">Cari menu favoritmu…</span>
        </div>
      </div>

      <!-- ── PROMO BANNER ───────────────────────────────────── -->
      <div class="px-container">
        <div class="promo-banner">
          <div class="promo-text">
            <span class="promo-badge">
              <GiftIcon class="h-3 w-3 mr-1 inline" /> Promo
            </span>
            <h2 class="promo-title">Gratis Minuman<br>untuk 10 Pembeli Pertama!</h2>
            <p class="promo-desc">Setiap hari jam 08.00 – 10.00</p>
          </div>
          <div class="promo-visual">
            <TicketIcon class="h-16 w-16 opacity-20 absolute -right-4 -bottom-4 text-white" />
            <GiftIcon class="h-16 w-16 text-white" />
          </div>
        </div>
      </div>

      <!-- ── KATEGORI ───────────────────────────────────────── -->
      <div class="section">
        <div class="section-header px-container">
          <h2 class="section-title">Kategori</h2>
        </div>
        <div class="scroll-row">
          <button
            v-for="cat in categories"
            :key="cat.id"
            class="category-pill"
            :class="{ active: activeCategory === cat.id }"
            @click="selectCategory(cat.id)"
          >
            {{ cat.label }}
          </button>
        </div>
      </div>

      <!-- ── PALING POPULER ─────────────────────────────────── -->
      <div class="section">
        <div class="section-header px-container">
          <h2 class="section-title">Populer</h2>
          <button class="see-all" @click="goToMenu">Lihat semua</button>
        </div>

        <div v-if="loadingMenu" class="scroll-row">
          <div v-for="n in 4" :key="n" class="pop-card skeleton-card"></div>
        </div>

        <div v-else class="scroll-row">
          <div
            v-for="item in popularItems"
            :key="item.id"
            class="pop-card"
            @click="openDetail(item)"
          >
            <div class="pop-img-wrap">
              <img :src="item.image" :alt="item.name" class="pop-img" />
            </div>
            <div class="pop-info">
              <p class="pop-name">{{ item.name }}</p>
              <div class="pop-rating">
                <StarIcon class="h-3 w-3 text-yellow-500 fill-current" />
                <span>{{ item.rating }}</span>
              </div>
              <p class="pop-price">{{ formatPrice(item.price) }}</p>
            </div>
            <button class="add-btn-round" @click.stop="addToCart(item)">
              <PlusIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>

      <!-- ── SEMUA MENU ─────────────────────────────────────── -->
      <div class="section">
        <div class="section-header px-container">
          <h2 class="section-title">
            {{ activeCategory === 'Semua' ? 'Semua Menu' : activeCategory }}
          </h2>
          <span class="item-count">{{ filteredItems.length }} item</span>
        </div>

        <div class="px-container menu-list">
          <div v-if="loadingMenu">
            <div v-for="n in 4" :key="n" class="list-card skeleton-list"></div>
          </div>

          <div
            v-for="item in filteredItems"
            :key="item.id"
            class="list-card"
          >
            <div class="list-img-wrap">
              <img :src="item.image" :alt="item.name" class="list-img" />
            </div>
            <div class="list-info">
              <p class="list-name">{{ item.name }}</p>
              <p class="list-desc">{{ item.description }}</p>
              <div class="list-bottom">
                <div class="list-rating">
                  <StarIcon class="h-3 w-3 text-yellow-500 fill-current" />
                  <span>{{ item.rating }}</span>
                </div>
                <p class="list-price">{{ formatPrice(item.price) }}</p>
              </div>
            </div>
            <button class="add-btn-square" @click="addToCart(item)">
              <PlusIcon class="h-6 w-6" />
            </button>
          </div>

          <p v-if="!loadingMenu && filteredItems.length === 0" class="empty-msg">
            Tidak ada menu di kategori ini...
          </p>
        </div>
      </div>

      <!-- bottom padding -->
      <div style="height: 16px;"></div>
    </div>

    <!-- Item Added Snackbar -->
    <transition name="snack">
      <div v-if="showSnack" class="snackbar">
        <CheckCircleIcon class="h-5 w-5 inline mr-1" /> {{ snackMsg }}
      </div>
    </transition>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCartStore } from '@/stores/cartStore';
import { 
  MagnifyingGlassIcon, 
  SparklesIcon, 
  BellIcon, 
  GiftIcon, 
  TicketIcon,
  StarIcon,
  PlusIcon,
  CheckCircleIcon,
  Squares2X2Icon,
  BeakerIcon,
  CakeIcon,
  VariableIcon as MugIcon,
  WrenchScrewdriverIcon as UtensilsIcon
} from '@heroicons/vue/24/outline';
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid';

const cartStore = useCartStore();
const allMenuItems = ref([]);
const loadingMenu = ref(true);
const activeCategory = ref('Semua');
const showSnack = ref(false);
const snackMsg = ref('');

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

const popularItems = computed(() =>
  allMenuItems.value.filter(i => i.is_popular).slice(0, 6)
);

const filteredItems = computed(() => {
  if (activeCategory.value === 'Semua') return allMenuItems.value;
  return allMenuItems.value.filter(i => i.category === activeCategory.value);
});

async function fetchMenu() {
  loadingMenu.value = true;
  try {
    const res = await fetch('/api/menu-items');
    const json = await res.json();
    allMenuItems.value = json.data;
  } catch (e) {
    console.error(e);
  } finally {
    loadingMenu.value = false;
  }
}

function selectCategory(cat) {
  activeCategory.value = cat;
}

function addToCart(item) {
  cartStore.addItem(item);
  snackMsg.value = `${item.name} ditambahkan!`;
  showSnack.value = true;
  setTimeout(() => { showSnack.value = false; }, 2000);
}

function openDetail(item) {
  // bisa extend ke modal atau halaman detail
  addToCart(item);
}

function goToMenu() {
  router.visit('/menu');
}

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}

onMounted(fetchMenu);
</script>

<style scoped>
.home-page {
  background: #F0EEFF;
  min-height: 100dvh;
}

/* Header */
.home-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 52px 20px 16px;
  background: #ffffff;
}

.greeting-sub {
  font-size: 13px;
  color: #6B7280;
  margin: 0 0 2px;
}

.brand-row {
  display: flex;
  align-items: center;
  gap: 6px;
}

.brand-logo { font-size: 24px; }

.brand-name {
  font-size: 28px;
  font-weight: 800;
  color: #7C6BC4;
  margin: 0;
  letter-spacing: -0.5px;
}

.notif-btn {
  background: #F3F4F6;
  border: none;
  width: 44px;
  height: 44px;
  border-radius: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #7C6BC4;
  margin-top: 8px;
  transition: background 0.2s;
}

.notif-btn:hover { background: #E8EAF6; }

/* Container */
.px-container { padding: 0 16px; }

/* Search */
.search-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  border-radius: 16px;
  padding: 13px 16px;
  margin: 16px 0;
  cursor: pointer;
  transition: background 0.2s;
  border: 1px solid #E5E7EB;
}

.search-bar:hover { background: #F9FAFB; }

.search-icon { color: #9CA3AF; }

.search-placeholder {
  color: #9CA3AF;
  font-size: 14px;
}

/* Promo Banner */
.promo-banner {
  background: linear-gradient(135deg, #7C6BC4 0%, #5A4DA0 100%);
  border-radius: 20px;
  padding: 22px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
  overflow: hidden;
  position: relative;
}

.promo-banner::before {
  content: '';
  position: absolute;
  width: 120px;
  height: 120px;
  background: rgba(255,255,255,0.05);
  border-radius: 50%;
  right: -30px;
  top: -30px;
}

.promo-badge {
  display: inline-block;
  background: #9B8FD4;
  color: white;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 20px;
  margin-bottom: 8px;
}

.promo-title {
  font-size: 17px;
  font-weight: 700;
  color: #ffffff;
  line-height: 1.3;
  margin: 0 0 6px;
}

.promo-desc {
  font-size: 12px;
  color: rgba(255,255,255,0.7);
  margin: 0;
}

.promo-visual {
  font-size: 64px;
  line-height: 1;
  filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
}

/* Section */
.section { margin-top: 24px; }

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.section-title {
  font-size: 17px;
  font-weight: 700;
  color: #1B1B1B;
  margin: 0;
}

.item-count {
  font-size: 13px;
  color: #6B7280;
  background: #ffffff;
  padding: 3px 10px;
  border-radius: 20px;
  border: 1px solid #E5E7EB;
}

.see-all {
  font-size: 13px;
  font-weight: 600;
  color: #7C6BC4;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 8px;
  transition: background 0.2s;
}

.see-all:hover { background: #E8EAF6; }

/* Category Pills */
.scroll-row {
  display: flex;
  gap: 10px;
  padding: 4px 16px 8px;
  overflow-x: auto;
  scrollbar-width: none;
}

.scroll-row::-webkit-scrollbar { display: none; }

.category-pill {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 50px;
  border: 1.5px solid #E5E7EB;
  background: #ffffff;
  font-size: 13px;
  font-weight: 500;
  color: #4B5563;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s ease;
  min-height: 38px;
}

.category-pill:hover {
  border-color: #9B8FD4;
  color: #7C6BC4;
}

.category-pill.active {
  background: #7C6BC4;
  border-color: #7C6BC4;
  color: #ffffff;
}

.cat-emoji { font-size: 15px; }

/* Popular Cards */
.pop-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 16px;
  width: 148px;
  flex-shrink: 0;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  cursor: pointer;
  position: relative;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.pop-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.pop-img-wrap {
  width: 80px;
  height: 80px;
  margin: 0 auto 12px;
}

.pop-img {
  width: 80px;
  height: 80px;
  border-radius: 50%; /* Bulat sempurna */
  object-fit: cover;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.pop-info { text-align: center; }

.pop-name {
  font-size: 13px;
  font-weight: 600;
  color: #1B1B1B;
  margin: 0 0 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.pop-rating {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 3px;
  font-size: 12px;
  color: #F59E0B;
  margin-bottom: 4px;
  font-weight: 600;
}

.star { font-size: 12px; }

.pop-price {
  font-size: 13px;
  font-weight: 700;
  color: #7C6BC4;
  margin: 0;
}

.add-btn-round {
  position: absolute;
  bottom: 12px;
  right: 12px;
  width: 32px;
  height: 32px;
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
  box-shadow: 0 3px 10px rgba(124, 107, 196, 0.35);
}

.add-btn-round:hover {
  background: #5A4DA0;
  transform: scale(1.1);
}

.add-btn-round:active { transform: scale(0.95); }

/* List Cards */
.menu-list { display: flex; flex-direction: column; gap: 12px; }

.list-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 14px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  transition: transform 0.2s ease;
}

.list-card:hover { transform: translateX(2px); }

.list-img-wrap {
  width: 64px;
  height: 64px;
  margin-right: 12px;
  flex-shrink: 0;
}

.list-img {
  width: 64px;
  height: 64px;
  border-radius: 16px; /* rounded-2xl approximate */
  object-fit: cover;
}

.list-info { flex: 1; min-width: 0; }

.list-name {
  font-size: 14px;
  font-weight: 600;
  color: #1B1B1B;
  margin: 0 0 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.list-desc {
  font-size: 12px;
  color: #6B7280;
  margin: 0 0 6px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.list-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.list-rating {
  display: flex;
  align-items: center;
  gap: 3px;
  font-size: 12px;
  color: #F59E0B;
  font-weight: 600;
}

.list-price {
  font-size: 14px;
  font-weight: 700;
  color: #7C6BC4;
  margin: 0;
}

.add-btn-square {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  background: #7C6BC4;
  border: none;
  color: white;
  font-size: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  flex-shrink: 0;
  transition: all 0.2s ease;
  box-shadow: 0 3px 10px rgba(124, 107, 196, 0.3);
}

.add-btn-square:hover { background: #5A4DA0; transform: scale(1.05); }
.add-btn-square:active { transform: scale(0.95); }

/* Skeleton */
.skeleton-card {
  width: 148px !important;
  height: 200px;
  background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 20px;
}

.skeleton-list {
  height: 92px;
  background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 16px;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.empty-msg {
  text-align: center;
  color: #9CA3AF;
  padding: 32px 0;
  font-size: 15px;
}

/* Snackbar */
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
.snack-enter-from { opacity: 0; transform: translateX(-50%) translateY(-12px); }
.snack-leave-to { opacity: 0; transform: translateX(-50%) translateY(-12px); }
</style>
