<template>
  <AppLayout>
    <div class="menu-page min-h-screen bg-gray-50 pb-28">

      <!-- ── HEADER ───────────────────────────────────────── -->
      <div class="sticky top-0 z-40 bg-primary px-6 pt-12 pb-6 rounded-b-[32px] shadow-lg">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-white text-2xl font-bold">Pilih Menu</h1>
          <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
            <AdjustmentsHorizontalIcon class="h-6 w-6 text-white" />
          </div>
        </div>
        <div class="relative group">
          <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-white/50 group-focus-within:text-accent transition-colors" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari kopi, teh, atau snack..."
            class="w-full bg-white/10 border-none rounded-2xl py-3.5 pl-12 pr-4 text-white placeholder:text-white/40 focus:ring-2 focus:ring-accent transition-all text-sm font-medium"
            @input="debouncedFetch"
          />
          <button v-if="searchQuery" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/50 hover:text-white" @click="clearSearch">
            <XMarkIcon class="h-4 w-4" />
          </button>
        </div>
      </div>

      <!-- ── KATEGORI ───────────────────────────────────────── -->
      <div class="mt-6 mb-4">
        <div class="flex gap-3 overflow-x-auto px-6 hide-scrollbar">
          <button
            v-for="cat in categories"
            :key="cat.id"
            class="px-5 py-2.5 rounded-full text-xs font-bold whitespace-nowrap transition-all duration-300 border"
            :class="activeCategory === cat.id 
              ? 'bg-primary border-primary text-white shadow-lg shadow-primary/20 scale-105' 
              : 'bg-white border-gray-100 text-gray-400'"
            @click="setCategory(cat.id)"
          >
            {{ cat.label }}
          </button>
        </div>
      </div>

      <!-- Result count -->
      <div class="px-6 mb-4 flex items-center justify-between">
        <p class="text-gray-400 text-[11px] font-bold uppercase tracking-wider">
          <span v-if="!loading">{{ filteredItems.length }} Menu Tersedia</span>
          <span v-else>Memperbarui daftar...</span>
        </p>
      </div>

      <!-- ── GRID MENU ──────────────────────────────────────── -->
      <div class="px-6 grid grid-cols-2 gap-4">

        <!-- Skeleton loading -->
        <template v-if="loading">
          <div v-for="n in 6" :key="n" class="aspect-[3/4] bg-white rounded-3xl animate-pulse"></div>
        </template>

        <template v-else>
          <div
            v-for="item in filteredItems"
            :key="item.id"
            class="menu-card bg-white rounded-[28px] overflow-hidden shadow-card-menu hover:shadow-lg transition-all duration-500 flex flex-col group border-none"
            @click="addToCart(item)"
          >
            <div class="relative aspect-square overflow-hidden">
               <img :src="item.image" :alt="item.name" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" />
               <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/20 to-transparent"></div>
               <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-primary text-[9px] font-black px-2 py-0.5 rounded-lg uppercase tracking-tight">
                 {{ item.category }}
               </span>
               <div v-if="item.is_popular" class="absolute top-2 right-2 bg-pink-light p-1.5 rounded-lg shadow-md">
                 <SparklesIcon class="h-3 w-3 text-primary" />
               </div>
            </div>
            
            <div class="p-4 flex-1 flex flex-col justify-between relative">
              <div>
                <h3 class="text-primary font-bold text-[13px] leading-tight mb-1 truncate">{{ item.name }}</h3>
                <p class="text-gray-400 text-[10px] line-clamp-1 mb-3">{{ item.description }}</p>
              </div>
              
              <div class="flex items-center justify-between">
                <div>
                  <div class="flex items-center gap-1 mb-0.5">
                    <StarIconSolid class="h-2.5 w-2.5 text-accent" />
                    <span class="text-[10px] font-bold text-primary">{{ item.rating }}</span>
                  </div>
                  <p class="text-primary font-black text-sm">{{ formatPrice(item.price) }}</p>
                </div>
                <button class="bg-accent w-9 h-9 rounded-2xl flex items-center justify-center shadow-lg shadow-accent/20 group-hover:rotate-90 transition-transform duration-300">
                  <PlusIcon class="h-5 w-5 text-white" />
                </button>
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="filteredItems.length === 0" class="col-span-2 py-20 text-center">
            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-black/5">
               <MagnifyingGlassIcon class="h-10 w-10 text-gray-200" />
            </div>
            <h3 class="text-primary font-bold text-lg">Menu tidak ditemukan</h3>
            <p class="text-gray-400 text-sm mt-1 px-10">Coba gunakan kata kunci lain atau reset filter kategori.</p>
            <button class="mt-6 bg-primary text-white px-8 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-primary/30" @click="resetFilter">Reset Filter</button>
          </div>
        </template>
      </div>

    </div>

    <!-- Snack -->
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
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCartStore } from '@/stores/cartStore';
import { 
  MagnifyingGlassIcon, 
  StarIcon, 
  PlusIcon,
  CheckCircleIcon,
  XMarkIcon,
  AdjustmentsHorizontalIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline';
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid';

const cartStore = useCartStore();
const allItems = ref([]);
const loading = ref(true);
const activeCategory = ref('Semua');
const searchQuery = ref('');
const showSnack = ref(false);
const snackMsg = ref('');
let debounceTimer = null;

const categories = [
  { id: 'Semua',    label: 'Semua Menu' },
  { id: 'Kopi',     label: 'Kopi Espresso' },
  { id: 'Non-Kopi', label: 'Minuman Segar' },
  { id: 'Makanan',  label: 'Kue & Pastry' },
  { id: 'Dessert',  label: 'Menu Manis' },
];

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
  snackMsg.value = `${item.name} Berhasil Ditambah!`;
  showSnack.value = true;
  setTimeout(() => { showSnack.value = false; }, 3000);
}

function goToCart() {
  router.visit('/cart');
}

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}

onMounted(fetchMenu);
</script>

<style scoped>
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
