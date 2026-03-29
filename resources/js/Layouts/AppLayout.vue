<template>
  <div class="app-shell">
    <!-- Page Content -->
    <main class="page-content" :class="{ 'has-cart-bar': cartStore.totalItems > 0 && showBottomNav }">
      <slot />
    </main>

    <!-- Floating Cart Button -->
    <transition name="cart-float">
      <div
        v-if="cartStore.totalItems > 0 && showBottomNav"
        class="floating-cart"
        @click="goToCart"
      >
        <div class="cart-float-inner">
          <div class="cart-float-left">
            <div class="cart-badge">{{ cartStore.totalItems }}</div>
            <span class="cart-float-label">Lihat Keranjang</span>
          </div>
          <span class="cart-float-price">{{ formatPrice(cartStore.totalPrice) }}</span>
        </div>
      </div>
    </transition>

    <!-- Bottom Navigation -->
    <nav v-if="showBottomNav" class="bottom-nav">
      <button
        v-for="tab in tabs"
        :key="tab.name"
        class="nav-tab"
        :class="{ active: isActive(tab.href) }"
        @click="navigate(tab.href)"
      >
        <span class="nav-icon">
          <component 
            :is="isActive(tab.href) ? tab.iconSolid : tab.iconOutline" 
            class="h-6 w-6"
          />
        </span>
        <span class="nav-label">{{ tab.label }}</span>
      </button>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/cartStore';
import { 
  HomeIcon as HomeIconOutline, 
  Bars3Icon as MenuIconOutline, 
  ClipboardDocumentListIcon as OrderIconOutline, 
  UserIcon as ProfilIconOutline 
} from '@heroicons/vue/24/outline';
import { 
  HomeIcon as HomeIconSolid, 
  Bars3Icon as MenuIconSolid, 
  ClipboardDocumentListIcon as OrderIconSolid, 
  UserIcon as ProfilIconSolid 
} from '@heroicons/vue/24/solid';

const cartStore = useCartStore();
const page = usePage();

const tabs = [
  { name: 'home',   href: '/',       label: 'Beranda', iconOutline: HomeIconOutline, iconSolid: HomeIconSolid },
  { name: 'menu',   href: '/menu',   label: 'Menu',    iconOutline: MenuIconOutline, iconSolid: MenuIconSolid },
  { name: 'order',  href: '/pesanan',label: 'Pesanan', iconOutline: OrderIconOutline, iconSolid: OrderIconSolid },
  { name: 'profil', href: '/profil', label: 'Profil',  iconOutline: ProfilIconOutline, iconSolid: ProfilIconSolid },
];

const showBottomNav = computed(() => {
  const hidden = ['/cart', '/order/'];
  return !hidden.some(p => page.url.startsWith(p));
});

function isActive(href) {
  if (href === '/') return page.url === '/';
  return page.url.startsWith(href);
}

function navigate(href) {
  router.visit(href);
}

function goToCart() {
  router.visit('/cart');
}

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}
</script>

<style scoped>
.app-shell {
  min-height: 100dvh;
  display: flex;
  flex-direction: column;
}

.page-content {
  flex: 1;
  padding-bottom: 72px;
}

.page-content.has-cart-bar {
  padding-bottom: 140px;
}

/* Bottom Nav */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  max-width: 430px;
  height: 72px;
  background: #ffffff;
  border-top: 1px solid #F0F0F0;
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding: 0 8px;
  z-index: 50;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
}

.nav-tab {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  padding: 8px 16px;
  border: none;
  background: none;
  cursor: pointer;
  border-radius: 12px;
  transition: all 0.2s ease;
  min-width: 56px;
  min-height: 44px;
  color: #9CA3AF;
}

.nav-tab:hover { background: #F8F9FA; }

.nav-tab.active {
  color: #7C6BC4;
}

.nav-icon {
  font-size: 20px;
  line-height: 1;
  display: block;
  transition: transform 0.2s ease;
}

.nav-tab.active .nav-icon {
  transform: scale(1.15);
}

.nav-label {
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.2px;
}

.nav-tab.active .nav-label {
  font-weight: 700;
}

/* Active indicator dot */
.nav-tab.active::after {
  content: '';
  display: block;
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: #7C6BC4;
  position: absolute;
  bottom: 8px;
}

.nav-tab { position: relative; }

/* Floating Cart */
.floating-cart {
  position: fixed;
  bottom: 80px;
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 32px);
  max-width: 398px;
  background: #5A4DA0;
  border-radius: 16px;
  padding: 14px 20px;
  cursor: pointer;
  z-index: 49;
  box-shadow: 0 8px 24px rgba(90, 77, 160, 0.35);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.floating-cart:hover {
  transform: translateX(-50%) translateY(-2px);
  box-shadow: 0 12px 28px rgba(90, 77, 160, 0.45);
}

.floating-cart:active {
  transform: translateX(-50%) translateY(0);
}

.cart-float-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cart-float-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.cart-badge {
  background: #9B8FD4;
  color: white;
  font-size: 12px;
  font-weight: 700;
  width: 24px;
  height: 24px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cart-float-label {
  color: #ffffff;
  font-size: 14px;
  font-weight: 600;
}

.cart-float-price {
  color: #ffffff;
  font-size: 14px;
  font-weight: 700;
}

/* Transitions */
.cart-float-enter-active,
.cart-float-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.cart-float-enter-from,
.cart-float-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(20px);
}
</style>
