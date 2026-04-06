<template>
  <AdminLayout title="Kelola Menu">
    <div class="menu-admin-page">
      <div class="app-card-lg p-5 sm:p-6 mb-5 bg-gradient-to-br from-white to-[#f5f6ff] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <p class="text-[11px] uppercase tracking-[0.16em] font-bold text-primary/50 mb-1">Menu Management</p>
          <h2 class="text-xl sm:text-2xl font-black text-gray-900 m-0">Kelola Menu</h2>
          <p class="text-sm text-gray-500 mt-1">Total: {{ menuItems.length }} item tersedia</p>
        </div>
        <button class="btn-add" @click="openModal()">
          <PlusIcon class="h-5 w-5" />
          <span>Tambah Menu Baru</span>
        </button>
      </div>

      <!-- Search & Filters -->
      <div class="app-card p-4 sm:p-5 mb-5">
        <div class="search-box">
          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama menu atau kategori…"
            class="search-input"
          />
        </div>
      </div>

      <!-- Table -->
      <div class="table-wrapper shadow-sm app-card-lg">
        <table class="menu-table">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Kategori</th>
              <th>Harga</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in filteredItems" :key="item.id">
              <td>
                <div class="product-cell">
                  <div class="product-img-box">
                    <img :src="item.image" :alt="item.name" class="product-img" />
                  </div>
                  <div class="product-info">
                    <p class="product-name">{{ item.name }}</p>
                    <p class="product-desc">{{ item.description }}</p>
                  </div>
                </div>
              </td>
              <td>
                <span class="cat-pill">{{ item.category }}</span>
              </td>
              <td>
                <span class="price-text">{{ formatPrice(item.price) }}</span>
              </td>
              <td>
                <button
                  class="toggle-avail"
                  :class="{ active: item.is_available }"
                  @click="toggleAvailability(item)"
                >
                  <span class="toggle-dot"></span>
                  <span class="toggle-label">{{ item.is_available ? 'Tersedia' : 'Habis' }}</span>
                </button>
              </td>
              <td>
                <div class="action-btns-row">
                  <button class="btn-edit-icon" @click="openModal(item)">
                    <PencilSquareIcon class="h-4 w-4" />
                  </button>
                  <button class="btn-delete-icon" @click="confirmDelete(item)">
                    <TrashIcon class="h-4 w-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredItems.length === 0">
              <td colspan="5" class="empty-table">
                Tidak ada menu yang ditemukan.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add/Edit Modal -->
      <transition name="admin-modal">
        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
          <div class="modal-card">
            <div class="modal-header">
              <h3>{{ isEdit ? 'Edit Menu' : 'Tambah Menu Baru' }}</h3>
              <button class="close-btn" @click="closeModal">✕</button>
            </div>
            
            <form @submit.prevent="submitForm" class="modal-form">
              <div class="form-grid">
                <div class="form-group span-2">
                  <label>Nama Menu *</label>
                  <input v-model="form.name" type="text" placeholder="Contoh: Cappuccino Latte" required />
                </div>
                
                <div class="form-group">
                  <label>Kategori *</label>
                  <select v-model="form.category" required>
                    <option value="Kopi">Kopi</option>
                    <option value="Non-Kopi">Non-Kopi</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Dessert">Dessert</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Harga (Rp) *</label>
                  <input v-model="form.price" type="number" placeholder="28000" required />
                </div>

                <div class="form-group span-2">
                  <label>Deskripsi</label>
                  <textarea v-model="form.description" rows="3" placeholder="Jelaskan produk ini secara singkat…"></textarea>
                </div>

                <div class="form-group span-2">
                  <label>URL Foto (Unsplash/Imgur)</label>
                  <input v-model="form.image" type="text" placeholder="https://..." />
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn-cancel" @click="closeModal">Batal</button>
                <button type="submit" class="btn-save" :disabled="form.processing">
                  {{ form.processing ? 'Menyimpan…' : 'Simpan Perubahan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>

      <!-- Delete Confirmation Modal -->
      <transition name="admin-modal">
        <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
          <div class="modal-card modal-card-sm">
            <div class="modal-header modal-header-plain">
              <h3>Konfirmasi Hapus</h3>
              <button class="close-btn" @click="showDeleteModal = false">✕</button>
            </div>
            <div class="modal-form modal-form-tight">
              <p class="delete-copy">Yakin hapus menu <strong>{{ itemToDelete?.name }}</strong>?</p>
              <div class="modal-footer">
                <button class="btn-cancel" @click="showDeleteModal = false">Batal</button>
                <button class="btn-save btn-danger" @click="procDelete">Ya, Hapus</button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  PlusIcon, 
  MagnifyingGlassIcon, 
  PencilSquareIcon, 
  TrashIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  menuItems: Array
});

const searchQuery = ref('');
const showModal = ref(false);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);
const isEdit = ref(false);
const currentId = ref(null);

const form = useForm({
  name: '',
  category: 'Kopi',
  price: '',
  description: '',
  image: '',
  is_available: true,
});

const filteredItems = computed(() => {
  if (!searchQuery.value) return props.menuItems;
  const q = searchQuery.value.toLowerCase();
  return props.menuItems.filter(i => 
    i.name.toLowerCase().includes(q) || 
    i.category.toLowerCase().includes(q)
  );
});

function openModal(item = null) {
  if (item) {
    isEdit.value = true;
    currentId.value = item.id;
    form.name = item.name;
    form.category = item.category;
    form.price = item.price;
    form.description = item.description;
    form.image = item.image;
    form.is_available = item.is_available;
  } else {
    isEdit.value = false;
    currentId.value = null;
    form.reset();
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
}

function submitForm() {
  if (isEdit.value) {
    form.post(route('admin.menu.update', currentId.value), {
      onSuccess: () => closeModal(),
      preserveScroll: true
    });
  } else {
    form.post(route('admin.menu.store'), {
      onSuccess: () => closeModal(),
      preserveScroll: true
    });
  }
}

function toggleAvailability(item) {
  router.post(route('admin.menu.toggle', item.id), {}, {
    preserveScroll: true
  });
}

function confirmDelete(item) {
  itemToDelete.value = item;
  showDeleteModal.value = true;
}

function procDelete() {
  if (itemToDelete.value) {
    router.delete(route('admin.menu.destroy', itemToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => { showDeleteModal.value = false; itemToDelete.value = null; }
    });
  }
}

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID');
}
</script>

<style scoped>
.menu-admin-page {
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title { font-size: 1.25rem; font-weight: 800; color: #1B1B1B; margin: 0; }
.page-sub { font-size: 13px; color: #6B7280; margin: 4px 0 0; }

.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #7C6BC4;
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 15px rgba(124, 107, 196, 0.3);
}

.btn-add:hover { background: #5A4DA0; transform: translateY(-2px); }

/* Table Controls */
.table-controls {
  margin-bottom: 20px;
}

.search-box {
  position: relative;
  display: flex;
  align-items: center;
  max-width: 400px;
}

.search-box svg {
  position: absolute;
  left: 14px;
}

.search-input {
  width: 100%;
  padding: 12px 14px 12px 42px;
  border: 1px solid #E5E7EB;
  border-radius: 12px;
  font-size: 14px;
  outline: none;
  background: white;
  transition: border-color 0.2s;
}

.search-input:focus { border-color: #7C6BC4; box-shadow: 0 0 0 3px rgba(124, 107, 196, 0.1); }

/* Table Style */
.table-wrapper {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid #F0F0F0;
}

.menu-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.menu-table th {
  background: #F9FAFB;
  padding: 16px 20px;
  font-size: 13px;
  font-weight: 700;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.menu-table td {
  padding: 16px 20px;
  border-bottom: 1px solid #F3F4F6;
  vertical-align: middle;
}

.product-cell { display: flex; align-items: center; gap: 14px; }
.product-img-box { width: 48px; height: 48px; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
.product-img { width: 100%; height: 100%; object-fit: cover; }
.product-name { font-weight: 700; font-size: 14px; color: #1B1B1B; margin: 0 0 2px; }
.product-desc { font-size: 12px; color: #6B7280; margin: 0; line-clamp: 1; -webkit-line-clamp: 1; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; }

.cat-pill {
  font-size: 11px;
  font-weight: 700;
  padding: 4px 12px;
  background: #F3F4F6;
  color: #4B5563;
  border-radius: 50px;
}

.price-text { font-weight: 800; color: #7C6BC4; font-size: 14px; }

/* Toggle Avail */
.toggle-avail {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #F9FAFB;
  border: 1.5px solid #E5E7EB;
  padding: 6px 14px;
  border-radius: 50px;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 105px;
}

.toggle-dot { width: 8px; height: 8px; border-radius: 50%; background: #9CA3AF; }
.toggle-label { font-size: 12px; font-weight: 700; color: #6B7280; }

.toggle-avail.active { background: #EDE9FE; border-color: #A78BFA; }
.toggle-avail.active .toggle-dot { background: #7C6BC4; }
.toggle-avail.active .toggle-label { color: #5B21B6; }

.action-btns-row { display: flex; gap: 8px; }
.btn-edit-icon, .btn-delete-icon {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  border: 1px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-edit-icon { background: #E0F2FE; color: #0284C7; border-color: #BAE6FD; }
.btn-edit-icon:hover { background: #BAE6FD; }
.btn-delete-icon { background: #FEE2E2; color: #DC2626; border-color: #FECACA; }
.btn-delete-icon:hover { background: #FECACA; }

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  padding: 20px;
}

.modal-card {
  background: white;
  width: 100%;
  max-width: 500px;
  border-radius: 28px;
  overflow: hidden;
  box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
  border: 1px solid #F3F4F6;
}

.modal-card-sm {
  max-width: 420px;
}

.modal-header {
  padding: 24px;
  background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%);
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #F0F0F0;
}

.modal-header-plain {
  border-bottom: none;
  padding-bottom: 8px;
}

.modal-header h3 { font-size: 18px; font-weight: 800; color: #7C6BC4; margin: 0; }
.close-btn {
  background: #F3F4F6;
  border: none;
  font-size: 16px;
  cursor: pointer;
  color: #6B7280;
  width: 30px;
  height: 30px;
  border-radius: 999px;
}

.modal-form { padding: 24px; }
.modal-form-tight { padding-top: 10px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.span-2 { grid-column: span 2; }

.form-group label { display: block; font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 8px; }

.form-group input, .form-group select, .form-group textarea {
  width: 100%;
  padding: 12px;
  border: 1.5px solid #E5E7EB;
  border-radius: 12px;
  font-size: 14px;
  outline: none;
  background: #fff;
  transition: all 0.2s ease;
}

.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
  border-color: #7C6BC4;
  box-shadow: 0 0 0 3px rgba(124, 107, 196, 0.12);
}

.modal-footer {
  margin-top: 32px;
  display: flex;
  gap: 12px;
}

.btn-cancel { flex: 1; padding: 14px; background: #F3F4F6; border: none; border-radius: 12px; font-weight: 700; color: #4B5563; cursor: pointer; }
.btn-save { flex: 2; padding: 14px; background: #7C6BC4; border: none; border-radius: 12px; font-weight: 700; color: white; cursor: pointer; }
.btn-save:disabled { opacity: 0.7; }

.btn-danger { background: #EF4444; }

.delete-copy {
  font-size: 14px;
  color: #4B5563;
  margin-bottom: 24px;
  line-height: 1.5;
}

.admin-modal-enter-active,
.admin-modal-leave-active { transition: opacity 0.24s ease; }
.admin-modal-enter-from,
.admin-modal-leave-to { opacity: 0; }
.admin-modal-enter-active .modal-card { animation: adminModalPop 0.28s cubic-bezier(0.34, 1.56, 0.64, 1); }

@keyframes adminModalPop {
  0% { transform: scale(0.95) translateY(10px); opacity: 0; }
  100% { transform: scale(1) translateY(0); opacity: 1; }
}
</style>
