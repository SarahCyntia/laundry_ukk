<template>
  <div>
    <div v-if="loading" class="text-center py-10">
      <div class="spinner-border"></div>
    </div>

    <div v-else class="profile-wrapper">
      <div class="profile-header">
        <img :src="mitra.foto_toko ? '/storage/' + mitra.foto_toko : '/img/default.png'" class="profile-photo" />

        <div class="profile-info">
          <h2 class="profile-name">{{ mitra.nama_laundry }}</h2>
          <span class="status-badge" :class="{
            accepted: mitra.status_validasi === 'diterima',
            rejected: mitra.status_validasi === 'ditolak',
            pending: mitra.status_validasi === 'menunggu',
          }">
            {{ mitra.status_validasi?.toUpperCase() }}
          </span>
          <p class="small text-muted mt-2">ID Mitra: {{ mitra.id }}</p>
        </div>
      </div>

      <div class="card detail-card shadow-sm">
        <h4 class="section-title">Informasi Pemilik</h4>
        <div class="grid-info">
          <div>
            <span class="info-label">Nama Pemilik</span>
            <p class="info-value">{{ mitra.user?.name ?? '-' }}</p>
          </div>
          <div>
            <span class="info-label">Email</span>
            <p class="info-value">{{ mitra.user?.email ?? '-' }}</p>
          </div>
          <div>
            <span class="info-label">No HP</span>
            <p class="info-value">{{ mitra.user?.phone ?? '-' }}</p>
          </div>
          <div>
            <span class="info-label">Status Toko</span>
            <span class="badge-store" :class="{
              buka: mitra.status_toko === 'buka',
              tutup: mitra.status_toko === 'tutup',
            }">
              {{ mitra.status_toko?.toUpperCase() ?? 'TUTUP' }}
            </span>
          </div>
          <div>
            <span class="info-label">jam Buka</span>
            <p class="info-value">{{ mitra.jam_buka ?? '-' }}</p>
          </div>
          <div>
            <span class="info-label">jam Tutup</span>
            <p class="info-value">{{ mitra.jam_tutup ?? '-' }}</p>
          </div>
        </div>

        <hr />
        <!-- <h4 class="section-title">Alamat Laundry</h4>
        <p class="info-value">{{ mitra.alamat_laundry }}</p> -->
        <h4 class="section-title">Alamat Laundry</h4>
        <p class="info-value">
          {{ mitra.alamat_laundry }},
          Kecamatan {{ mitra.kecamatan?.nama }}
        </p>

      </div>

      <button class="btn btn-edit" @click="editMitra">✎ Edit Profil</button>

      <div v-if="openForm" class="modal-overlay">
        <div class="modal-box">
          <Form :selected="selected" @close="openForm = false" @refresh="getMitra" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axios from '@/libs/axios';
import Form from './form.vue';

const loading = ref(true);
const mitra = ref<any>({});
const selected = ref<string | number>('');
const openForm = ref(false);

onMounted(() => getMitra());

const getMitra = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/profile'); // pakai GET sesuai controller index
    mitra.value = res.data.data;
  } catch (e) {
    console.error('Gagal memuat mitra:', e);
  } finally {
    loading.value = false;
  }
};
const form = ref({
  nama_laundry: '',
  alamat_laundry: '',
  kecamatan_id: null,
  status_toko: '',
  jam_buka: '',
  jam_tutup: '',
  deskripsi: '',
  name: '',
  email: '',
  phone: '',
  foto_toko: null,
})



const editMitra = () => {
  if (!mitra.value.id) return alert('ID mitra tidak ditemukan!');
  selected.value = mitra.value.id;
  openForm.value = true;
};
const props = defineProps<{
  selected: string | number | null
}>()

</script>


<style scoped>
.profile-wrapper {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
}

.profile-header {
  background: white;
  display: flex;
  gap: 20px;
  padding: 25px;
  border-radius: 20px;
  align-items: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  margin-bottom: 25px;
}

.profile-photo {
  width: 130px;
  height: 130px;
  border-radius: 20px;
  object-fit: cover;
  border: 4px solid #f0f0f0;
}

.profile-name {
  font-size: 26px;
  font-weight: bold;
  margin: 0;
}

.status-badge {
  padding: 6px 10px;
  border-radius: 15px;
  font-weight: bold;
  font-size: 12px;
}

.status-badge.accepted {
  background: #d1f7d6;
  color: #1a8a2b;
}

.status-badge.rejected {
  background: #ffd7d7;
  color: #d93025;
}

.status-badge.pending {
  background: #fff5c2;
  color: #a58500;
}

.detail-card {
  background: white;
  border-radius: 20px;
  padding: 30px;
}

.section-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 15px;
}

.grid-info {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.info-label {
  font-size: 12px;
  font-weight: bold;
  color: #888;
}

.info-value {
  font-size: 15px;
  color: #333;
  margin-top: 3px;
}

.badge-store {
  padding: 6px 12px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: bold;
}

.badge-store.buka {
  background: #c8f8d6;
  color: #1a8a2b;
}

.badge-store.tutup {
  background: #ffd9d9;
  color: #d93025;
}

.btn-edit {
  background: #3b82f6;
  color: white;
  padding: 12px 20px;
  border-radius: 12px;
  margin-top: 20px;
  display: inline-block;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-box {
  background: white;
  padding: 20px;
  border-radius: 15px;
  width: 600px;
  max-width: 95%;
}
</style>