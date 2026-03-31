<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 pb-28">

      <!-- Header -->
      <div class="bg-primary px-6 pt-12 pb-8 rounded-b-[32px]">
        <h1 class="text-white text-2xl font-bold mb-1">Pesanan Saya</h1>
        <p class="text-white/60 text-sm font-medium">Pantau status pesanan kopimu</p>
      </div>

      <!-- Tabs -->
      <div class="flex gap-3 px-6 mt-6 mb-6">
        <button 
          v-for="t in ['Aktif', 'Selesai']" 
          :key="t" 
          class="flex-1 py-3 rounded-2xl text-sm font-bold transition-all duration-300 border"
          :class="activeTab === t 
            ? 'bg-primary border-primary text-white shadow-lg shadow-primary/20' 
            : 'bg-white border-gray-100 text-gray-400'"
          @click="activeTab = t"
        >
          {{ t }}
        </button>
      </div>

      <!-- Empty State -->
      <div class="px-6">
        <div class="flex flex-col items-center justify-center py-20 text-center">
          <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center shadow-xl shadow-black/5 mb-6">
            <TicketIcon class="h-14 w-14 text-gray-200" />
          </div>
          <h2 class="text-primary text-xl font-bold mb-2">Belum Ada Pesanan</h2>
          <p class="text-gray-400 text-sm mb-8 leading-relaxed px-8">
            Sepertinya kamu belum memesan apa-apa.<br>Yuk, coba minuman spesial kami!
          </p>
          <button 
            class="bg-primary text-white font-bold px-8 py-4 rounded-full shadow-lg shadow-primary/30 w-full max-w-xs transition-transform active:scale-95"
            @click="goToMenu"
          >
            Jelajahi Menu
          </button>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { TicketIcon } from '@heroicons/vue/24/outline';

const activeTab = ref('Aktif');

function goToMenu() {
  router.visit('/menu');
}
</script>
