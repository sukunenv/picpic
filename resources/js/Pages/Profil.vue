<template>
  <AppLayout>
    <div class="profil-page">
      <!-- Header -->
      <div class="profil-header">
        <div class="avatar-wrap">
          <UserCircleIcon class="h-20 w-20 text-white/80" />
        </div>
        <h1 class="profil-name">Pelanggan Setia</h1>
        <p class="profil-sub">Picpic Member</p>
      </div>

      <!-- Stats -->
      <div class="stats-row px-container">
        <div class="stat-card">
          <ClipboardDocumentCheckIcon class="h-5 w-5 text-[#7C6BC4]" />
          <span class="stat-num">12</span>
          <span class="stat-label">Pesanan</span>
        </div>
        <div class="stat-card">
          <StarIcon class="h-5 w-5 text-[#7C6BC4]" />
          <span class="stat-num">4.9</span>
          <span class="stat-label">Rating Fav</span>
        </div>
        <div class="stat-card">
          <BeakerIcon class="h-5 w-5 text-[#7C6BC4]" />
          <span class="stat-num">Kopi</span>
          <span class="stat-label">Kategori</span>
        </div>
      </div>

      <!-- Menu List -->
      <div class="menu-sections px-container">
        <div class="profil-section">
          <h3 class="section-title">
            <Cog6ToothIcon class="h-4 w-4 inline mr-1 mb-0.5" /> Pengaturan
          </h3>
          <div
            v-for="item in settingItems"
            :key="item.label"
            class="menu-item"
          >
            <component :is="item.icon" class="h-5 w-5 text-gray-500" />
            <span class="menu-item-label">{{ item.label }}</span>
            <ChevronRightIcon class="h-4 w-4 text-gray-300" />
          </div>
        </div>

        <div class="profil-section">
          <h3 class="section-title">
            <InformationCircleIcon class="h-4 w-4 inline mr-1 mb-0.5" /> Informasi
          </h3>
          <div
            v-for="item in infoItems"
            :key="item.label"
            class="menu-item"
          >
            <component :is="item.icon" class="h-5 w-5 text-gray-500" />
            <span class="menu-item-label">{{ item.label }}</span>
            <ChevronRightIcon class="h-4 w-4 text-gray-300" />
          </div>
        </div>
      </div>

      <!-- App version -->
      <div class="app-version">
        <SparklesIcon class="h-8 w-8 text-[#7C6BC4] mx-auto mb-2" />
        <p class="app-name">Picpic</p>
        <p class="version">v1.0.0 — Made with <HeartIcon class="h-3 w-3 inline text-red-500 fill-current" /> in Indonesia</p>
      </div>

      <div style="height: 16px;"></div>
    </div>
  </AppLayout>
</template>

<script setup>
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
  Cog6ToothIcon,
  InformationCircleIcon,
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
</script>

<style scoped>
.profil-page { background: #F0EEFF; min-height: 100dvh; }

.profil-header {
  background: linear-gradient(135deg, #7C6BC4 0%, #5A4DA0 100%);
  padding: 52px 24px 32px;
  text-align: center;
}

.avatar-wrap {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
}

.avatar-emoji { font-size: 40px; }

.profil-name {
  font-size: 22px;
  font-weight: 700;
  color: white;
  margin: 0 0 4px;
}

.profil-sub {
  font-size: 13px;
  color: rgba(255,255,255,0.7);
  margin: 0;
}

.stats-row {
  display: flex;
  gap: 10px;
  margin-top: -20px;
  margin-bottom: 16px;
}

.px-container { padding: 0 16px; }

.stat-card {
  flex: 1;
  background: white;
  border-radius: 16px;
  padding: 14px 10px;
  text-align: center;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.stat-num { font-size: 16px; font-weight: 800; color: #7C6BC4; }
.stat-label { font-size: 11px; color: #6B7280; font-weight: 500; }

.menu-sections { display: flex; flex-direction: column; gap: 16px; }

.profil-section {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-title {
  font-size: 14px;
  font-weight: 700;
  color: #6B7280;
  padding: 14px 16px 10px;
  margin: 0;
  border-bottom: 1px solid #F3F4F6;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 15px 16px;
  border-bottom: 1px solid #F9FAFB;
  cursor: pointer;
  transition: background 0.2s;
  min-height: 52px;
}

.menu-item:last-child { border-bottom: none; }
.menu-item:hover { background: #F9FAFB; }
.menu-item:active { background: #F0EEFF; }

.menu-item-icon { font-size: 20px; }
.menu-item-label { flex: 1; font-size: 14px; color: #1B1B1B; font-weight: 500; }
.menu-arrow { color: #9CA3AF; }

.app-version {
  text-align: center;
  padding: 24px;
}

.brand-logo { font-size: 32px; display: block; margin-bottom: 6px; }
.app-name { font-size: 15px; font-weight: 700; color: #7C6BC4; margin: 0 0 4px; }
.version { font-size: 12px; color: #9CA3AF; margin: 0; }
</style>
