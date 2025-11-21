<template>
  <div class="profile-container">
    <!-- Header -->
    <div class="profile-header">
      <button class="btn-back" @click="goBack">
        ← Kembali
      </button>
      <h1>Profil Saya</h1>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Memuat data profil...</p>
    </div>

    <!-- Profile Content -->
    <div v-else class="profile-content">
      <!-- Profile Card -->
      <div class="profile-card">
        <div class="avatar-section">
          <div class="avatar-circle">
            <span class="avatar-icon">👤</span>
          </div>
          <button class="btn-change-photo" @click="showPhotoUpload">
            📷 Ubah Foto
          </button>
        </div>

        <div class="profile-info-display">
          <h2>{{ userProfile?.nama || 'Nama Pengguna' }}</h2>
          <p class="user-email">{{ userProfile?.email || 'email@example.com' }}</p>
          <p class="user-phone">📱 {{ userProfile?.no_telp || 'Belum ada nomor telepon' }}</p>
          <p class="member-since">Bergabung sejak {{ formatDate(userProfile?.created_at) }}</p>
        </div>
      </div>

      <!-- Detail Information -->
      <div class="info-section">
        <div class="section-header">
          <h3>Informasi Detail</h3>
          <button class="btn-edit" @click="toggleEditMode">
            {{ isEditMode ? '❌ Batal' : '✏️ Edit Profil' }}
          </button>
        </div>

        <!-- View Mode -->
        <div v-if="!isEditMode" class="info-grid">
          <div class="info-item">
            <label>Nama Lengkap</label>
            <p>{{ userProfile?.nama || '-' }}</p>
          </div>
          <div class="info-item">
            <label>Email</label>
            <p>{{ userProfile?.email || '-' }}</p>
          </div>
          <div class="info-item">
            <label>Nomor Telepon</label>
            <p>{{ userProfile?.no_telp || '-' }}</p>
          </div>
          <div class="info-item">
            <label>Alamat</label>
            <p>{{ userProfile?.alamat || 'Belum ada alamat' }}</p>
          </div>
          <div class="info-item">
            <label>Kota</label>
            <p>{{ userProfile?.kota || '-' }}</p>
          </div>
          <div class="info-item">
            <label>Kode Pos</label>
            <p>{{ userProfile?.kode_pos || '-' }}</p>
          </div>
        </div>

        <!-- Edit Mode -->
        <div v-else class="edit-form">
          <div class="form-group">
            <label>Nama Lengkap *</label>
            <input 
              v-model="editForm.nama" 
              type="text" 
              placeholder="Masukkan nama lengkap"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label>Email *</label>
            <input 
              v-model="editForm.email" 
              type="email" 
              placeholder="Masukkan email"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label>Nomor Telepon *</label>
            <input 
              v-model="editForm.no_telp" 
              type="tel" 
              placeholder="08xxxxxxxxxx"
              class="form-control"
            />
          </div>

          <div class="form-group full-width">
            <label>Alamat Lengkap</label>
            <textarea 
              v-model="editForm.alamat" 
              placeholder="Masukkan alamat lengkap"
              class="form-control"
              rows="3"
            ></textarea>
          </div>

          <div class="form-group">
            <label>Kota</label>
            <input 
              v-model="editForm.kota" 
              type="text" 
              placeholder="Masukkan kota"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label>Kode Pos</label>
            <input 
              v-model="editForm.kode_pos" 
              type="text" 
              placeholder="12345"
              class="form-control"
            />
          </div>

          <div class="form-actions">
            <button class="btn-save" @click="saveProfile">
              💾 Simpan Perubahan
            </button>
            <button class="btn-cancel" @click="cancelEdit">
              Batal
            </button>
          </div>
        </div>
      </div>

      <!-- Change Password Section -->
      <div class="password-section">
        <div class="section-header">
          <h3>Keamanan Akun</h3>
        </div>
        
        <button class="btn-change-password" @click="showChangePassword">
          🔒 Ubah Password
        </button>
      </div>

      <!-- Statistics -->
      <div class="stats-section">
        <h3>Statistik Saya</h3>
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
              <p class="stat-number">{{ userStats.totalOrders || 0 }}</p>
              <p class="stat-label">Total Pesanan</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
              <p class="stat-number">{{ userStats.completedOrders || 0 }}</p>
              <p class="stat-label">Selesai</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
              <p class="stat-number">{{ userStats.pendingOrders || 0 }}</p>
              <p class="stat-label">Dalam Proses</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();
const authStore = useAuthStore();

// State
const loading = ref(true);
const isEditMode = ref(false);
const userProfile = ref<any>(null);
const userStats = ref({
  totalOrders: 0,
  completedOrders: 0,
  pendingOrders: 0
});

const editForm = ref({
  nama: '',
  email: '',
  no_telp: '',
  alamat: '',
  kota: '',
  kode_pos: ''
});

// Methods
function goBack() {
  router.back();
}

function formatDate(dateString: string) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
}

function toggleEditMode() {
  isEditMode.value = !isEditMode.value;
  
  if (isEditMode.value) {
    // Isi form dengan data saat ini
    editForm.value = {
      nama: userProfile.value?.nama || '',
      email: userProfile.value?.email || '',
      no_telp: userProfile.value?.no_telp || '',
      alamat: userProfile.value?.alamat || '',
      kota: userProfile.value?.kota || '',
      kode_pos: userProfile.value?.kode_pos || ''
    };
  }
}

function cancelEdit() {
  isEditMode.value = false;
  editForm.value = {
    nama: '',
    email: '',
    no_telp: '',
    alamat: '',
    kota: '',
    kode_pos: ''
  };
}

async function saveProfile() {
  // Validasi
  if (!editForm.value.nama || !editForm.value.email || !editForm.value.no_telp) {
    Swal.fire({
      icon: 'error',
      title: 'Data Tidak Lengkap',
      text: 'Nama, email, dan nomor telepon wajib diisi!'
    });
    return;
  }

  try {
    const response = await axios.put('/user/profile', editForm.value);
    
    if (response.data) {
      userProfile.value = response.data;
      isEditMode.value = false;
      
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Profil berhasil diperbarui',
        timer: 1500,
        showConfirmButton: false
      });
    }
  } catch (error: any) {
    console.error('Error updating profile:', error);
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memperbarui',
      text: error.response?.data?.message || 'Terjadi kesalahan saat memperbarui profil'
    });
  }
}

function showPhotoUpload() {
  Swal.fire({
    title: 'Upload Foto Profil',
    text: 'Fitur upload foto akan segera hadir!',
    icon: 'info',
    confirmButtonColor: '#667eea'
  });
}

function showChangePassword() {
  Swal.fire({
    title: 'Ubah Password',
    html: `
      <input type="password" id="old-password" class="swal2-input" placeholder="Password Lama">
      <input type="password" id="new-password" class="swal2-input" placeholder="Password Baru">
      <input type="password" id="confirm-password" class="swal2-input" placeholder="Konfirmasi Password Baru">
    `,
    showCancelButton: true,
    confirmButtonText: 'Ubah Password',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#667eea',
    preConfirm: () => {
      const oldPassword = (document.getElementById('old-password') as HTMLInputElement)?.value;
      const newPassword = (document.getElementById('new-password') as HTMLInputElement)?.value;
      const confirmPassword = (document.getElementById('confirm-password') as HTMLInputElement)?.value;

      if (!oldPassword || !newPassword || !confirmPassword) {
        Swal.showValidationMessage('Semua field harus diisi!');
        return false;
      }

      if (newPassword !== confirmPassword) {
        Swal.showValidationMessage('Password baru tidak cocok!');
        return false;
      }

      if (newPassword.length < 6) {
        Swal.showValidationMessage('Password minimal 6 karakter!');
        return false;
      }

      return { oldPassword, newPassword };
    }
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await axios.post('/user/change-password', {
          old_password: result.value.oldPassword,
          new_password: result.value.newPassword
        });

        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Password berhasil diubah',
          timer: 1500,
          showConfirmButton: false
        });
      } catch (error: any) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: error.response?.data?.message || 'Gagal mengubah password'
        });
      }
    }
  });
}

// Lifecycle
onMounted(async () => {
  loading.value = true;
  
  try {
    // Ambil data profil
    const profileResponse = await axios.get('/user/profile');
    userProfile.value = profileResponse.data;

    // Ambil statistik pesanan
    try {
      const statsResponse = await axios.get('/user/orders/stats');
      userStats.value = statsResponse.data;
    } catch (error) {
      console.error('Error fetching stats:', error);
    }
  } catch (error: any) {
    console.error('Error fetching profile:', error);
    
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memuat Profil',
      text: 'Tidak dapat memuat data profil. Silakan coba lagi.',
      confirmButtonColor: '#667eea'
    }).then(() => {
      router.push({ name: 'beranda' });
    });
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.profile-container {
  min-height: 100vh;
  background: #f7f8fa;
  padding-bottom: 40px;
}

/* Header */
.profile-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 24px 20px;
  color: white;
  position: relative;
}

.btn-back {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  margin-bottom: 12px;
  transition: background 0.3s;
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.3);
}

.profile-header h1 {
  font-size: 28px;
  font-weight: bold;
}

/* Loading */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  gap: 16px;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e5e7eb;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  color: #667eea;
  font-size: 16px;
}

/* Content */
.profile-content {
  max-width: 1000px;
  margin: -40px auto 0;
  padding: 0 20px;
}

/* Profile Card */
.profile-card {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  text-align: center;
  margin-bottom: 24px;
}

.avatar-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 24px;
}

.avatar-circle {
  width: 120px;
  height: 120px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.avatar-icon {
  font-size: 56px;
  color: white;
}

.btn-change-photo {
  background: #f3f4f6;
  color: #667eea;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-change-photo:hover {
  background: #e5e7eb;
}

.profile-info-display h2 {
  font-size: 24px;
  color: #333;
  margin-bottom: 8px;
}

.user-email {
  color: #667eea;
  font-size: 16px;
  margin-bottom: 8px;
}

.user-phone {
  color: #666;
  font-size: 15px;
  margin-bottom: 8px;
}

.member-since {
  color: #999;
  font-size: 13px;
}

/* Info Section */
.info-section,
.password-section,
.stats-section {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  margin-bottom: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.section-header h3 {
  font-size: 20px;
  color: #333;
}

.btn-edit {
  background: #667eea;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-edit:hover {
  background: #5568d3;
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.info-item label {
  display: block;
  font-size: 13px;
  color: #999;
  margin-bottom: 6px;
  font-weight: 500;
}

.info-item p {
  font-size: 15px;
  color: #333;
  font-weight: 500;
}

/* Edit Form */
.edit-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 14px;
  color: #333;
  margin-bottom: 8px;
  font-weight: 500;
}

.form-control {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 15px;
  outline: none;
  transition: border-color 0.3s;
}

.form-control:focus {
  border-color: #667eea;
}

textarea.form-control {
  resize: vertical;
  font-family: inherit;
}

.form-actions {
  grid-column: 1 / -1;
  display: flex;
  gap: 12px;
  margin-top: 8px;
}

.btn-save {
  flex: 1;
  background: #667eea;
  color: white;
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-save:hover {
  background: #5568d3;
}

.btn-cancel {
  background: #f3f4f6;
  color: #666;
  border: none;
  padding: 14px 24px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-cancel:hover {
  background: #e5e7eb;
}

/* Password Section */
.btn-change-password {
  width: 100%;
  background: #f3f4f6;
  color: #667eea;
  border: 2px solid #667eea;
  padding: 14px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-change-password:hover {
  background: #667eea;
  color: white;
}

/* Stats Section */
.stats-section h3 {
  font-size: 20px;
  color: #333;
  margin-bottom: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.stat-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 16px;
  color: white;
}

.stat-icon {
  font-size: 36px;
}

.stat-info {
  flex: 1;
}

.stat-number {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
  .profile-content {
    margin-top: -20px;
  }

  .profile-card {
    padding: 24px 16px;
  }

  .info-section,
  .password-section,
  .stats-section {
    padding: 20px 16px;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .btn-edit {
    width: 100%;
  }

  .info-grid,
  .edit-form {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>