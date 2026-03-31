<template>
  <AppLayout>
    <div class="home-page min-h-screen bg-gray-50 pb-28">
      
      <!-- ── HEADER & SEARCH ────────────────────────────────── -->
      <div class="relative pb-6">
        <!-- Header ungu dengan curve bawah -->
        <div class="bg-primary px-6 pt-12 pb-16 rounded-b-[40px]">
          
          <!-- Greeting -->
          <div class="flex justify-between items-center mb-2">
            <div>
              <h1 class="text-white text-xl font-semibold leading-tight">
                Selamat datang,
              </h1>
              <p class="text-white/80 text-sm font-medium mt-0.5">
                Mau ngopi apa hari ini? ☕
              </p>
            </div>
            <div class="rounded-full bg-white p-1 border-2 border-white/30 shadow-md">
              <img src="/logo.png" class="w-10 h-10 rounded-full object-contain">
            </div>
          </div>

          <!-- Search bar overlap (push down) -->
          <div class="translate-y-10" @click="goToMenu">
            <div class="bg-white rounded-2xl shadow-xl flex items-center px-4 py-4 gap-3 cursor-pointer border-gray-100 border transition-transform active:scale-[0.98]">
              <MagnifyingGlassIcon class="text-primary/60 w-5 h-5"/>
              <span class="flex-1 outline-none text-sm text-primary/60 font-medium">Cari minuman atau snack...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── PROMO BANNER ──────────────────────────────────────── -->
      <div class="px-6 mt-2 relative overflow-hidden h-52 sm:h-60 group">
        <!-- Loading State -->
        <div v-if="loadingBanners" class="w-full h-full bg-gray-200 animate-pulse rounded-[32px]"></div>

        <!-- Banner Content -->
        <div v-else class="w-full h-full relative">
          <transition-group name="slide-fade">
            <div v-for="(banner, index) in banners" 
                 :key="banner.id"
                 v-show="currentBanner === index"
                 class="absolute inset-0 w-full h-full rounded-[32px] overflow-hidden shadow-xl shadow-primary/10"
            >
              <!-- Background Image / Gradient Fallback -->
              <div v-if="banner.image" class="absolute inset-0">
                <img :src="banner.image" 
                     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" 
                     :alt="banner.title"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
              </div>
              <div v-else class="absolute inset-0 bg-gradient-to-br from-primary via-[#7C6BC4] to-[#4B3FA0]"></div>

              <!-- Content Overlay -->
              <div class="absolute inset-0 flex flex-col justify-end p-6 pb-8">
                <h2 class="text-white text-xl sm:text-2xl font-black mb-1 drop-shadow-md leading-tight">{{ banner.title }}</h2>
                <p class="text-white/90 text-[11px] sm:text-xs font-medium mb-4 line-clamp-2 drop-shadow-md max-w-[80%]">{{ banner.description }}</p>
                <div v-if="banner.button_text" 
                     class="self-start px-5 py-2.5 bg-white text-primary rounded-xl font-bold text-[11px] shadow-lg shadow-black/10 active:scale-95 transition-transform"
                     @click="handleBannerClick(banner.button_url)"
                >
                  {{ banner.button_text }}
                </div>
              </div>
            </div>

            <!-- Empty State / Default Fallback -->
            <div v-if="banners.length === 0" key="fallback" class="w-full h-full bg-gradient-to-br from-primary via-[#7C6BC4] to-[#4B3FA0] rounded-[32px] flex flex-col items-center justify-center p-6 text-center">
              <div class="bg-white/20 p-4 rounded-full mb-4 backdrop-blur-md ring-8 ring-white/5">
                <img src="/logo.png" class="w-16 h-16 object-contain" alt="Picpic">
              </div>
              <p class="text-white/60 text-[10px] font-bold tracking-[0.2em] uppercase mb-1">Stay tuned</p>
              <h2 class="text-white text-xl font-bold">kumpul mencerita ☕</h2>
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

      <!-- ── KATEGORI ───────────────────────────────────────── -->
      <div class="mt-10">
        <div class="px-6 mb-4 flex items-center justify-between">
          <h3 class="text-primary font-semibold text-base">Kategori Kopi</h3>
          <button class="text-accent font-bold text-xs">Lihat Semua</button>
        </div>
        <div class="flex gap-4 overflow-x-auto px-6 hide-scrollbar">
          <div v-for="cat in categories" :key="cat.id" class="flex flex-col items-center gap-2 flex-shrink-0" @click="selectCategory(cat.id)">
            <div 
              class="w-16 h-16 rounded-full flex items-center justify-center p-4 transition duration-300"
              :class="activeCategory === cat.id ? 'bg-primary shadow-lg shadow-primary/30 scale-110' : 'bg-white shadow-sm'"
            >
              <component :is="cat.icon" class="w-full h-full" :class="activeCategory === cat.id ? 'text-white' : 'text-primary/80'" />
            </div>
            <span class="text-[11px] font-bold" :class="activeCategory === cat.id ? 'text-primary' : 'text-gray-400'">{{ cat.label }}</span>
          </div>
        </div>
      </div>

      <!-- ── POPULER ─────────────────────────────────── -->
      <div class="mt-10 mb-8">
        <div class="px-6 mb-4 flex items-center justify-between">
          <h3 class="text-primary font-semibold text-base">Menu Terlaris 🔥</h3>
        </div>
        <div class="flex gap-4 overflow-x-auto px-6 hide-scrollbar pb-4 -mx-1">
          <div v-for="item in popularItems" :key="item.id" class="popular-card flex-shrink-0 bg-white p-4 rounded-3xl w-40 sm:w-44 shadow-card-menu relative group" @click="addToCart(item)">
            <div class="relative mb-4">
              <img :src="item.image" :alt="item.name" class="w-28 h-28 sm:w-32 sm:h-32 mx-auto object-cover rounded-2xl group-hover:scale-105 transition duration-500">
              <div class="absolute -top-1 -right-1 bg-accent p-2 rounded-xl shadow-md transform rotate-12">
                <StarIcon class="h-3 w-3 text-white fill-current" />
              </div>
            </div>
            <div class="space-y-1">
              <h4 class="text-primary font-bold text-sm truncate">{{ item.name }}</h4>
              <p class="text-primary font-bold text-xs">{{ formatPrice(item.price) }}</p>
            </div>
            <button class="absolute bottom-4 right-4 bg-accent w-8 h-8 rounded-xl flex items-center justify-center shadow-lg shadow-accent/20 group-hover:scale-110 transition duration-300">
              <PlusIcon class="h-5 w-5 text-white" />
            </button>
          </div>
          <!-- Right spacer padding fix -->
          <div class="w-2 flex-shrink-0"></div>
        </div>
      </div>

      <!-- ── SEMUA MENU ─────────────────────────────────────── -->
      <div class="px-6 mt-4">
        <h3 class="text-primary font-semibold text-base mb-4">Semua Menu</h3>
        <div class="space-y-4">
          <div v-for="item in filteredItems.slice(0, 4)" :key="item.id" class="flex bg-white p-3 rounded-2xl shadow-card-menu border-none gap-4 items-center" @click="addToCart(item)">
            <img :src="item.image" :alt="item.name" class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
            <div class="flex-1 min-w-0 py-1">
              <h4 class="text-primary font-bold text-sm truncate mb-0.5">{{ item.name }}</h4>
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
          
          <button @click="goToMenu" class="w-full bg-primary/10 text-primary font-bold text-[13px] py-4 rounded-2xl mt-4 flex items-center justify-center gap-2 active:scale-[0.98] transition-all">
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
