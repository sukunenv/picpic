<template>
  <div class="flex h-screen overflow-hidden bg-[#eef2f3] font-sans">
    <Head :title="title" />
    <!-- SIDEBAR - fixed kiri, full height (Hanya Desktop) -->
    <aside class="hidden lg:flex lg:flex-shrink-0 p-4 pr-0">
      <div class="flex flex-col w-64 bg-[#4B3FA0] shadow-2xl shadow-[#4B3FA0]/20 z-50 rounded-3xl overflow-hidden">
        
        <!-- Header Sidebar -->
        <div class="p-6 flex items-center gap-3 border-b border-white/10">
          <img src="/logo.png" class="w-8 h-8 rounded-full shrink-0 bg-white p-[2px]">
          <h1 class="text-lg font-extrabold tracking-tight text-white m-0">Picpic Admin</h1>
        </div>
        
        <!-- Nav items -->
        <nav class="flex-1 px-4 flex flex-col gap-2 overflow-y-auto py-4">
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="flex items-center gap-3 px-4 py-3 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 font-semibold"
            :class="{ 'bg-[#9B8FD4] !text-white shadow-lg shadow-[#9B8FD4]/30': isActive(item.href) }"
          >
            <component :is="item.icon" class="h-5 w-5 shrink-0" />
            <span>{{ item.label }}</span>
          </Link>
        </nav>

        <!-- Admin info + logout -->
        <div class="p-6 border-t border-white/10 mt-auto">
          <div class="mb-4">
            <p class="font-bold m-0 text-white">{{ $page.props.auth.user.name }}</p>
            <p class="text-[12px] text-white/50 m-0">Kasir / Admin</p>
          </div>
          <button class="w-full flex items-center gap-2.5 p-2.5 bg-red-500/10 text-red-500 hover:bg-red-500/20 rounded-xl font-semibold transition-all duration-200" @click="logout">
            <ArrowLeftStartOnRectangleIcon class="h-5 w-5 shrink-0" />
            <span>Logout</span>
          </button>
        </div>
        
      </div>
    </aside>

    <!-- KONTEN KANAN - flex-1 agar isi sisa layar -->
    <div class="flex flex-col flex-1 w-0 overflow-hidden lg:p-4 lg:pl-4">
      
      <!-- Mobile header -->
      <header class="lg:hidden bg-white/95 backdrop-blur-md border-b border-gray-100 flex-shrink-0 flex items-center justify-between px-4 py-3 shadow-sm z-40">
        <div class="flex items-center gap-2.5">
          <img src="/logo.png" alt="Picpic Logo" class="w-8 h-8 rounded-full" />
          <span class="font-extrabold text-[#7C6BC4] text-base">Picpic Admin</span>
        </div>
        <button class="text-red-500 outline-none p-2" @click="logout" aria-label="Logout">
          <ArrowLeftStartOnRectangleIcon class="h-6 w-6" />
        </button>
      </header>

      <!-- Scrollable content -->
      <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none app-card lg:rounded-3xl lg:shadow-xl lg:ring-1 lg:ring-black/5 lg:bg-gradient-to-br lg:from-white lg:to-[#f8f9ff]">
        <div class="p-4 sm:p-6 lg:p-8 pb-32 lg:pb-8">
          <slot />
        </div>
      </main>

    </div>

    <!-- Mobile bottom nav -->
    <nav class="fixed bottom-3 inset-x-3 lg:hidden bg-white/95 backdrop-blur-md border border-gray-200/80 h-[70px] flex items-center justify-around z-50 shadow-[0_10px_35px_rgba(15,23,42,0.10)] rounded-2xl px-2 pb-safe">
      <Link
        v-for="item in navItems"
        :key="item.href"
        :href="item.href"
        class="flex flex-col items-center gap-1.5 text-[11px] font-semibold transition-colors duration-200 py-2 w-16"
        :class="isActive(item.href) ? 'text-[#7C6BC4]' : 'text-gray-400 hover:text-gray-600'"
      >
        <component :is="item.icon" class="h-6 w-6" />
        <span>{{ item.label }}</span>
      </Link>
    </nav>

  </div>
</template>

<script>
export default {
  inheritAttrs: false
};
</script>

<script setup>
import { Link, router, usePage, Head } from '@inertiajs/vue3';
import { 
  SparklesIcon, 
  Squares2X2Icon, 
  ClipboardDocumentListIcon, 
  QueueListIcon,
  ArrowLeftStartOnRectangleIcon,
  PhotoIcon,
  BanknotesIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  title: String,
});

const page = usePage();

const navItems = [
  { href: '/admin/dashboard', label: 'Dashboard', icon: Squares2X2Icon },
  { href: '/admin/kasir',     label: 'Kasir',     icon: BanknotesIcon },
  { href: '/admin/orders',    label: 'Orders',    icon: ClipboardDocumentListIcon },
  { href: '/admin/menu',      label: 'Menu',      icon: QueueListIcon },
  { href: '/admin/banners',   label: 'Banners',   icon: PhotoIcon },
];

function isActive(href) {
  return page.url.startsWith(href);
}

function logout() {
  if (confirm('Yakin ingin logout?')) {
    router.post(route('admin.logout'));
  }
}
</script>

<style>
/* Safe area inset for iOS mobile bottom swiper */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
  .pb-safe {
    padding-bottom: env(safe-area-inset-bottom);
  }
}
</style>
