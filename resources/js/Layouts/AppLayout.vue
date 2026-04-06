<template>
  <div class="app-shell bg-[#eef2f3] min-h-screen">
    <Head :title="title" />
    <div class="max-w-lg mx-auto bg-white min-h-screen md:min-h-[96vh] md:my-4 md:rounded-[28px] md:shadow-2xl md:ring-1 md:ring-black/5 relative flex flex-col overflow-hidden">
      <!-- Page Content -->
      <main class="page-content flex-1 overflow-y-auto" :class="{ 'has-cart-bar': cartStore.totalItems > 0 && showBottomNav }">
        <slot />
      </main>

    <!-- Floating Cart Button -->
    <transition name="cart-bounce">
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
        <span v-if="isActive(tab.href)" class="nav-pill"></span>
        <span class="nav-icon">
          <component 
            :is="isActive(tab.href) ? tab.iconSolid : tab.iconOutline" 
            class="h-6 w-6"
          />
        </span>
        <span class="nav-label">{{ tab.label }}</span>
        <div v-if="isActive(tab.href)" class="nav-indicator"></div>
      </button>
    </nav>
    </div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false
};
</script>

<script setup>
import { computed, watch } from 'vue';
import { router, usePage, Head } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/cartStore';
import { 
  HomeIcon as HomeIconOutline, 
  QueueListIcon as MenuIconOutline, 
  TicketIcon as OrderIconOutline, 
  UserCircleIcon as ProfilIconOutline 
} from '@heroicons/vue/24/outline';
import { 
  HomeIcon as HomeIconSolid, 
  QueueListIcon as MenuIconSolid, 
  TicketIcon as OrderIconSolid, 
  UserCircleIcon as ProfilIconSolid 
} from '@heroicons/vue/24/solid';

const props = defineProps({
  title: String,
});

const cartStore = useCartStore();
const page = usePage();

const tabs = [
  { name: 'home',   href: '/',       label: 'Beranda', iconOutline: HomeIconOutline, iconSolid: HomeIconSolid },
  { name: 'menu',   href: '/menu',   label: 'Menu',    iconOutline: MenuIconOutline, iconSolid: MenuIconSolid },
  { name: 'order',  href: '/pesanan',label: 'Pesanan', iconOutline: OrderIconOutline, iconSolid: OrderIconSolid },
  { name: 'profil', href: '/profil', label: 'Profil',  iconOutline: ProfilIconOutline, iconSolid: ProfilIconSolid },
];

const showBottomNav = computed(() => {
  const hidden = ['/cart', '/order-status']; // Adjusted paths if needed
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
  padding-bottom: 92px;
}

.page-content.has-cart-bar {
  padding-bottom: 168px;
}

/* Bottom Nav */
.bottom-nav {
  position: absolute;
  bottom: 10px;
  left: 12px;
  right: 12px;
  height: 72px;
  background: rgba(255, 255, 255, 0.96);
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding: 0 10px 6px;
  z-index: 100;
  border: 1px solid #e5e7eb;
  border-radius: 18px;
  box-shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
  backdrop-filter: blur(8px);
}

.nav-tab {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 10px 12px;
  border: none;
  background: none;
  cursor: pointer;
  transition: color 0.2s ease, transform 0.2s ease;
  color: #9ca3af;
  position: relative;
  border-radius: 12px;
}

.nav-pill {
  position: absolute;
  inset: 2px 0 auto;
  height: 34px;
  border-radius: 10px;
  background: rgba(99, 103, 255, 0.12);
  z-index: -1;
}

.nav-tab.active {
  color: #2f356d;
}

.nav-icon {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.nav-tab.active .nav-icon {
  transform: translateY(-1px);
}

.nav-label {
  font-size: 11px;
  font-weight: 600;
}

.nav-tab.active .nav-label {
  font-weight: 700;
}

.nav-indicator {
  position: absolute;
  bottom: 4px;
  width: 18px;
  height: 2px;
  background: #6367ff;
  border-radius: 50%;
  animation: dot-scale 0.2s forwards;
}

@keyframes dot-scale {
  from { transform: scaleX(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

/* Floating Cart */
.floating-cart {
  position: absolute;
  bottom: 92px;
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - 48px);
  background: linear-gradient(135deg, #6367ff, #7c6bc4);
  border-radius: 16px;
  padding: 14px 16px;
  cursor: pointer;
  z-index: 99;
  box-shadow: 0 12px 24px rgba(99, 103, 255, 0.28);
  transition: transform 0.2s ease;
}

.floating-cart:active {
  transform: translateX(-50%) scale(0.97);
}

.cart-float-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cart-float-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.cart-badge {
  background: #ffffff;
  color: #6367ff;
  font-size: 13px;
  font-weight: 800;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 10px rgba(17, 24, 39, 0.15);
}

.cart-float-label {
  color: #ffffff;
  font-size: 15px;
  font-weight: 600;
}

.cart-float-price {
  color: #ffffff;
  font-size: 15px;
  font-weight: 700;
}

/* Transitions */
.cart-bounce-enter-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.cart-bounce-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.cart-bounce-enter-from {
  opacity: 0;
  transform: translateX(-50%) translateY(30px);
}
.cart-bounce-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(30px) scale(0.9);
}

@media (max-width: 380px) {
  .page-content {
    padding-bottom: 88px;
  }

  .page-content.has-cart-bar {
    padding-bottom: 156px;
  }

  .bottom-nav {
    left: 8px;
    right: 8px;
    height: 68px;
    padding: 0 6px 4px;
  }

  .nav-tab {
    padding: 8px 8px;
    gap: 3px;
  }

  .nav-label {
    font-size: 10px;
  }

  .floating-cart {
    width: calc(100% - 28px);
    bottom: 84px;
    padding: 12px 12px;
  }

  .cart-float-left {
    gap: 10px;
  }

  .cart-float-label,
  .cart-float-price {
    font-size: 13px;
  }
}
</style>
