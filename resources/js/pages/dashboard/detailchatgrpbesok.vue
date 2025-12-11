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

          <button class="submit-button" @click="openPaymentModal" :disabled="!canSubmit || submitting">
            <span v-if="submitting">Memproses...</span>
            <span v-else>Buat Pesanan</span>
          </button>
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

  <!-- Payment Modal -->
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

      <div style="display:flex; gap:8px; margin-top:12px;">
        <button class="btn btn-secondary" @click="closePaymentModal" :disabled="submitting">Batal</button>
        <button class="btn btn-primary" @click="submitForm" :disabled="!selectedPayment || submitting">
          <span v-if="submitting">Memproses...</span>
          <span v-else>Lanjutkan</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from "@/libs/axios";
import Swal from 'sweetalert2';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// props
const props = defineProps<{ id?: string }>();

// state
const mitra = ref<any>(null);
const loading = ref(false);
const selectedLayanan = ref<number | null>(null);
const beratEstimasi = ref<number>(1);
const namaPemesan = ref<string>('');
const noTelepon = ref<string>(authStore.user?.pelanggan?.no_telepon || authStore.user?.no_telepon || '');
const catatan = ref<string>('');
const showPaymentModal = ref(false);
const selectedPayment = ref<string | null>(null);
const submitting = ref(false);

const pelangganId = computed(() => authStore.user?.pelanggan?.id ?? authStore.user?.id);

// update nama & noTelepon jika authStore.user berubah
watch(() => authStore.user, (val) => {
  if (!val) return;
  namaPemesan.value = val.name || val.nama || val.pelanggan?.nama || '';
  noTelepon.value = val.pelanggan?.no_telepon || val.no_telepon || noTelepon.value;
});

// fallback to localStorage if store not ready
onMounted(() => {
  const mitraId = route.params.id || props.id;
  if (!mitraId) {
    router.push('/');
    return;
  }

  loading.value = true;
  axios.get(`/mitraa/${mitraId}`)
    .then(res => {
      mitra.value = res.data;
    })
    .catch(err => {
      console.error('Error fetching mitra detail:', err);
      Swal.fire({ icon: 'error', title: 'Gagal Memuat Data', text: err.response?.data?.message || 'Tidak dapat memuat detail laundry.'});
    })
    .finally(() => loading.value = false);

  // try localStorage fallback
  try {
    const user = JSON.parse(localStorage.getItem("user") || 'null');
    if (user) {
      namaPemesan.value = user.name || user.pelanggan?.nama || namaPemesan.value;
      noTelepon.value = user.pelanggan?.no_telepon || user.no_telepon || noTelepon.value;
    }
  } catch (e) { /* ignore */ }
});

// computed
const canSubmit = computed(() => {
  return !!selectedLayanan.value && beratEstimasi.value > 0 && (noTelepon.value || '').trim() !== '';
});

// helper: pilih layanan
function selectLayanan(jenis_layanan: any) {
  selectedLayanan.value = jenis_layanan.id;
}

// helper: get selected layanan details
function getSelectedLayananName() {
  if (!mitra.value || !selectedLayanan.value) return '';
  const layanan = mitra.value.jenis_layanan.find((l:any) => l.id === selectedLayanan.value);
  return layanan ? layanan.nama_layanan : '';
}
function getSelectedLayananPrice() {
  if (!mitra.value || !selectedLayanan.value) return 0;
  const layanan = mitra.value.jenis_layanan.find((l:any) => l.id === selectedLayanan.value);
  return layanan ? Number(layanan.harga) : 0;
}
function calculateTotal() {
  return getSelectedLayananPrice() * (beratEstimasi.value || 0);
}

// open/close modal
function openPaymentModal() {
  if (!canSubmit.value) {
    Swal.fire({ icon: 'warning', title: 'Lengkapi data', text: 'Lengkapi berat & nomor telepon terlebih dahulu.'});
    return;
  }
  showPaymentModal.value = true;
  selectedPayment.value = 'manual'; // default pilihan
}
function closePaymentModal() {
  showPaymentModal.value = false;
  selectedPayment.value = null;
}

// load midtrans snap script (returns a promise resolved when script ready)
function loadSnapScript(clientKey = 'SB-Mid-client-XXXXX') {
  return new Promise<void>((resolve, reject) => {
    if ((window as any).snap) {
      return resolve();
    }

    const existing = document.querySelector('script[data-midtrans-snap]') as HTMLScriptElement | null;
    if (existing) {
      existing.onload ? existing.onload = () => resolve() : resolve();
      return;
    }

    const script = document.createElement('script');
    script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
    script.async = true;
    script.setAttribute('data-client-key', clientKey);
    script.setAttribute('data-midtrans-snap', '1');
    script.onload = () => resolve();
    script.onerror = (e) => reject(e);
    document.body.appendChild(script);
  });
}

// submit form after user selects payment
async function submitForm() {
  if (!canSubmit.value) {
    Swal.fire({ icon: 'warning', title: 'Lengkapi data', text: 'Lengkapi semua field yang wajib.'});
    return;
  }
  if (!selectedPayment.value) {
    Swal.fire({ icon: 'warning', title: 'Pilih Metode Pembayaran', text: 'Silakan pilih metode pembayaran.'});
    return;
  }

  submitting.value = true;

  // build payload
  const kodeOrder = `ORD-${Date.now()}`;
  const payload: any = {
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
    metode_pembayaran: selectedPayment.value,
    total_harga: calculateTotal()
  };

  try {
    // send to backend
    const res = await axios.post('/order/store', payload);

    // expected response: res.data.success && (res.data.snap_token | res.data.client_key)
    const data = res.data || {};

    // close modal UI
    showPaymentModal.value = false;

    if (selectedPayment.value === 'midtrans') {
      const snapToken = data.snap_token ?? data.snapToken ?? data.data?.snap_token;
      const clientKey = data.client_key ?? data.clientKey ?? data.data?.client_key ?? 'SB-Mid-client-XXXXX';

      if (!snapToken) {
        // backend belum generate snap token — try request explicit endpoint
        // try a fallback call to /payment/token/:orderId if you have such endpoint
        Swal.fire({ icon: 'error', title: 'Token Midtrans tidak tersedia', text: 'Gagal mendapatkan token pembayaran.'});
        submitting.value = false;
        return;
      }

      // ensure Snap lib is loaded (with clientKey if provided)
      try {
        await loadSnapScript(clientKey);
      } catch (e) {
        console.error('Gagal load Midtrans snap.js', e);
        Swal.fire({ icon: 'error', title: 'Gagal memuat pembayaran', text: 'Tidak dapat memuat Midtrans.'});
        submitting.value = false;
        return;
      }

      // call snap popup
      (window as any).snap.pay(snapToken, {
        onSuccess: async (result: any) => {
          console.log('Midtrans success', result);
          // optionally call backend to update status
          try {
            await axios.post('/manual-update-status', {
              order_id: result.order_id || payload.kode_order,
              transaction_status: result.transaction_status,
              payment_type: result.payment_type,
            });
          } catch (e) {
            console.warn('Gagal update status setelah midtrans success', e);
          }
          Swal.fire({ icon: 'success', title: 'Pembayaran Berhasil' }).then(() => {
            router.push('/RiwayatTransaksi');
          });
        },
        onPending: async (result: any) => {
          console.log('Midtrans pending', result);
          try {
            await axios.post('/manual-update-status', {
              order_id: result.order_id || payload.kode_order,
              transaction_status: result.transaction_status,
              payment_type: result.payment_type,
            });
          } catch (e) {}
          Swal.fire({ icon: 'info', title: 'Menunggu Pembayaran' }).then(() => {
            router.push('/RiwayatTransaksi');
          });
        },
        onError: function (result: any) {
          console.error('Midtrans error', result);
          Swal.fire({ icon: 'error', title: 'Pembayaran Gagal' });
        },
        onClose: function () {
          console.log('Midtrans popup ditutup');
        }
      });

      submitting.value = false;
      return;
    }

    // For COD or manual transfer: just show success and redirect to history
    Swal.fire({
      icon: 'success',
      title: 'Pesanan Berhasil Dibuat',
      html: `<p>Kode order: <strong>${kodeOrder}</strong></p><p>Metode: <strong>${selectedPayment.value}</strong></p>`,
      confirmButtonText: 'OK'
    }).then(() => {
      router.push('/RiwayatTransaksi');
    });

  } catch (error: any) {
    console.error('Error submitting order:', error);
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error.response?.data?.message || 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.'
    });
  } finally {
    submitting.value = false;
  }
}
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

.submit-button {
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

.submit-button:hover:not(:disabled) {
  background: #5568d3;
}

.submit-button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* Payment modal */
.payment-modal {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.5);
  z-index: 3000;
}
.payment-box {
  width: 360px;
  background: white;
  padding: 18px;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.payment-option {
  display: flex;
  gap: 10px;
  align-items: center;
  padding: 8px 0;
  cursor: pointer;
}
.payment-option input { margin-right: 8px; }

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
