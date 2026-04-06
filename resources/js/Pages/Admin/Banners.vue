<template>
  <AdminLayout title="Kelola Banner Promo">
    <div class="banners-admin-page">
      <div class="app-card-lg p-5 sm:p-6 mb-5 bg-gradient-to-br from-white to-[#f5f6ff] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <p class="text-[11px] uppercase tracking-[0.16em] font-bold text-primary/50 mb-1">Banner Management</p>
          <h2 class="text-xl sm:text-2xl font-black text-gray-900 m-0">Kelola Banner & Promo</h2>
          <p class="text-sm text-gray-500 mt-1">Total: {{ banners.length }} banner terdaftar</p>
        </div>
        <button class="btn-add" @click="openModal()">
          <PlusIcon class="h-5 w-5" />
          <span>Tambah Banner Baru</span>
        </button>
      </div>

      <!-- Table -->
      <div class="table-wrapper shadow-sm app-card-lg">
        <table class="menu-table">
          <thead>
            <tr>
              <th>Preview</th>
              <th>Info Banner</th>
              <th>Jadwal</th>
              <th>Urutan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in banners" :key="item.id">
              <td>
                <div class="banner-preview">
                  <img :src="item.image || '/logo.png'" :alt="item.title" class="banner-img" />
                </div>
              </td>
              <td>
                <div class="banner-info">
                  <p class="banner-name">{{ item.title }}</p>
                  <p class="banner-desc">{{ item.description }}</p>
                  <p v-if="item.button_text" class="banner-btn-info">Tombol: {{ item.button_text }} ({{ item.button_url }})</p>
                </div>
              </td>
              <td>
                <div class="schedule-info">
                  <p v-if="item.start_date || item.end_date" class="text-xs">
                    {{ item.start_date ? formatDate(item.start_date) : '...' }} - 
                    {{ item.end_date ? formatDate(item.end_date) : '...' }}
                  </p>
                  <p v-else class="text-xs text-gray-400">Selalu Tampil</p>
                </div>
              </td>
              <td>
                <span class="order-pill">{{ item.order }}</span>
              </td>
              <td>
                <button
                  class="toggle-avail"
                  :class="{ active: item.is_active }"
                  @click="toggleStatus(item)"
                >
                  <span class="toggle-dot"></span>
                  <span class="toggle-label">{{ item.is_active ? 'Aktif' : 'Nonaktif' }}</span>
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
            <tr v-if="banners.length === 0">
              <td colspan="6" class="empty-table">
                Belum ada banner promo.
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
              <h3>{{ isEdit ? 'Edit Banner' : 'Tambah Banner Promo' }}</h3>
              <button class="close-btn" @click="closeModal">✕</button>
            </div>
            
            <form @submit.prevent="submitForm" class="modal-form">
              <div class="form-grid">
                <div class="form-group span-2">
                  <label>Judul Promo *</label>
                  <input v-model="form.title" type="text" placeholder="Contoh: Diskon Akhir Pekan 50%" required />
                </div>
                
                <div class="form-group span-2">
                  <label>Deskripsi Singkat</label>
                  <textarea v-model="form.description" rows="2" placeholder="Jelaskan promo ini…"></textarea>
                </div>

                <div class="form-group">
                  <label>Teks Tombol</label>
                  <input v-model="form.button_text" type="text" placeholder="Contoh: Klaim Sekarang" />
                </div>

                <div class="form-group">
                  <label>URL Tujuan Tombol</label>
                  <input v-model="form.button_url" type="text" placeholder="Contoh: /menu" />
                </div>

                <div class="form-group span-2">
                  <label>URL Foto Banner (Cloudinary/Unsplash)</label>
                  <input v-model="form.image" type="text" placeholder="https://..." />
                </div>

                <div class="form-group text-xs">
                  <label>Tanggal Mulai (Opsional)</label>
                  <input v-model="form.start_date" type="date" />
                </div>

                <div class="form-group text-xs">
                  <label>Tanggal Berakhir (Opsional)</label>
                  <input v-model="form.end_date" type="date" />
                </div>

                <div class="form-group">
                  <label>Urutan Tampil</label>
                  <input v-model="form.order" type="number" placeholder="0" />
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn-cancel" @click="closeModal">Batal</button>
                <button type="submit" class="btn-save" :disabled="form.processing">
                  {{ form.processing ? 'Menyimpan…' : 'Simpan Banner' }}
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
              <h3>Hapus Banner</h3>
              <button class="close-btn" @click="showDeleteModal = false">✕</button>
            </div>
            <div class="modal-form modal-form-tight">
              <p class="delete-copy">Yakin hapus banner <strong>{{ itemToDelete?.title }}</strong>?</p>
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
import { ref, reactive } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  PlusIcon, 
  PencilSquareIcon, 
  TrashIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  banners: Array
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);
const isEdit = ref(false);
const currentId = ref(null);

const form = useForm({
  title: '',
  description: '',
  button_text: '',
  button_url: '',
  image: '',
  is_active: true,
  start_date: '',
  end_date: '',
  order: 0,
});

function openModal(item = null) {
  if (item) {
    isEdit.value = true;
    currentId.value = item.id;
    form.title = item.title;
    form.description = item.description || '';
    form.button_text = item.button_text || '';
    form.button_url = item.button_url || '';
    form.image = item.image || '';
    form.is_active = item.is_active;
    form.start_date = item.start_date ? item.start_date.split('T')[0] : '';
    form.end_date = item.end_date ? item.end_date.split('T')[0] : '';
    form.order = item.order;
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
    form.post(route('admin.banners.update', currentId.value), {
      onSuccess: () => closeModal(),
      preserveScroll: true
    });
  } else {
    form.post(route('admin.banners.store'), {
      onSuccess: () => closeModal(),
      preserveScroll: true
    });
  }
}

function toggleStatus(item) {
  router.post(route('admin.banners.toggle', item.id), {}, {
    preserveScroll: true
  });
}

function confirmDelete(item) {
  itemToDelete.value = item;
  showDeleteModal.value = true;
}

function procDelete() {
  if (itemToDelete.value) {
    router.delete(route('admin.banners.destroy', itemToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => { showDeleteModal.value = false; itemToDelete.value = null; }
    });
  }
}

function formatDate(dateString) {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
}
</script>

<style scoped>
.banners-admin-page { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-title { font-size: 1.25rem; font-weight: 800; color: #1B1B1B; margin: 0; }
.page-sub { font-size: 13px; color: #6B7280; margin: 4px 0 0; }

.btn-add {
  display: flex; align-items: center; gap: 8px; background: #7C6BC4; color: white;
  border: none; padding: 12px 20px; border-radius: 12px; font-weight: 700; cursor: pointer;
  transition: all 0.2s; box-shadow: 0 4px 15px rgba(124, 107, 196, 0.3);
}
.btn-add:hover { background: #5A4DA0; transform: translateY(-2px); }

.table-wrapper { background: white; border-radius: 20px; overflow: hidden; border: 1px solid #F0F0F0; }
.menu-table { width: 100%; border-collapse: collapse; text-align: left; }
.menu-table th { background: #F9FAFB; padding: 16px 20px; font-size: 13px; font-weight: 700; color: #6B7280; text-transform: uppercase; }
.menu-table td { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }

.banner-preview { width: 120px; height: 64px; border-radius: 12px; overflow: hidden; background: #F3F4F6; }
.banner-img { width: 100%; height: 100%; object-fit: cover; }

.banner-name { font-weight: 700; font-size: 14px; color: #1B1B1B; margin-bottom: 2px; }
.banner-desc { font-size: 12px; color: #6B7280; line-clamp: 1; -webkit-line-clamp: 1; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; }
.banner-btn-info { font-size: 10px; color: #7C6BC4; font-weight: 600; margin-top: 4px; }

.order-pill { font-size: 12px; font-weight: 800; padding: 4px 10px; background: #F3F4F6; border-radius: 8px; color: #4B3FA0; }

.toggle-avail {
  display: flex; align-items: center; gap: 8px; background: #F9FAFB; border: 1.5px solid #E5E7EB;
  padding: 6px 14px; border-radius: 50px; cursor: pointer; transition: all 0.2s; min-width: 100px;
}
.toggle-dot { width: 8px; height: 8px; border-radius: 50%; background: #9CA3AF; }
.toggle-label { font-size: 12px; font-weight: 700; color: #6B7280; }
.toggle-avail.active { background: #EDE9FE; border-color: #A78BFA; }
.toggle-avail.active .toggle-dot { background: #7C6BC4; }
.toggle-avail.active .toggle-label { color: #5B21B6; }

.action-btns-row { display: flex; gap: 8px; }
.btn-edit-icon, .btn-delete-icon {
  width: 38px; height: 38px; border-radius: 12px; border: 1px solid transparent; cursor: pointer;
  display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;
}
.btn-edit-icon { background: #E0F2FE; color: #0284C7; border-color: #BAE6FD; }
.btn-edit-icon:hover { background: #BAE6FD; }
.btn-delete-icon { background: #FEE2E2; color: #DC2626; border-color: #FECACA; }
.btn-delete-icon:hover { background: #FECACA; }

.modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 20px; }
.modal-card { background: white; width: 100%; max-width: 500px; border-radius: 28px; overflow: hidden; box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22); margin: auto; border: 1px solid #F3F4F6; }
.modal-card-sm { max-width: 420px; }
.modal-header { padding: 24px; background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%); display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #F0F0F0; }
.modal-header-plain { border-bottom: none; padding-bottom: 8px; }
.modal-header h3 { font-size: 18px; font-weight: 800; color: #7C6BC4; margin: 0; }
.close-btn { background: #F3F4F6; border: none; font-size: 16px; cursor: pointer; color: #6B7280; width: 30px; height: 30px; border-radius: 999px; }
.modal-form { padding: 24px; }
.modal-form-tight { padding-top: 10px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.span-2 { grid-column: span 2; }
.form-group label { display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px; }
.form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1.5px solid #E5E7EB; border-radius: 10px; font-size: 14px; outline: none; background: #fff; transition: all 0.2s ease; }
.form-group input:focus, .form-group textarea:focus { border-color: #7C6BC4; box-shadow: 0 0 0 3px rgba(124, 107, 196, 0.12); }
.modal-footer { margin-top: 24px; display: flex; gap: 12px; }
.btn-cancel { flex: 1; padding: 12px; background: #F3F4F6; border: none; border-radius: 10px; font-weight: 700; color: #4B5563; cursor: pointer; }
.btn-save { flex: 2; padding: 12px; background: #7C6BC4; border: none; border-radius: 10px; font-weight: 700; color: white; cursor: pointer; }
.btn-danger { background: #EF4444; }
.delete-copy { font-size: 14px; color: #4B5563; margin-bottom: 24px; line-height: 1.5; }

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
