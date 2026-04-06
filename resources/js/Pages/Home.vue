<template>
  <AppLayout>
    <div class="home-page min-h-screen bg-gradient-to-b from-[#f3f5ff] via-[#f7f8ff] to-[#f9fafb] pb-28">
      
      <!-- ── HEADER & SEARCH ────────────────────────────────── -->
      <div class="relative pb-4">
        <div class="relative overflow-hidden bg-gradient-to-br from-primary to-[#5059df] px-5 sm:px-6 pt-12 pb-14 rounded-b-[34px] shadow-[0_16px_40px_rgba(99,103,255,0.34)]">
          <div class="absolute -right-10 -top-8 w-36 h-36 rounded-full bg-white/10"></div>
          <div class="absolute -left-10 -bottom-14 w-36 h-36 rounded-full bg-white/10"></div>

          <div class="relative flex justify-between items-center mb-3">
            <div>
              <p class="text-[10px] tracking-[0.14em] uppercase font-bold text-white/70 mb-1">Good day</p>
              <h1 class="app-page-title text-white">Selamat datang,</h1>
              <p class="text-white/85 text-sm font-medium mt-0.5">Pilih menu favoritmu hari ini.</p>
            </div>
            <div class="bg-white/95 rounded-full p-1 border-2 border-white/40 shadow-lg">
              <img src="/logo.png" class="w-10 h-10 rounded-full object-contain" alt="Logo">
            </div>
          </div>

          <div class="relative flex gap-2 mb-4">
            <div class="px-3 py-1.5 text-[11px] font-bold rounded-full bg-white/15 text-white border border-white/20">Fast Delivery</div>
            <div class="px-3 py-1.5 text-[11px] font-bold rounded-full bg-white/15 text-white border border-white/20">Fresh Brew</div>
          </div>

          <div class="relative" @click="goToMenu">
            <div class="bg-white/95 rounded-2xl shadow-xl flex items-center px-4 py-3.5 gap-3 cursor-pointer border border-white/40 transition-transform active:scale-[0.98]">
              <MagnifyingGlassIcon class="text-primary/60 w-5 h-5" />
              <span class="flex-1 outline-none text-sm text-primary/60 font-medium">Cari minuman atau snack...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── PROMO BANNER ──────────────────────────────────────── -->
      <div class="px-5 sm:px-6 mt-4 relative overflow-hidden h-56 sm:h-60 group">
        <!-- Loading State -->
        <div v-if="loadingBanners" class="w-full h-full bg-gray-200 animate-pulse rounded-2xl md:rounded-3xl"></div>

        <!-- Banner Content -->
        <div v-else class="w-full h-full relative">
          <transition-group name="slide-fade">
            <div v-for="(banner, index) in banners" 
                 :key="banner.id"
                 v-show="currentBanner === index"
                 class="absolute inset-0 w-full h-full rounded-3xl overflow-hidden shadow-[0_18px_42px_rgba(15,23,42,0.18)]"
            >
              <!-- Background Image / Gradient Fallback -->
              <div v-if="banner.image" class="absolute inset-0">
                <img :src="banner.image" 
                     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" 
                     :alt="banner.title"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/78 via-black/22 to-transparent"></div>
              </div>
              <div v-else class="absolute inset-0 bg-gradient-to-br from-primary via-[#7C6BC4] to-[#4B3FA0]"></div>

              <!-- Content Overlay -->
              <div class="absolute inset-0 flex flex-col justify-end p-6 pb-8">
                <h2 class="text-white text-[22px] sm:text-3xl font-black mb-1 drop-shadow-md leading-tight">{{ banner.title }}</h2>
                <p class="text-white/90 text-xs sm:text-sm font-medium mb-4 line-clamp-2 drop-shadow-md max-w-[88%]">{{ banner.description }}</p>
              <div v-if="banner.button_text"
                     class="self-start px-5 py-2.5 bg-white text-primary rounded-xl font-bold text-[11px] shadow-md shadow-black/10 active:scale-95 transition-transform"
                     @click="handleBannerClick(banner.button_url)"
                >
                  {{ banner.button_text }}
                </div>
              </div>
            </div>

            <!-- Empty State / Default Fallback -->
            <div v-if="banners.length === 0" key="fallback" class="w-full h-full bg-gradient-to-br from-primary via-[#7C6BC4] to-[#4B3FA0] rounded-2xl flex flex-col items-center justify-center p-6 text-center">
              <div class="bg-white/20 p-4 rounded-full mb-4 backdrop-blur-md ring-8 ring-white/5">
                <img src="/logo.png" class="w-16 h-16 object-contain" alt="Picpic">
              </div>
              <p class="text-white/60 text-[10px] font-bold tracking-[0.2em] uppercase mb-1">Stay tuned</p>
              <h2 class="text-white text-xl font-bold">Kumpul dan bercerita</h2>
            </div>
          </transition-group>

          <!-- Dots Indicator -->
          <div v-if="banners.length > 1" class="absolute bottom-4 left-0 right-0 flex justify-center gap-1.5 z-10">
            <button v-for="(_, index) in banners" 
                    :key="index"
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="currentBanner === index ? 'w-6 bg-white' : 'w-1.5 bg-white/40'"
                    @click="setBanner(index)"
            ></button>
          </div>
        </div>
      </div>

      <!-- ── FEATURED SHOWCASE ───────────────────────────────── -->
      <div v-if="featuredItem" class="px-5 sm:px-6 mt-6">
        <div class="app-card-lg app-pressable p-4 sm:p-5 shadow-[0_14px_35px_rgba(99,103,255,0.12)]">
          <div class="flex items-start justify-between mb-3">
            <div>
              <p class="text-[10px] tracking-[0.18em] uppercase text-primary/50 font-bold">Pilihan Hari Ini</p>
              <h3 class="app-section-title text-primary mt-1">{{ featuredItem.name }}</h3>
            </div>
            <span class="bg-primary/10 text-primary text-[11px] font-bold px-3 py-1 rounded-full">
              {{ formatPrice(featuredItem.price) }}
            </span>
          </div>

          <div class="bg-gradient-to-br from-[#f2f4ff] to-[#eef1ff] rounded-2xl p-4 flex items-center gap-4 relative overflow-hidden border border-white">
            <div class="absolute -right-10 -top-10 w-28 h-28 rounded-full bg-primary/10"></div>
            <img
              :src="featuredItem.image"
              :alt="featuredItem.name"
              class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-2xl shadow-md shadow-primary/10 app-pressable"
            />
            <div class="flex-1 min-w-0">
              <p class="text-xs text-gray-500 leading-relaxed line-clamp-2 mb-3">
                {{ featuredItem.description || 'Rasa seimbang dengan aroma khas, cocok untuk menemani aktivitasmu.' }}
              </p>
              <div class="flex items-center gap-2">
                <button class="app-btn-primary px-4 py-2 text-xs shadow-lg shadow-primary/30" @click.stop="addToCart(featuredItem)">
                  Order Sekarang
                </button>
                <button class="bg-white border border-gray-200 text-primary px-3 py-2 rounded-xl text-xs font-semibold" @click.stop="goToMenu">
                  Lihat Menu
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── KATEGORI ───────────────────────────────────────── -->
      <div class="mt-10">
        <div class="px-5 sm:px-6 mb-4 flex items-center justify-between">
          <h3 class="app-section-title text-primary">Kategori Menu</h3>
          <button class="text-accent font-bold text-xs">Lihat Semua</button>
        </div>
        <div class="flex gap-3 overflow-x-auto px-5 sm:px-6 hide-scrollbar">
          <div v-for="cat in categories" :key="cat.id" class="flex flex-col items-center gap-2 flex-shrink-0" @click="selectCategory(cat.id)">
            <div 
              class="w-16 h-16 rounded-2xl flex items-center justify-center p-4 transition duration-300 border"
              :class="activeCategory === cat.id ? 'bg-primary shadow-xl shadow-primary/30 scale-105 border-primary' : 'bg-white shadow-md border-gray-100'"
            >
              <component :is="cat.icon" class="w-full h-full" :class="activeCategory === cat.id ? 'text-white' : 'text-primary/80'" />
            </div>
            <span class="text-[11px] font-bold" :class="activeCategory === cat.id ? 'text-primary' : 'text-gray-400'">{{ cat.label }}</span>
          </div>
        </div>
      </div>

      <!-- ── POPULER ─────────────────────────────────── -->
      <div class="mt-10 mb-7">
        <div class="px-5 sm:px-6 mb-4 flex items-center justify-between">
          <h3 class="app-section-title text-primary">Menu Terlaris</h3>
        </div>
        <div class="flex gap-4 overflow-x-auto px-5 sm:px-6 hide-scrollbar pb-4 -mx-1">
          <div v-for="item in popularItems" :key="item.id" class="popular-card app-card flex-shrink-0 p-4 rounded-2xl md:rounded-3xl w-40 sm:w-44 relative group" @click="addToCart(item)">
            <div class="relative mb-4">
              <img :src="item.image" :alt="item.name" class="w-28 h-28 sm:w-32 sm:h-32 mx-auto object-cover rounded-2xl md:rounded-3xl group-hover:scale-105 transition duration-500">
              <div class="absolute -top-1 -right-1 bg-accent p-2 rounded-xl shadow-md transform rotate-12">
                <StarIcon class="h-3 w-3 text-white fill-current" />
              </div>
            </div>
            <div class="space-y-1">
              <h4 class="text-primary font-bold text-sm truncate">{{ item.name }}</h4>
              <p class="text-primary font-bold text-xs">{{ formatPrice(item.price) }}</p>
            </div>
            <button class="absolute bottom-4 right-4 bg-accent w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-accent/20 group-hover:scale-110 transition duration-300">
              <PlusIcon class="h-5 w-5 text-white" />
            </button>
          </div>
          <!-- Right spacer padding fix -->
          <div class="w-2 flex-shrink-0"></div>
        </div>
      </div>

      <!-- ── SEMUA MENU ─────────────────────────────────────── -->
      <div class="px-5 sm:px-6 mt-4">
        <h3 class="app-section-title text-primary mb-4">Semua Menu</h3>
        <div class="space-y-3">
          <div v-for="item in filteredItems.slice(0, 4)" :key="item.id" class="app-card flex p-3.5 rounded-2xl gap-4 items-center" @click="addToCart(item)">
            <img :src="item.image" :alt="item.name" class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
            <div class="flex-1 min-w-0 py-1">
              <h4 class="text-primary font-bold text-sm truncate mb-1">{{ item.name }}</h4>
              <p class="text-gray-400 text-[10px] line-clamp-1 mb-2">{{ item.description }}</p>
              <div class="flex items-center gap-2">
                 <span class="text-[#6367FF] font-semibold text-sm">{{ formatPrice(item.price) }}</span>
                 <span class="bg-[#F8F9FA] text-[#F59E0B] text-[9px] font-bold px-1.5 py-0.5 rounded-full flex items-center gap-0.5">
                   <StarIconSolid class="h-2 w-2" /> {{ item.rating }}
                 </span>
              </div>
            </div>
            <button class="w-9 h-9 flex-shrink-0 rounded-xl bg-accent flex items-center justify-center shadow-md shadow-accent/20 active:scale-90 transition-transform">
              <PlusIcon class="h-5 w-5 text-white" />
            </button>
          </div>
          
          <button @click="goToMenu" class="w-full bg-primary/10 text-primary font-bold text-[13px] py-4 rounded-xl mt-4 flex items-center justify-center gap-2 active:scale-[0.98] transition-all">
            Lihat Semua Menu <ArrowRightIcon class="h-4 w-4 stroke-2" />
          </button>
        </div>
      </div>

      <!-- Snackbar -->
      <transition name="snack">
        <div v-if="showSnack" class="snackbar-modern">
          <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-lg bg-accent/20 flex items-center justify-center">
                <CheckCircleIcon class="h-5 w-5 text-accent" />
             </div>
             <div>
               <p class="text-white text-xs font-bold">{{ snackMsg }}</p>
               <button @click="goToCart" class="text-accent text-[10px] font-bold uppercase tracking-wider">Lihat Keranjang</button>
             </div>
          </div>
        </div>
      </transition>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCartStore } from '@/stores/cartStore';
import { 
  MagnifyingGlassIcon, 
  StarIcon, 
  PlusIcon,
  CheckCircleIcon,
  Squares2X2Icon,
  BeakerIcon,
  CakeIcon,
  ArrowRightIcon,
  LifebuoyIcon as MugIcon,
  HandThumbUpIcon as UtensilsIcon
} from '@heroicons/vue/24/outline';
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid';

const cartStore = useCartStore();
const allMenuItems = ref([]);
const loadingMenu = ref(true);
const activeCategory = ref('Semua');
const showSnack = ref(false);
const snackMsg = ref('');

// ── BANNER LOGIC ──────────────────────────────────────
const banners = ref([]);
const loadingBanners = ref(true);
const currentBanner = ref(0);
let bannerTimer = null;

const fetchBanners = async () => {
  try {
    const response = await fetch('/api/banners');
    const result = await response.json();
    banners.value = result.data;
    if (banners.value.length > 1) {
      startBannerTimer();
    }
  } catch (e) {
    console.error('Error loading banners', e);
  } finally {
    loadingBanners.value = false;
  }
};

const startBannerTimer = () => {
  if (bannerTimer) clearInterval(bannerTimer);
  bannerTimer = setInterval(() => {
    currentBanner.value = (currentBanner.value + 1) % banners.value.length;
  }, 4000);
};

const setBanner = (index) => {
  currentBanner.value = index;
  startBannerTimer(); // Reset timer on manual interaction
};

const handleBannerClick = (url) => {
  if (!url) return;
  if (url.startsWith('http')) {
    window.location.href = url;
  } else {
    router.visit(url);
  }
};

const categories = [
  { id: 'Semua',    label: 'Semua',      icon: Squares2X2Icon },
  { id: 'Kopi',     label: 'Kopi',       icon: MugIcon },
  { id: 'Non-Kopi', label: 'Non-Kopi',   icon: BeakerIcon },
  { id: 'Makanan',  label: 'Makanan',    icon: UtensilsIcon },
  { id: 'Dessert',  label: 'Dessert',    icon: CakeIcon },
];

const popularItems = computed(() =>
  allMenuItems.value.filter(i => i.is_popular).slice(0, 6)
);

const featuredItem = computed(() => {
  if (popularItems.value.length > 0) return popularItems.value[0];
  return allMenuItems.value[0] || null;
});

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
  snackMsg.value = `${item.name} Berhasil Ditambah!`;
  showSnack.value = true;
  // Trigger bounce on bottom nav automatically via AppLayout watching store
  setTimeout(() => { showSnack.value = false; }, 3000);
}

function goToMenu() {
  router.visit('/menu');
}

function goToCart() {
  router.visit('/cart');
}

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}

onMounted(() => {
  fetchBanners();
  fetchMenu();
});

onUnmounted(() => {
  if (bannerTimer) clearInterval(bannerTimer);
});
</script>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-fade-enter-from {
  opacity: 0;
  transform: translateX(40px);
}
.slide-fade-leave-to {
  opacity: 0;
  transform: translateX(-40px);
}

.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.snackbar-modern {
  position: fixed;
  top: 24px;
  left: 50%;
  transform: translateX(-50%);
  background: #1B1B1B;
  padding: 12px 16px;
  border-radius: 20px;
  width: calc(100% - 48px);
  max-width: 350px;
  z-index: 1000;
  box-shadow: 0 15px 30px rgba(0,0,0,0.3);
}

.snack-enter-active, .snack-leave-active { transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.snack-enter-from { opacity: 0; transform: translateX(-50%) translateY(-30px); }
.snack-leave-to { opacity: 0; transform: translateX(-50%) translateY(-30px) scale(0.9); }
</style>
