<template>
  <div class="detail-container">
    <!-- Back Button -->
    <div class="back-button-wrapper">
      <button class="btn-back" @click="router.back()">
        ← Kembali
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <p>Memuat detail laundry...</p>
    </div>

    <!-- Detail Content -->
    <div v-else-if="mitra" class="detail-content">
      <!-- Header Section -->
      <div class="header-section">
        <div class="header-image">
          <div class="placeholder-img">🧺</div>
        </div>
        <div class="header-info">
          <h1>{{ mitra.nama_laundry }}</h1>
          <p class="address">📍 {{ mitra.alamat_laundry }}, {{ mitra.kecamatan?.nama }}</p>
          <div class="status-badge" :class="{ open: mitra.status_toko === 'buka' }">
            {{ mitra.status_toko === 'buka' ? '✓ Buka' : '✗ Tutup' }}
          </div>
        </div>
      </div>
      <!-- Services Section -->
      <div class="services-section">
        <h2>Layanan Tersedia</h2>
        
        <div v-if="mitra.jenis_layanan && mitra.jenis_layanan.length" class="services-grid">
          <div 
            v-for="layanan in mitra.jenis_layanan" 
            :key="layanan.id" 
            class="service-card"
            :class="{ selected: selectedLayanan === layanan.id }"
            @click="selectLayanan(layanan)"
          >
            <div class="service-icon">🧼</div>
            <h3>{{ layanan.nama_layanan }}</h3>
            <p class="service-desc">{{ layanan.deskripsi }}</p>
            <div class="service-price">
              <span class="price">Rp {{ Number(layanan.harga).toLocaleString('id-ID') }}</span>
              <span class="unit">/ {{ layanan.satuan }}</span>
            </div>
            <div v-if="selectedLayanan === layanan.id" class="selected-check">✓</div>
          </div>
        </div>

        <div v-else class="no-services">
          <p>Belum ada layanan tersedia</p>
        </div>
      </div>

      <!-- Order Form -->
      <div v-if="selectedLayanan" class="order-section">
        <h2>Detail Pesanan</h2>
        
        <div class="order-form">
          <div class="form-group">
            <label>Estimasi Berat (kg)</label>
            <input 
              v-model.number="beratEstimasi" 
              type="number" 
              step="0.5"
              min="0.5" 
              placeholder="Contoh: 5"
              class="form-control"
            />
            <small class="form-hint">Berat aktual akan ditimbang saat Anda antar cucian</small>
          </div>

          <div class="form-group">
            <label>Nama Pemesan</label>
            <input 
              v-model="namaPemesan" 
              type="text" 
              placeholder="Masukkan nama Anda"
              class="form-control"
              disabled
            />
            <small class="form-hint">Nama diambil dari akun Anda</small>
          </div>

          <div class="form-group">
            <label>Nomor Telepon</label>
            <input 
              v-model="noTelepon" 
              type="tel" 
              placeholder="Contoh: 08123456789"
              class="form-control"
            />
          </div>

          <div class="form-group">
            <label>Catatan (Opsional)</label>
            <textarea 
              v-model="catatan" 
              placeholder="Catatan khusus untuk pesanan Anda, misalnya: pisahkan pakaian putih, jangan pakai pewangi, dll."
              rows="3"
              class="form-control"
            ></textarea>
          </div>

          <!-- Order Summary -->
          <div class="order-summary">
            <h3>Ringkasan Pesanan</h3>
            <div class="summary-row">
              <span>Layanan:</span>
              <span>{{ getSelectedLayananName() }}</span>
            </div>
            <div class="summary-row">
              <span>Estimasi Berat:</span>
              <span>{{ beratEstimasi }} kg</span>
            </div>
            <div class="summary-row">
              <span>Harga/kg:</span>
              <span>Rp {{ getSelectedLayananPrice().toLocaleString('id-ID') }}</span>
            </div>
            <div class="summary-row total">
              <span>Estimasi Total:</span>
              <span>Rp {{ calculateTotal().toLocaleString('id-ID') }}</span>
            </div>
            <small class="summary-note">
              * Total akhir akan disesuaikan dengan berat aktual saat Anda antar cucian
            </small>
          </div>

<button class="submit-button" @click="openPaymentModal">
  Buat Pesanan
</button>


          <!-- <button 
            class="btn-order" 
            @click="submitOrder"
            :disabled="!canSubmit"
          >
            Buat Pesanan
          </button> -->
        </div>
      </div>

      <!-- Info Section -->
      <div class="info-section">
        <h2>Cara Pesan</h2>
        <div class="info-grid">
          <div class="info-card">
            <div class="info-icon">📝</div>
            <h4>1. Pesan Online</h4>
            <p>Pilih layanan dan isi form pemesanan di halaman ini</p>
          </div>
          <div class="info-card">
            <div class="info-icon">🚶</div>
            <h4>2. Antar Cucian</h4>
            <p>Datang langsung ke laundry dan serahkan cucian Anda</p>
          </div>
          <div class="info-card">
            <div class="info-icon">⚙️</div>
            <h4>3. Proses</h4>
            <p>Mitra akan memproses cucian sesuai layanan yang dipilih</p>
          </div>
          <div class="info-card">
            <div class="info-icon">✅</div>
            <h4>4. Ambil</h4>
            <p>Cucian siap diambil sesuai estimasi waktu yang diberikan</p>
          </div>
        </div>

        <div class="additional-info">
          <h3>Informasi Tambahan</h3>
          <div class="info-list">
            <div class="info-item">
              <span class="info-label">⏰ Jam Operasional:</span>
              <span>Senin - Sabtu: 08:00 - 20:00 | Minggu: 09:00 - 18:00</span>
            </div>
            <div class="info-item">
              <span class="info-label">💳 Metode Pembayaran:</span>
              <span>Cash, Transfer Bank, E-Wallet (GoPay, OVO, DANA)</span>
            </div>
            <div class="info-item">
              <span class="info-label">⚡ Express Service:</span>
              <span>Tersedia layanan kilat selesai dalam 3-6 jam</span>
            </div>
            <div class="info-item">
              <span class="info-label">🧼 Minimal Order:</span>
              <span>Minimal 1 kg untuk semua layanan</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="error-state">
      <p>Data laundry tidak ditemukan</p>
      <button class="btn-back" @click="router.push('/')">
        Kembali ke Beranda
      </button>
    </div>
  </div>



<div v-if="showPaymentModal" class="payment-modal">
  <div class="payment-box">
    <h3>Pilih Metode Pembayaran</h3>

    <label class="payment-option">
      <input type="radio" value="manual" v-model="selectedPayment" />
      Transfer Manual
    </label>

    <label class="payment-option">
      <input type="radio" value="cod" v-model="selectedPayment" />
      Bayar di Tempat
    </label>

    <label class="payment-option">
      <input type="radio" value="midtrans" v-model="selectedPayment" />
      Midtrans (QRIS/VA)
    </label>

    <button class="submit-button" @click="submitForm">
      Lanjutkan
    </button>
  </div>
</div>








</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from "@/libs/axios";
import Swal from 'sweetalert2';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();



const openPaymentModal = () => {
  showPaymentModal.value = true;
};






const props = defineProps<{ id: string }>();
console.log(props.id)


watch(() => authStore.user, (val) => {
  if (val && val.pelanggan) {
    noTelepon.value = val.pelanggan.no_telepon;
  }
});


onMounted(() => {
  const user = JSON.parse(localStorage.getItem("user"));
  
  if (user && user.role === "pelanggan" && user.pelanggan) {
    namaPemesan.value = user.pelanggan.nama;
  }
});
// localStorage.setItem("user", JSON.stringify(response.data.user));

// State
const mitra = ref(null);
const loading = ref(false);
const selectedLayanan = ref(null);
const beratEstimasi = ref(1);
const namaPemesan = ref('');
// const noTelepon = ref('');
const noTelepon = ref(authStore.user?.pelanggan?.no_telepon || "");
const catatan = ref('');
const showPaymentModal = ref(false);
const selectedPayment = ref(null);


// Computed
const canSubmit = computed(() => {
  return selectedLayanan.value && 
         beratEstimasi.value > 0 && 
         noTelepon.value.trim() !== '';
});



const pelangganId = computed(() => authStore.user?.pelanggan?.id ?? authStore.user?.id);


// const pelangganId = computed(() => {
//   return authStore.user?.id || null;
// });




// Methods
function selectLayanan(jenis_layanan) {
  selectedLayanan.value = jenis_layanan.id;
}

function getSelectedLayananName() {
  if (!mitra.value || !selectedLayanan.value) return '';
  const layanan = mitra.value.jenis_layanan.find(l => l.id === selectedLayanan.value);
  return layanan ? layanan.nama_layanan : '';
}

function getSelectedLayananPrice() {
  if (!mitra.value || !selectedLayanan.value) return 0;
  const layanan = mitra.value.jenis_layanan.find(l => l.id === selectedLayanan.value);
  return layanan ? Number(layanan.harga) : 0;
}

function calculateTotal() {
  return getSelectedLayananPrice() * beratEstimasi.value;
}






function generateOrderCode() {
  return `ORD-${Date.now()}`;
}

function buildConfirmationHTML(layanan) {
  return `
    <div style="text-align: left; padding: 10px;">
      <p><strong>Laundry:</strong> ${mitra.value.nama_laundry}</p>
      <p><strong>Alamat:</strong> ${mitra.value.alamat_laundry}</p>

      <hr style="margin: 12px 0;">

      <p><strong>Layanan:</strong> ${layanan.nama_layanan}</p>
      <p><strong>Estimasi Berat:</strong> ${beratEstimasi.value} kg</p>
      <p><strong>Estimasi Total:</strong> Rp ${calculateTotal().toLocaleString(
        "id-ID"
      )}</p>

      <hr style="margin: 12px 0;">

      <p><strong>No. Telepon:</strong> ${noTelepon.value}</p>
      ${
        catatan.value
          ? `<p><strong>Catatan:</strong> ${catatan.value}</p>`
          : ""
      }

      <hr style="margin: 12px 0;">

      <p style="color: #667eea; font-weight: 600; margin-top: 12px;">
        ✓ Silakan antar cucian Anda ke alamat laundry<br>
        ✓ Berat aktual akan ditimbang saat Anda datang<br>
        ✓ Pesanan menunggu konfirmasi dari mitra
      </p>
    </div>
  `;
}
function buildSuccessHTML(kodeOrder) {
  return `
    <p><strong>Kode Order: ${kodeOrder}</strong></p>
    <p style="margin-top: 12px;">Status: 
      <span style="color: #f59e0b; font-weight: 600;">
        Menunggu Konfirmasi Mitra
      </span>
    </p>

    <hr style="margin: 16px 0;">

    <p style="margin-top: 12px;"><strong>Langkah selanjutnya:</strong></p>
    <ol style="text-align: left; padding-left: 20px; margin-top: 8px;">
      <li>Tunggu konfirmasi dari mitra</li>
      <li>Jika diterima, antar cucian ke: <strong>${mitra.value.nama_laundry}</strong></li>
      <li>Alamat: ${mitra.value.alamat_laundry}</li>
      <li>Kecamatan: ${mitra.value.kecamatan?.nama || "-"}</li>
      <li>Sebutkan kode order: <strong>${kodeOrder}</strong></li>
      <li>Cucian akan ditimbang</li>
      <li>Pembayaran setelah total akhir keluar</li>
    </ol>

    <p style="margin-top: 16px; color: #667eea; font-weight: 600;">
      💡 Anda bisa memantau status di halaman Riwayat Transaksi
    </p>
  `;
}



async function submitOrder() {
  if (!canSubmit.value) return;

  // Pastikan user login
  if (!pelangganId.value) {
    return Swal.fire({
      icon: "warning",
      title: "Login Diperlukan",
      text: "Silakan login terlebih dahulu untuk membuat pesanan.",
      confirmButtonColor: "#667eea",
    }).then(() => router.push("/sign-in"));
  }

  const layanan = mitra.value.jenis_layanan.find(
    (l) => l.id === selectedLayanan.value
  );

  // Modal konfirmasi awal
  Swal.fire({
    title: "Konfirmasi Pesanan",
    html: buildConfirmationHTML(layanan),
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#667eea",
    cancelButtonColor: "#aaa",
    confirmButtonText: "Ya, Buat Pesanan",
    cancelButtonText: "Batal",
  }).then(async (result) => {
    if (!result.isConfirmed) return;

    try {
      const kodeOrder = generateOrderCode();

      const orderData = {
        pelanggan_id: pelangganId.value,
        mitra_id: mitra.value.id,
        jenis_layanan_id: selectedLayanan.value,
        kode_order: kodeOrder,
        berat_estimasi: beratEstimasi.value,
        berat_aktual: null,
        harga_final: null,
        catatan: catatan.value || null,
        status: "menunggu_konfirmasi_mitra",
        no_telepon: noTelepon.value,
      };

      console.log("Order data:", orderData);

      await axios.post("/order/store", orderData);

      Swal.fire({
        icon: "success",
        title: "Pesanan Berhasil Dibuat!",
        html: buildSuccessHTML(kodeOrder),
        confirmButtonColor: "#667eea",
        confirmButtonText: "Mengerti",
      })
      // }).then(() => router.push("/RiwayatTransaksi"));

    } catch (error) {
      console.error("Error submitting order:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text:
          error.response?.data?.message ||
          "Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.",
      });
    }
  });
}












//lama
// async function submitOrder() {
//   if (!canSubmit.value) return;

//   // Cek apakah user sudah login
//   if (!pelangganId.value) {
//     Swal.fire({
//       icon: 'warning',
//       title: 'Login Diperlukan',
//       text: 'Silakan login terlebih dahulu untuk membuat pesanan.',
//       confirmButtonColor: '#667eea',
//     }).then(() => {
//       router.push('/sign-in');
//     });
//     return;
//   }

//   const layanan = mitra.value.jenis_layanan.find(l => l.id === selectedLayanan.value);
  
//   Swal.fire({
//     title: 'Konfirmasi Pesanan',
//     html: `
//       <div style="text-align: left; padding: 10px;">
//         <p><strong>Laundry:</strong> ${mitra.value.nama_laundry}</p>
//         <p><strong>Alamat:</strong> ${mitra.value.alamat_laundry}</p>
//         <hr style="margin: 12px 0;">
//         <p><strong>Layanan:</strong> ${layanan.nama_layanan}</p>
//         <p><strong>Estimasi Berat:</strong> ${beratEstimasi.value} kg</p>
//         <p><strong>Estimasi Total:</strong> Rp ${calculateTotal().toLocaleString('id-ID')}</p>
//         <hr style="margin: 12px 0;">
//         <p><strong>No. Telepon:</strong> ${noTelepon.value}</p>
//         ${catatan.value ? `<p><strong>Catatan:</strong> ${catatan.value}</p>` : ''}
//         <hr style="margin: 12px 0;">
//         <p style="color: #667eea; font-weight: 600; margin-top: 12px;">
//           ✓ Silakan antar cucian Anda ke alamat laundry di atas<br>
//           ✓ Berat aktual akan ditimbang saat Anda datang<br>
//           ✓ Pesanan menunggu konfirmasi dari mitra
//         </p>
//       </div>
//     `,
//     icon: 'question',
//     showCancelButton: true,
//     confirmButtonColor: '#667eea',
//     cancelButtonColor: '#aaa',
//     confirmButtonText: 'Ya, Buat Pesanan',
//     cancelButtonText: 'Batal',
//   }).then(async (result) => {
//     if (result.isConfirmed) {
//       try {
//         // Generate kode order (bisa dihandle di backend)
//         const kodeOrder = `ORD-${Date.now()}`;

//         // Kirim data pesanan ke backend sesuai struktur tabel order
//         const orderData = {
//           pelanggan_id: pelangganId.value,
//           mitra_id: mitra.value.id,
//           jenis_layanan_id: selectedLayanan.value,
//           kode_order: kodeOrder,
//           berat_estimasi: beratEstimasi.value,
//           berat_aktual: null, // Akan diisi saat pelanggan antar cucian
//           harga_final: null, // Akan diisi saat berat aktual sudah ada
//           catatan: catatan.value || null,
//           status: 'menunggu_konfirmasi_mitra',
//           no_telepon: noTelepon.value,
//         };

//         console.log('Order data:', orderData);

//         // Uncomment saat API siap
//         const response = await axios.post('/order', orderData);
        
//         // Simulasi success (hapus saat API siap)
//         // await new Promise(resolve => setTimeout(resolve, 1000));
//         //<li>Alamat: ${mitra.value.alamat_laundry.kecamatan}</li>
//         Swal.fire({
//           icon: 'success',
//           title: 'Pesanan Berhasil Dibuat!',
//           html: `
//             <p><strong>Kode Order: ${kodeOrder}</strong></p>
//             <p style="margin-top: 12px;">Status: <span style="color: #f59e0b; font-weight: 600;">Menunggu Konfirmasi Mitra</span></p>
//             <hr style="margin: 16px 0;">
//             <p style="margin-top: 12px;"><strong>Langkah selanjutnya:</strong></p>
//             <ol style="text-align: left; padding-left: 20px; margin-top: 8px;">
//               <li>Tunggu konfirmasi dari mitra</li>
//               <li>Jika diterima, antar cucian ke: <strong>${mitra.value.nama_laundry}</strong></li>
//               <li>Alamat: ${mitra.value.alamat_laundry}</li>
// <li>Kecamatan: ${mitra.value.kecamatan?.nama || '-'}</li>

//               <li>Sebutkan kode order: <strong>${kodeOrder}</strong></li>
//               <li>Cucian akan ditimbang (berat aktual)</li>
//               <li>Bayar sesuai total akhir</li>
//               <li>Ambil kembali setelah selesai</li>
//             </ol>
//             <p style="margin-top: 16px; color: #667eea; font-weight: 600;">
//               💡 Anda dapat memantau status pesanan di halaman Riwayat Transaksi
//             </p>
//           `,
//           confirmButtonColor: '#667eea',
//           confirmButtonText: 'Mengerti',
//         }).then(() => {
//           // Redirect ke halaman riwayat transaksi
//           router.push('/RiwayatTransaksi');
//           // Atau ke beranda
//           // router.push('/');
//         });
//       } catch (error) {
//         console.error('Error submitting order:', error);
//         Swal.fire({
//           icon: 'error',
//           title: 'Gagal',
//           text: error.response?.data?.message || 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.',
//         });
//       }
//     }
//   });
// }

// Lifecycle
onMounted(async () => {
  const mitraId = route.params.id;
  
  if (!mitraId) {
    router.push('/');
    return;
  }

  loading.value = true;
  try {
    // Request ini otomatis include Authorization header
    const response = await axios.get(`/mitraa/${mitraId}`);
    mitra.value = response.data;
    
    // Set nama pemesan dari user yang login
    if (authStore.user) {
      namaPemesan.value = authStore.user.name || authStore.user.nama_pelanggan || '';
    }
  } catch (error) {
    console.error('Error fetching mitra detail:', error);
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memuat Data',
      text: error.response?.data?.message || 'Tidak dapat memuat detail laundry.',
    });
  } finally {
    loading.value = false;
  }
});
// onMounted(async () => {
//   const mitraId = route.params.id;
//   console.log(route.params);
//   // const mitraId = props.id;

  
//   if (!mitraId) {
//     router.push('/');
//     return;
//   }

//   loading.value = true;
//   try {
//     const response = await axios.get(`/mitra/${mitraId}`);
//     mitra.value = response.data;
//     console.log('Detail mitra:', mitra.value);
//   } catch (error) {
//     console.error('Error fetching mitra detail:', error);
//     Swal.fire({
//       icon: 'error',
//       title: 'Gagal Memuat Data',
//       text: 'Tidak dapat memuat detail laundry. Silakan coba lagi.',
//     });
//   } finally {
//     loading.value = false;
//   }
// });
</script>

<style scoped>
.detail-container {
  min-height: 100vh;
  background: #f7f7f7;
  padding-bottom: 40px;
}

/* Back Button */
.back-button-wrapper {
  background: white;
  padding: 16px 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.btn-back {
  background: transparent;
  border: 2px solid #667eea;
  color: #667eea;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-back:hover {
  background: #667eea;
  color: white;
}

/* Loading & Error States */
.loading-state,
.error-state {
  text-align: center;
  padding: 80px 20px;
  color: #666;
  font-size: 18px;
}

.error-state {
  color: #dc3545;
}

/* Detail Content */
.detail-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Header Section */
.header-section {
  background: white;
  border-radius: 12px;
  padding: 32px;
  margin: 24px 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 32px;
  align-items: center;
}

.header-image {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  width: 150px;
  height: 150px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.placeholder-img {
  font-size: 64px;
}

.header-info {
  flex: 1;
}

.header-info h1 {
  font-size: 32px;
  color: #333;
  margin-bottom: 12px;
}

.address {
  color: #666;
  font-size: 16px;
  margin-bottom: 16px;
}

.status-badge {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 14px;
  background: #f0f0f0;
  color: #999;
}

.status-badge.open {
  background: #d4edda;
  color: #16a34a;
}

/* Services Section */
.services-section {
  background: white;
  border-radius: 12px;
  padding: 32px;
  margin: 24px 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.services-section h2 {
  font-size: 24px;
  color: #333;
  margin-bottom: 24px;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.service-card {
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.3s;
  position: relative;
  background: white;
}

.service-card:hover {
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
  transform: translateY(-2px);
}

.service-card.selected {
  border-color: #667eea;
  background: #f0f4ff;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.service-icon {
  font-size: 40px;
  margin-bottom: 12px;
}

.service-card h3 {
  font-size: 18px;
  color: #333;
  margin-bottom: 8px;
}

.service-desc {
  color: #666;
  font-size: 14px;
  margin-bottom: 16px;
  line-height: 1.5;
}

.service-price {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.price {
  font-size: 20px;
  font-weight: bold;
  color: #667eea;
}

.unit {
  font-size: 14px;
  color: #666;
}

.selected-check {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 32px;
  height: 32px;
  background: #667eea;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: bold;
}

.no-services {
  text-align: center;
  padding: 40px;
  color: #999;
}

/* Order Section */
.order-section {
  background: white;
  border-radius: 12px;
  padding: 32px;
  margin: 24px 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.order-section h2 {
  font-size: 24px;
  color: #333;
  margin-bottom: 24px;
}

.order-form {
  max-width: 600px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.form-control {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 15px;
  transition: border-color 0.3s;
  font-family: inherit;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
}

/* Order Summary */
.order-summary {
  background: #f9fafb;
  border-radius: 8px;
  padding: 20px;
  margin: 24px 0;
}

.order-summary h3 {
  font-size: 18px;
  color: #333;
  margin-bottom: 16px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  color: #666;
}

.summary-row.total {
  border-top: 2px solid #e5e7eb;
  margin-top: 8px;
  padding-top: 16px;
  font-weight: bold;
  font-size: 18px;
  color: #333;
}

.btn-order {
  width: 100%;
  background: #667eea;
  color: white;
  padding: 14px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-order:hover:not(:disabled) {
  background: #5568d3;
}

.btn-order:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* Info Section */
.info-section {
  background: white;
  border-radius: 12px;
  padding: 32px;
  margin: 24px 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.info-section h2 {
  font-size: 24px;
  color: #333;
  margin-bottom: 24px;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.info-card {
  padding: 20px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  text-align: center;
}

.info-icon {
  font-size: 40px;
  margin-bottom: 12px;
}

.info-card h4 {
  font-size: 16px;
  color: #333;
  margin-bottom: 8px;
}

.info-card p {
  color: #666;
  font-size: 14px;
  line-height: 1.5;
}

/* Additional Info */
.additional-info {
  margin-top: 32px;
  padding-top: 32px;
  border-top: 2px solid #e5e7eb;
}

.additional-info h3 {
  font-size: 20px;
  color: #333;
  margin-bottom: 20px;
}

.info-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-item {
  display: flex;
  gap: 12px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 8px;
  font-size: 14px;
  line-height: 1.5;
}

.info-label {
  font-weight: 600;
  color: #667eea;
  min-width: 180px;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .header-section {
    flex-direction: column;
    text-align: center;
  }

  .header-info h1 {
    font-size: 24px;
  }

  .services-grid {
    grid-template-columns: 1fr;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .info-item {
    flex-direction: column;
    gap: 8px;
  }

  .info-label {
    min-width: auto;
  }
}
</style>