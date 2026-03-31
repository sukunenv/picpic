<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 pb-28">

      <!-- Header -->
      <div class="bg-primary px-6 h-56 rounded-b-[32px] text-center relative overflow-hidden flex flex-col items-center justify-center pt-6">
        <!-- Decorative circles -->
        <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/10 blur-md rounded-full"></div>
        <div class="absolute -bottom-4 -left-8 w-24 h-24 bg-white/10 blur-md rounded-full"></div>
        
        <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3 ring-4 ring-white/10 relative z-10 shadow-lg">
          <UserCircleIcon class="h-14 w-14 text-white/90" />
        </div>
        <h1 class="text-white text-xl font-bold mb-1 relative z-10">Pelanggan Setia</h1>
        <p class="text-accent text-xs font-bold uppercase tracking-wider relative z-10">✦ Picpic Member ✦</p>
      </div>

      <!-- Stats (Floating Card Overlap) -->
      <div class="mx-6 -mt-10 bg-white rounded-2xl shadow-md p-5 relative z-10 mb-6">
        <div class="flex items-center justify-between">
          <!-- Pesanan -->
          <div class="flex-1 flex flex-col items-center">
            <ClipboardDocumentCheckIcon class="h-6 w-6 text-purple-500 mb-2" />
            <p class="text-primary text-xl font-black mb-0.5">{{ animatedOrder }}</p>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Pesanan</p>
          </div>
          
          <div class="w-px h-12 bg-gray-100"></div>
          
          <!-- Rating -->
          <div class="flex-1 flex flex-col items-center">
            <StarIcon class="h-6 w-6 text-yellow-500 mb-2" />
            <p class="text-primary text-xl font-black mb-0.5">{{ animatedRating }}</p>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Rating</p>
          </div>
          
          <div class="w-px h-12 bg-gray-100"></div>
          
          <!-- Favorit -->
          <div class="flex-1 flex flex-col items-center">
            <BeakerIcon class="h-6 w-6 text-blue-500 mb-2" />
            <p class="text-primary text-xl font-black mb-0.5">Kopi</p>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Favorit</p>
          </div>
        </div>
      </div>

      <!-- Menu Sections -->
      <div class="px-6 space-y-4">
        <!-- Settings -->
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100">
          <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider px-5 pt-4 pb-2">Pengaturan</h3>
          <div
            v-for="item in settingItems"
            :key="item.label"
            class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 last:border-0 cursor-pointer hover:bg-gray-50 transition-colors active:bg-gray-100"
          >
            <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center">
              <component :is="item.icon" class="h-4 w-4 text-primary" />
            </div>
            <span class="flex-1 text-primary text-sm font-medium">{{ item.label }}</span>
            <ChevronRightIcon class="h-4 w-4 text-gray-300" />
          </div>
        </div>

        <!-- Information -->
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100">
          <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider px-5 pt-4 pb-2">Informasi</h3>
          <div
            v-for="item in infoItems"
            :key="item.label"
            class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 last:border-0 cursor-pointer hover:bg-gray-50 transition-colors active:bg-gray-100"
          >
            <div class="w-9 h-9 rounded-xl bg-accent/10 flex items-center justify-center">
              <component :is="item.icon" class="h-4 w-4 text-accent" />
            </div>
            <span class="flex-1 text-primary text-sm font-medium">{{ item.label }}</span>
            <ChevronRightIcon class="h-4 w-4 text-gray-300" />
          </div>
        </div>
      </div>

      <!-- App Footer -->
      <div class="text-center py-10 px-6">
        <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
          <SparklesIcon class="h-6 w-6 text-primary" />
        </div>
        <p class="text-primary font-bold text-sm">Picpic</p>
        <p class="text-gray-400 text-[11px] mt-1">v1.0.0 — Made with <HeartIcon class="h-3 w-3 inline text-red-400 fill-current" /> in Indonesia</p>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
  UserCircleIcon,
  ClipboardDocumentCheckIcon,
  StarIcon,
  BeakerIcon,
  BellIcon,
  MoonIcon,
  GlobeAltIcon,
  BookOpenIcon,
  ShieldCheckIcon,
  PhoneIcon,
  ChevronRightIcon,
  SparklesIcon,
  HeartIcon
} from '@heroicons/vue/24/outline';

const settingItems = [
  { icon: BellIcon, label: 'Notifikasi' },
  { icon: MoonIcon, label: 'Mode Gelap' },
  { icon: GlobeAltIcon, label: 'Bahasa' },
];

const infoItems = [
  { icon: BookOpenIcon, label: 'Syarat & Ketentuan' },
  { icon: ShieldCheckIcon, label: 'Kebijakan Privasi' },
  { icon: PhoneIcon, label: 'Hubungi Kami' },
  { icon: StarIcon, label: 'Beri Rating Aplikasi' },
];

const animatedOrder = ref(0);
const animatedRating = ref(0.0);

onMounted(() => {
  let orderTarget = 12;
  let o = 0;
  let orderInterval = setInterval(() => {
    if (o < orderTarget) {
      o += 1;
      animatedOrder.value = o;
    } else clearInterval(orderInterval);
  }, 50);

  let ratingTarget = 4.9;
  let r = 0.0;
  let ratingInterval = setInterval(() => {
    if (r < ratingTarget) {
      r += 0.2;
      animatedRating.value = r > ratingTarget ? ratingTarget.toFixed(1) : r.toFixed(1);
    } else clearInterval(ratingInterval);
  }, 30);
});
</script>
