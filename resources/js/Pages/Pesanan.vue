<template>
  <AppLayout>
    <div class="orders-page">
      <!-- Header -->
      <div class="orders-header">
        <h1 class="orders-title">Pesanan Saya</h1>
        <p class="orders-subtitle">Pantau status pesanan kopimu di sini</p>
      </div>

      <!-- Tabs (Optional: Aktif vs Selesai) -->
      <div class="orders-tabs px-container">
        <button 
          v-for="t in ['Aktif', 'Selesai']" 
          :key="t" 
          class="tab-btn" 
          :class="{ active: activeTab === t }"
          @click="activeTab = t"
        >
          {{ t }}
        </button>
      </div>

      <!-- Empty State / Placeholder -->
      <div class="orders-list px-container">
        <div class="empty-state">
          <div class="empty-illustration">
            <ClipboardDocumentListIcon class="h-20 w-20 text-gray-300 mx-auto" />
          </div>
          <h2 class="empty-title">Belum ada pesanan aktif</h2>
          <p class="empty-desc">Sepertinya kamu belum memesan apa-apa. Mari jelajahi menu kami!</p>
          <button class="menu-btn" @click="goToMenu">Jelajahi Menu</button>
        </div>
      </div>

      <!-- Bottom Spacer -->
      <div style="height: 24px;"></div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ClipboardDocumentListIcon } from '@heroicons/vue/24/outline';

const activeTab = ref('Aktif');

function goToMenu() {
  router.visit('/menu');
}
</script>

<style scoped>
.orders-page {
  background: #F0EEFF;
  min-height: 100vh;
}

.orders-header {
  background: white;
  padding: 52px 24px 20px;
  border-bottom: 1px solid #F0F0F0;
}

.orders-title {
  font-size: 24px;
  font-weight: 800;
  color: #7C6BC4;
  margin: 0 0 4px;
}

.orders-subtitle {
  font-size: 14px;
  color: #6B7280;
  margin: 0;
}

.px-container {
  padding: 0 16px;
}

.orders-tabs {
  display: flex;
  gap: 12px;
  margin-top: 16px;
}

.tab-btn {
  flex: 1;
  padding: 10px;
  border-radius: 12px;
  border: 1.5px solid #E5E7EB;
  background: white;
  font-size: 14px;
  font-weight: 600;
  color: #6B7280;
  cursor: pointer;
  transition: all 0.2s;
}

.tab-btn.active {
  background: #7C6BC4;
  border-color: #7C6BC4;
  color: white;
}

.orders-list {
  margin-top: 40px;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
}

.empty-illustration {
  font-size: 64px;
  margin-bottom: 16px;
}

.empty-title {
  font-size: 18px;
  font-weight: 700;
  color: #1B1B1B;
  margin: 0 0 8px;
}

.empty-desc {
  font-size: 14px;
  color: #6B7280;
  line-height: 1.6;
  margin: 0 0 24px;
}

.menu-btn {
  background: #7C6BC4;
  color: white;
  border: none;
  padding: 12px 32px;
  border-radius: 14px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(124, 107, 196, 0.2);
}

.menu-btn:hover {
  background: #5A4DA0;
}
</style>
