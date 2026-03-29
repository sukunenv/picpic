<template>
  <div class="admin-shell">
    <!-- Desktop Sidebar -->
    <aside class="sidebar-desktop">
      <div class="sidebar-header">
        <img src="/logo.png" class="w-8 h-8 rounded-full">
        <h1 class="admin-brand">Picpic Admin</h1>
      </div>
      
      <nav class="sidebar-nav">
        <Link
          v-for="item in navItems"
          :key="item.href"
          :href="item.href"
          class="nav-link"
          :class="{ active: isActive(item.href) }"
        >
          <component :is="item.icon" class="h-5 w-5" />
          <span>{{ item.label }}</span>
        </Link>
      </nav>

      <div class="sidebar-footer">
        <div class="user-info">
          <p class="user-name">{{ $page.props.auth.user.name }}</p>
          <p class="user-role">Kasir / Admin</p>
        </div>
        <button class="logout-btn" @click="logout">
          <ArrowLeftStartOnRectangleIcon class="h-5 w-5" />
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
      <!-- Mobile Header -->
      <header class="mobile-header">
        <img src="/logo.png" alt="Picpic Logo" class="mobile-logo" />
        <span class="mobile-brand">Picpic Admin</span>
        <button class="mobile-logout" @click="logout" aria-label="Logout">
          <ArrowLeftStartOnRectangleIcon class="h-5 w-5" />
        </button>
      </header>

      <main class="admin-main">
        <slot />
      </main>

      <!-- Mobile Bottom Nav -->
      <nav class="mobile-bottom-nav">
        <Link
          v-for="item in navItems"
          :key="item.href"
          :href="item.href"
          class="mobile-nav-link"
          :class="{ active: isActive(item.href) }"
        >
          <component :is="item.icon" class="h-6 w-6" />
          <span>{{ item.label }}</span>
        </Link>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { 
  SparklesIcon, 
  Squares2X2Icon, 
  ClipboardDocumentListIcon, 
  QueueListIcon,
  ArrowLeftStartOnRectangleIcon
} from '@heroicons/vue/24/outline';

const page = usePage();

const navItems = [
  { href: '/admin/dashboard', label: 'Dashboard', icon: Squares2X2Icon },
  { href: '/admin/orders',    label: 'Orders',    icon: ClipboardDocumentListIcon },
  { href: '/admin/menu',      label: 'Menu',      icon: QueueListIcon },
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

<style scoped>
.admin-shell {
  display: flex;
  min-height: 100vh;
  background: #F9FAFB; /* gray-50 */
  font-family: 'Plus Jakarta Sans', sans-serif;
}

/* Sidebar Desktop */
.sidebar-desktop {
  width: 220px;
  background: #5A4DA0;
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  z-index: 100;
}

@media (max-width: 1023px) {
  .sidebar-desktop { display: none; }
}

.sidebar-header {
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.sidebar-logo {
  width: 40px;
  height: 40px;
  object-fit: contain;
  border-radius: 8px;
  background: white;
  padding: 2px;
}

.admin-brand {
  font-size: 18px;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.sidebar-nav {
  flex: 1;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-weight: 600;
  border-radius: 12px;
  transition: all 0.2s;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.nav-link.active {
  background: #9B8FD4;
  color: white;
  box-shadow: 0 4px 12px rgba(155, 143, 212, 0.3);
}

.sidebar-footer {
  padding: 24px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info { margin-bottom: 16px; }
.user-name { font-weight: 700; margin: 0; }
.user-role { font-size: 12px; color: rgba(255, 255, 255, 0.5); margin: 0; }

.logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  background: rgba(239, 68, 68, 0.1);
  color: #EF4444;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.logout-btn:hover { background: rgba(239, 68, 68, 0.2); }

/* Main Wrapper */
.main-wrapper {
  flex: 1;
  margin-left: 220px;
  display: flex;
  flex-direction: column;
}

@media (max-width: 1023px) {
  .main-wrapper { margin-left: 0; }
}

.mobile-header {
  display: none;
  background: white;
  padding: 12px 16px;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid #F0F0F0;
  position: sticky;
  top: 0;
  z-index: 90;
}

.mobile-logo {
  width: 32px;
  height: 32px;
  object-fit: contain;
  border-radius: 50%;
}

@media (max-width: 1023px) {
  .mobile-header { display: flex; }
}

.mobile-brand {
  font-weight: 800;
  color: #7C6BC4;
  font-size: 16px;
  flex: 1;
}

.mobile-logout {
  background: none;
  border: none;
  color: #EF4444;
  cursor: pointer;
}

.admin-main {
  padding: 24px 24px 24px 32px;
  max-width: 1200px;
  width: 100%;
}

@media (max-width: 767px) {
  .admin-main { padding: 16px; padding-bottom: 90px; }
}

/* Mobile Bottom Nav */
.mobile-bottom-nav {
  display: none;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  height: 72px;
  border-top: 1px solid #F0F0F0;
  justify-content: space-around;
  align-items: center;
  padding: 0 10px;
  z-index: 100;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
}

@media (max-width: 1023px) {
  .mobile-bottom-nav { display: flex; }
}

.mobile-nav-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  color: #9CA3AF;
  text-decoration: none;
  font-size: 11px;
  font-weight: 600;
  transition: color 0.2s;
}

.mobile-nav-link.active {
  color: #7C6BC4;
}
</style>
