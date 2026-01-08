<template>
  <div class="page-container">
    <!-- Hero Section -->
    <div class="hero-section">
      <router-link v-if="!isLoggedIn" :to="{ name: 'sign-in' }" class="btn-login-top">
        🔑 Sign-in
      </router-link>

      <button v-else class="btn-login-top text-danger" @click="signOut">
        🚪 Logout
      </button>

      <div v-if="isLoggedIn">
        <button class="btn-profil-top" @click="goToProfile">
          <span class="profile-icon">👤</span>
          <span class="profile-name">
            {{ userProfile?.nama || "Profil Pelanggan" }}
          </span>
        </button>
      </div>


      <div class="icon">🏠</div>
      <h1>Selamat Datang di Beranda</h1>
      <p class="subtitle">Platform Laundry Terpercaya di Indonesia</p>

      <div class="hero-buttons">
        <button class="btn-primary"><a class="nav-link" href="#cari">Cari Laundry</a></button>
        <button class="btn-secondary" @click="confirmMitra">
          Daftar Jadi Mitra
        </button>
      </div>












    </div>

    <!-- Layanan untuk Pengguna -->
    <div class="service-section named" id="cari">
      <div class="content-wrapper">
        <div class="section-header">
          <h2>Layanan Laundry Pilihan</h2>
          <p class="section-desc">
            Temukan laundry terbaik di sekitar Anda dengan harga terjangkau dan pelayanan terpercaya.
          </p>
        </div>

        <!-- Filter & Search -->
        <div class="search-box">
          <input v-model="searchQuery" type="text" placeholder="Cari laundry berdasarkan lokasi..."
            class="search-input" />
          <button class="btn-search" @click="filterLaundry">🔍 Cari</button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <p>Memuat data laundry...</p>
        </div>

        <!-- Laundry Cards Grid -->
        <div v-else-if="filteredMitraList.length > 0" class="laundry-grid">
          <div v-for="m in filteredMitraList" :key="m.id" class="laundry-card">
            <!-- Header Image -->
            <div class="laundry-image">
              <div class="placeholder-img">🧺</div>
              <span class="laundry-badge" v-if="m.status_toko === 'buka'">✓ Buka</span>
              <span class="laundry-badge closed" v-else>✗ Tutup</span>
            </div>

            <!-- Card Info -->
            <div class="laundry-info">
              <h3>{{ m.nama_laundry }}</h3>
              <p class="location">📍 {{ m.alamat_laundry }}, Kecamatan {{ m.kecamatan?.nama }}</p>

              <!-- Layanan List -->
              <div v-if="m.jenis_layanan && m.jenis_layanan.length" class="layanan-list">
                <h4>Layanan Tersedia:</h4>
                <ul>
                  <li v-for="l in m.jenis_layanan" :key="l.id">
                    <strong>{{ l.nama_layanan }}</strong><br />
                    <span class="desc">{{ l.deskripsi }}</span><br />
                    <span class="price">Rp {{ Number(l.harga).toLocaleString('id-ID') }} / {{ l.satuan }}</span>
                  </li>
                </ul>
              </div>

              <div v-else class="no-service">
                <p>Belum ada layanan tersedia</p>
              </div>

              <!-- Action Button -->
              <!-- <router-link
  :to="m.status_toko === 'buka' ? { name: 'DetailLaundry', params: { id: m.id } } : null"
  @click="Logging"
  class="btn-order"
  :class="{ disabled: m.status_toko !== 'buka' }"
>
  {{ m.status_toko === 'buka' ? 'Pilih Laundry' : 'Tutup' }}
</router-link> -->


              <button class="btn-order" @click="goToLaundry(m.id)" :disabled="m.status_toko !== 'buka'">
                {{ m.status_toko === 'buka' ? 'Pilih Laundry' : 'Sedang Tutup' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Jika tidak ada data -->
        <div v-else class="no-data">
          <p>{{ searchQuery ? 'Tidak ditemukan laundry dengan lokasi tersebut.' : 'Belum ada laundry tersedia saat ini.'
          }}</p>
        </div>
      </div>
    </div>

    <!-- Keunggulan Layanan -->
    <div class="features-section">
      <div class="content-wrapper">
        <h2 class="text-center">Kenapa Pilih Kami?</h2>
        <div class="features-grid">
          <div class="feature-box" v-for="(item, idx) in features" :key="idx">
            <div class="feature-icon">{{ item.icon }}</div>
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
          </div>
        </div>
      </div>
      <!-- Cek Status Laundry -->
    </div>
  




<div class="cek-status-section">
  <div class="content-wrapper">
    <h2 class="text-center mt-20">Cek Status Laundry</h2>
    <p class="section-desc text-kecil" id="kecil">
      Masukkan kode order untuk mengetahui status laundry Anda
    </p>



    <p>Masukkan Kode Order</p>
    <div class="search-kode" id="kode">
      <input
        v-model="KodeOrder"
        type="text"
        placeholder="Contoh: ORD-123456"
        class="search-input"
        @keyup.enter="cekStatus"
      />

      <!-- <button class="btn-search" @click="cekStatus" :disabled="loading"> -->
        <button
  type="button"
  class="btn-search"
  @click="cekStatus"
>
        {{ loading ? "Mencari..." : "🔍 Cari" }}
      </button>
    </div>

    <div v-if="statusResult" class="status-result">
      <span class="status-badge">
        {{ statusResult }}
      </span>
    </div>
  </div>
</div>



  
  </div>
</template>

<script setup lang="ts">
import Swal from "sweetalert2";
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import axios from "axios";

const loadingLaundry = ref(false);
const loadingStatus = ref(false);



const kodeOrder = ref("");
const statusResult = ref<string | null>(null);
  

async function cekStatus() {
  if (!kodeOrder.value) {
    Swal.fire("Oops", "Kode order tidak boleh kosong", "warning");
    return;
  }

  try {
    const res = await axios.get(`/cek-status/${kodeOrder.value}`);
    statusResult.value = res.data.status_label;
  } catch (e) {
    statusResult.value = null;
    Swal.fire("Tidak ditemukan", "Kode order tidak valid", "error");
  }
}


const router = useRouter();
const authStore = useAuthStore();

// State
const mitraList = ref([]);
const searchQuery = ref('');
const loading = ref(false);
// const laundryId = route.params.id;

// Computed
const isLoggedIn = computed(() => authStore.isAuthenticated);

const filteredMitraList = computed(() => {
  if (!searchQuery.value) {
    return mitraList.value;
  }

  return mitraList.value.filter(m =>
    m.nama_laundry.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    m.alamat_laundry.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    m.kecamatan?.nama?.toLowerCase().includes(q)
  );
});

// Features data
const features = ref([
  { icon: "🚚", title: "Antar Jemput", desc: "Gratis antar jemput area tertentu" },
  { icon: "⚡", title: "Express Service", desc: "Selesai dalam 3 jam" },
  { icon: "💳", title: "Pembayaran Mudah", desc: "Berbagai metode pembayaran" },
  { icon: "🛡️", title: "Garansi Aman", desc: "Jaminan ganti rugi" },
]);

// Methods
function goToLaundry(id: number) {
  console.log("id", id)
  router.push({ name: "DetailLaundry", params: { id: id } });
}

function goToProfile() {
  // console.log("id", id)
  router.push({ name: "pelanggan.profil-pelanggan" });
}




const alamat = ref('')
const kecamatan_id = ref<number | null>(null)
const kecamatans = ref([])
onMounted(async () => {
  const res = await axios.get('kecamatan')
  kecamatans.value = res.data
})

watch(alamat, async (val) => {
  if (val.length < 10) return

  const res = await axios.post('/deteksi-kecamatan', {
    alamat: val
  })

  if (res.data?.id) {
    kecamatan_id.value = res.data.id
  }
})











// function goToLaundry(id: number) {
//   // Arahkan ke LaundryDetail.vue dengan mengirim ID mitra
//   router.push({
//     name: 'DetailLaundry',
//     params: { id: id.toString() }
//   });
// }

function filterLaundry() {
  // Filter sudah otomatis via computed property
  console.log('Filtering with query:', searchQuery.value);
}

const signOut = () => {
  Swal.fire({
    icon: "warning",
    text: "Apakah Anda yakin ingin keluar?",
    showCancelButton: true,
    confirmButtonText: "Ya, Keluar",
    cancelButtonText: "Batal",
    reverseButtons: true,
    buttonsStyling: false,
    customClass: {
      confirmButton: "btn fw-semibold btn-light-primary",
      cancelButton: "btn fw-semibold btn-light-danger",
    },
  }).then(async (result) => {
    if (result.isConfirmed) {
      await authStore.logout();
      Swal.fire({
        icon: "success",
        text: "Berhasil keluar",
        timer: 1500,
        showConfirmButton: false,
      }).then(() => {
        router.push({ name: "beranda" });
      });
    }
  });
};

const confirmMitra = () => {
  Swal.fire({
    title: "Daftar Jadi Mitra?",
    text: "Kamu akan diarahkan ke halaman login untuk mendaftar sebagai mitra.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#667eea",
    cancelButtonColor: "#aaa",
    confirmButtonText: "Ya, lanjutkan",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      router.push("/sign-up");
    }
  });
};




// Lifecycle
onMounted(async () => {
  loading.value = true;
  try {
    const response = await axios.get('/mitra');
    mitraList.value = response.data;
    console.log('Data mitra berhasil dimuat:', mitraList.value);
  } catch (error) {
    console.error('Error fetching mitra:', error);
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memuat Data',
      text: 'Tidak dapat memuat data laundry. Silakan coba lagi.',
    });
  } finally {
    loading.value = false;
  }
});
</script>

<style>

#kode {
  display: flex;
  gap: 12px;
  max-width: 600px;
  margin: 0 auto 48px;
  margin-top: 60px;
}



.cek-status-section {
  padding: 80px 20px;
  /* background: linear-gradient(135deg, #d0e4fd 0%, #eef2f6 100%); */
  position: relative;
  overflow: hidden;
  min-height: 400px;
}

.cek-status-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 500px;
  height: 500px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  z-index: 0;
}

.cek-status-section::after {
  content: '';
  position: absolute;
  bottom: -30%;
  left: -5%;
  width: 400px;
  height: 400px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 50%;
  z-index: 0;
}

.cek-status-section .content-wrapper {
  position: relative;
  z-index: 1;
  max-width: 700px;
  margin: 0 auto;
}

.cek-status-section h2 {
  color: #667eea;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 15px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.cek-status-section .section-desc {
  color: #667eea;
  font-size: 1rem;
  margin-bottom: 40px;
  opacity: 0.9;
  line-height: 1.6;
}

.cek-box {
  display: flex;
  align-items: center;
  gap: 15px;
  margin: 0 auto;
}
#box{
    flex: 1;
    padding: 14px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: border-color 0.3s;

}

.cek-input {
  flex: 1;
  padding: 16px 24px;
  font-size: 1rem;
  border: 1px solid #d0d0d0;
  border-radius: 12px;
  background: #ffffff;
  color: #666;
  transition: all 0.3s ease;
  outline: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.cek-input:focus {
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.cek-input::placeholder {
  color: #999;
}

.btn-cek {
  padding: 16px 40px;
  font-size: 1rem;
  font-weight: 600;
  background: #667eea;
  color: #ffffff;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-cek:hover {
  background: #5568d3;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}


.btn-cek::before {
  content: '🔍';
  font-size: 1.1rem;
}

.status-result {
  text-align: center;
  margin-top: 30px;
  animation: fadeInUp 0.5s ease;
}

.status-badge {
  display: inline-block;
  padding: 15px 35px;
  background: #ffffff;
  color: #667eea;
  font-size: 1.2rem;
  font-weight: 700;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  border: 2px solid #667eea;
  animation: pulse 2s infinite;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .cek-status-section {
    padding: 60px 20px;
  }

  .cek-status-section h2 {
    font-size: 2rem;
    margin-bottom: 12px;
  }

  .cek-status-section .section-desc {
    font-size: 0.95rem;
    margin-bottom: 30px;
  }

  .cek-status-box {
    flex-direction: column;
    gap: 12px;
  }

  .cek-input {
    width: 100%;
    padding: 14px 20px;
  }

  .btn-cek {
    width: 100%;
    padding: 14px 30px;
    justify-content: center;
  }

  .status-badge {
    padding: 12px 28px;
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .cek-status-section h2 {
    font-size: 1.75rem;
  }
}
/* .btn-profile {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  height: 40px;
  width: 105%;

}

.btn-profile:hover {
  background: #6c80d9;
} */




.btn-profil-top {
  position: fixed;
  top: 20px;
  left: 30px;
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  font-size: 14px;
  z-index: 1000;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  height: 40px;
  width: 150px;
}

.btn-profil-top:hover {
  background: #8699ef;
  color: white;
}

.text-blue {
  color: #8cbbdd;
  border-color: #dc3545;
}

.text-blue:hover {
  background: #db9298;
  color: white;
}








html {
  scroll-behavior: smooth;
}
</style>

<style scoped>
.disabled {
  pointer-events: none;
  opacity: 0.5;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.page-container {
  min-height: 100vh;
  background: #f7f7f7;
}

/* Login Button */
.btn-login-top {
  position: fixed;
  top: 20px;
  right: 30px;
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  font-size: 14px;
  z-index: 1000;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-login-top:hover {
  background: #6b7fda;
  color: white;
}


.text-danger {
  color: #dc3545;
  border-color: #dc3545;
}

.text-danger:hover {
  background: #db9298;
  color: white;
}

/* Layout */
.content-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.text-center {
  text-align: center;
  font-size: 30px;
  font-weight: bold;
  color: #333;
  margin-bottom: 48px;
}
.text-kecil {
  text-align: center;
  font-size: 18px;
    color: #666;
  max-width: 800px;
  margin: 0 auto;
  line-height: 1.6;
}
#kecil {
  margin-top: -50px;
}

/* Hero Section */
.hero-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 20px;
  text-align: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  position: relative;
}

.icon {
  font-size: 64px;
  margin-bottom: 20px;
}

.hero-section h1 {
  font-size: 42px;
  font-weight: bold;
  margin-bottom: 12px;
}

.subtitle {
  font-size: 20px;
  margin-bottom: 32px;
  opacity: 0.9;
}

.hero-buttons {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  justify-content: center;
}

.btn-primary,
.btn-secondary {
  padding: 14px 36px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: white;
  color: #667eea;
  border: none;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.btn-primary a {
  text-decoration: none;
  color: inherit;
}

.btn-secondary {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Service Section */
.service-section {
  background: white;
  padding: 64px 20px;
}

.section-header {
  text-align: center;
  margin-bottom: 48px;
}

.section-header h2 {
  font-size: 32px;
  font-weight: bold;
  color: #333;
  margin-bottom: 16px;
}

.section-desc {
  color: #666;
  max-width: 800px;
  margin: 0 auto;
  line-height: 1.6;
}

/* Search Box */
.search-box {
  display: flex;
  gap: 12px;
  max-width: 600px;
  margin: 0 auto 48px;
}

.search-input {
  flex: 1;
  padding: 14px 20px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
  outline: none;
  transition: border-color 0.3s;
}

.search-input:focus {
  border-color: #667eea;
}

.btn-search {
  background: #667eea;
  color: white;
  padding: 14px 28px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-search:hover {
  background: #5568d3;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: #667eea;
  font-size: 18px;
  font-weight: 500;
}

/* Laundry Grid */
.laundry-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.laundry-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  display: flex;
  flex-direction: column;
}

.laundry-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

/* Card Header */
.laundry-image {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.placeholder-img {
  font-size: 64px;
}

.laundry-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: white;
  color: #16a34a;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: bold;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.laundry-badge.closed {
  color: #dc3545;
}

/* Card Body */
.laundry-info {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.laundry-info h3 {
  font-size: 20px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.laundry-info .location {
  color: #666;
  font-size: 14px;
  margin-bottom: 16px;
  display: block;
}

/* Layanan List */
.layanan-list {
  margin-bottom: 16px;
  flex: 1;
}

.layanan-list h4 {
  font-size: 15px;
  color: #667eea;
  margin-bottom: 12px;
  font-weight: 600;
}

.layanan-list ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.layanan-list li {
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  line-height: 1.6;
}

.layanan-list li:last-child {
  border-bottom: none;
}

.layanan-list li strong {
  color: #333;
  font-size: 15px;
}

.layanan-list li .desc {
  color: #666;
  font-size: 13px;
}

.layanan-list li .price {
  color: #667eea;
  font-weight: 600;
  font-size: 14px;
}

.no-service {
  text-align: center;
  padding: 20px;
  color: #999;
  font-style: italic;
  flex: 1;
}

/* Order Button */
.btn-order {
  width: 100%;
  background: #667eea;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
  margin-top: auto;
}

.btn-order:hover:not(:disabled) {
  background: #5568d3;
}

.btn-order:disabled {
  background: #ccc;
  cursor: not-allowed;
  opacity: 0.6;
}

/* No Data State */
.no-data {
  text-align: center;
  padding: 80px 20px;
  color: #999;
  font-size: 16px;
}

/* Features Section */
.features-section {
  background: #f9fafb;
  padding: 64px 20px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 32px;
}

.feature-box {
  text-align: center;
  padding: 24px;
}

.feature-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.feature-box h3 {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.feature-box p {
  color: #666;
  font-size: 14px;
  line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-section h1 {
    font-size: 28px;
  }

  .subtitle {
    font-size: 16px;
  }

  .section-header h2,
  .text-center {
    font-size: 24px;
  }

  .search-box {
    flex-direction: column;
  }

  .laundry-grid {
    grid-template-columns: 1fr;
  }

  .btn-login-top {
    top: 10px;
    right: 10px;
    padding: 8px 16px;
    font-size: 13px;
  }
}

@media (max-width: 480px) {
  .hero-section h1 {
    font-size: 24px;
  }

  .hero-buttons {
    flex-direction: column;
    width: 100%;
  }

  .btn-primary,
  .btn-secondary {
    width: 100%;
  }
}
</style>