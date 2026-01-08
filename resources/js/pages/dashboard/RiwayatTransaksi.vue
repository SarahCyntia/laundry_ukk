<template>
  <div class="transaksi-container">
    <!-- Back Button -->
    <div class="back-button-wrapper">
      <button class="btn-back" @click="router.back()">
        ← Kembali
      </button>
    </div>

    <!-- Header -->
    <div class="header-section">
      <h1>Riwayat Transaksi</h1>
      <p class="subtitle">Pantau status pesanan laundry Anda</p>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button v-for="status in filterStatus" :key="status.value"
        :class="['tab-btn', { active: activeFilter === status.value }]" @click="activeFilter = status.value">
        {{ status.label }}
        <span v-if="getCountByStatus(status.value)" class="badge">
          {{ getCountByStatus(status.value) }}
        </span>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Memuat riwayat transaksi...</p>
    </div>

    <!-- Order List -->
    <div v-else-if="filteredOrder.length" class="order-list">
      <div v-for="order in filteredOrder" :key="order.id" class="order-card" @click="viewOrderDetail(order)">
        <!-- Order Header -->
        <div class="order-header">
          <div class="order-code">
            <span class="code-label">Kode Order</span>
            <span class="code-value">{{ order.kode_order }}</span>
          </div>
          <div :class="['status-badge', getStatusClass(order.status)]">
            {{ getStatusLabel(order.status) }}
          </div>
        </div>

        <!-- Order Info -->
        <div class="order-info">
          <div class="info-row">
            <span class="label">🧺 Nama Laundry:</span>
            <span class="value">{{ order.mitra?.nama_laundry || '-' }}</span>
          </div>
          <div class="info-row">
            <span class="label">🧼 Layanan:</span>
            <span class="value">{{ order.jenis_layanan?.nama_layanan || '-' }}</span>
          </div>
          <div class="info-row">
            <span class="label">⚖️ Berat:</span>
            <span class="value">
              {{ order.berat_aktual ? `${order.berat_aktual} kg (aktual)` : `${order.berat_estimasi} kg (estimasi)` }}
            </span>
          </div>
          <div class="info-row total">
            <span class="label">💰 Total:</span>
            <span class="value">
              {{ order.harga_final
                ? `Rp ${Number(order.harga_final).toLocaleString('id-ID')}`
                : `Rp ${(order.berat_estimasi * order.jenis_layanan?.harga || 0).toLocaleString('id-ID')} (estimasi)`
              }}
            </span>
          </div>
        </div>

        <!-- Order Footer -->
        <div class="order-footer">
          <span class="order-date">
            📅 {{ formatDate(order.created_at) }}
          </span>
          <button class="btn-detail" @click.stop="viewOrderDetail(order)">
            Lihat Detail →
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="empty-state">
      <div class="empty-icon">📦</div>
      <h3>Belum Ada Transaksi</h3>
      <p>{{ getEmptyMessage() }}</p>
      <button class="btn-primary" @click="goCari">
        Cari Laundry
      </button>


      <!-- <button class="btn-primary" @click="router.push({ path: '/', hash: '#cari' })">
  Cari Laundry
</button> -->


      <!-- <button class="btn-primary" @click="router.push('/') ">
        Cari Laundry
      </button> -->
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '@/libs/axios';
import Swal from 'sweetalert2';

const router = useRouter();

// State
const order = ref([]);
const loading = ref(false);
const activeFilter = ref('semua');

// Filter options
const filterStatus = [
  { value: 'semua', label: 'Semua' },
  { value: 'menunggu_konfirmasi_mitra', label: 'Menunggu' },
  { value: 'diterima', label: 'Diterima' },
  { value: 'diproses', label: 'Diproses' },
  { value: 'selesai', label: 'Selesai' },
  { value: 'ditolak', label: 'Ditolak' },
];

// Computed
const filteredOrder = computed(() => {
  if (activeFilter.value === 'semua') {
    return order.value;
  }
  return order.value.filter(order => order.status === activeFilter.value);
});


function formatTime(date: Date) {
  return date.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
  });
}

function formatDateDay(date: Date) {
  return date.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
  });
}

function getCountdown(target: Date) {
  const now = new Date().getTime();
  const diff = target.getTime() - now;

  if (diff <= 0) return "Waktu antar sudah lewat";

  const hours = Math.floor(diff / 1000 / 60 / 60);
  const minutes = Math.floor((diff / 1000 / 60) % 60);

  return `Sisa waktu ${hours} jam ${minutes} menit untuk mengantar`;
}



// async function updateStatusSudahAntar(order) {
//   try {
//     const res = await axios.post(`/pelanggan/order/${order.id}/sudah-antar`);
//     Swal.fire("Berhasil", "Status berhasil diperbarui!", "success");
//   } catch (err) {
//     Swal.fire("Gagal", "Terjadi kesalahan", "error");
//   }
// }
async function updateStatusSudahAntar(order) {
  Swal.fire({
    title: "Upload Foto Struk",
    html: `
      <input type="file" id="fotoStruk" accept="image/*" 
        style="margin-top:15px; border:1px solid #ddd; padding:10px; width:100%; border-radius:6px;">
    `,
    confirmButtonText: "Kirim",
    showCancelButton: true,
    cancelButtonText: "Batal",
    preConfirm: () => {
      const file = (document.getElementById("fotoStruk") as HTMLInputElement).files?.[0];
      if (!file) {
        Swal.showValidationMessage("Anda harus upload foto struk!");
      }
      return file;
    }
  }).then(async (result) => {
    if (!result.value) return;

    const file = result.value;
    const formData = new FormData();
    formData.append("foto_struk", file);

    try {
      const res = await axios.post(
        `/pelanggan/order/${order.id}/sudah-antar`,
        formData,
        { headers: { "Content-Type": "multipart/form-data" } }
      );

      Swal.fire("Berhasil", "Struk berhasil diupload", "success");

      fetchOrder(); // refresh data
    } catch (err) {
      Swal.fire("Gagal", "Tidak dapat mengupload struk.", "error");
    }
  });
}

function chatLaundry(order) {
  const phone = order.mitra?.phone || '';
  const msg = `Halo, saya ingin menanyakan order laundry dengan kode ${order.kode_order}`;

  window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, "_blank");
}
function bukaMaps(order) {
  const alamat = order.mitra?.alamat_laundry || "";
  window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(alamat)}`, "_blank");
}














// Methods
function getCountByStatus(status: string) {
  if (status === 'semua') return order.value.length;
  return order.value.filter(order => order.status === status).length;
}

function getStatusClass(status: string) {
  const statusMap = {
    'menunggu_konfirmasi_mitra': 'warning',
    'ditunggu_mitra': 'warning',
    'diterima': 'info',
    'diproses': 'processing',
    'selesai': 'success',
    'ditolak': 'danger',
  };
  return statusMap[status] || 'default';
}

function getStatusLabel(status: string) {
  const labelMap = {
    'menunggu_konfirmasi_mitra': 'Menunggu Konfirmasi Mitra',
    'ditunggu_mitra': 'Ditunggu Mitra',
    'diterima': 'Diterima Mitra',
    'diproses': 'Sedang Diproses',
    'selesai': 'Selesai',
    'ditolak': 'Ditolak',
  };
  return labelMap[status] || status;
}

function formatDate(dateString: string) {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

function getEmptyMessage() {
  if (activeFilter.value === 'semua') {
    return 'Anda belum memiliki transaksi. Mulai pesan laundry sekarang!';
  }
  return `Tidak ada transaksi dengan status "${filterStatus.find(f => f.value === activeFilter.value)?.label}".`;
}

function viewOrderDetail(order) {
  const createdAt = new Date(order.created_at);

  const waktuAntar = new Date(createdAt.getTime() + 2 * 60 * 60 * 1000);
  const estimasiSelesai = new Date(waktuAntar.getTime() + 6 * 60 * 60 * 1000);

  let htmlContent = '';

 if (order.status === 'ditunggu_mitra') {

  const sudahUpload = !!order.foto_struk;

  htmlContent = `
    <p style="font-size:18px; font-weight:600; color:#2563eb;">
      ✓ Order Diterima Mitra → Harap Antar Laundry
    </p>

    ${
      !sudahUpload
        ? `
          <p><strong>Waktu Antar:</strong><br>
            ${formatTime(waktuAntar)} WIB (${formatDateDay(waktuAntar)})
          </p>

          <p><strong>Estimasi Selesai:</strong><br>
            ${formatTime(estimasiSelesai)} WIB (${formatDateDay(estimasiSelesai)})
          </p>

          <p style="margin-top:10px; padding:10px; background:#eef2ff; border-radius:8px; font-weight:600; color:#4338ca;">
            ${getCountdown(waktuAntar)}
          </p>
        `
        : ``
    }

    <hr style="margin:14px 0">

    <p><strong>Antar ke Laundry:</strong><br> ${order.mitra?.nama_laundry}</p>
    <p><strong>Alamat:</strong><br> ${order.mitra?.alamat_laundry}</p>
    <p><strong>Kode Order:</strong> ${order.kode_order}</p>

    <div style="margin-top:20px; display:flex; flex-direction:column; gap:10px;">

      ${
        !sudahUpload
          ? `
            <button id="btnSudahAntar"
              style="padding:10px; background:#4f46e5; color:white; border-radius:6px; font-weight:600;">
              ✓ Saya Sudah Antar
            </button>
          `
          : `
            <div style="padding:10px; background:#d1fae5; color:#065f46; border-radius:6px; font-weight:600;">
              👍 Anda sudah mengupload struk
            </div>
          `
      }

      <button id="btnChat" style="padding:10px; background:#10b981; color:white; border-radius:6px; font-weight:600;">
        💬 Chat Laundry
      </button>

      <button id="btnMaps" style="padding:10px; background:#2563eb; color:white; border-radius:6px; font-weight:600;">
        📍 Lihat Lokasi Laundry
      </button>

    </div>
  `;
}


  /* 🔴 TAMBAHAN BARU */
  else if (order.status === 'ditolak') {
    htmlContent = `
    <p style="font-size:18px; font-weight:600; color:#dc2626;">
      ✗ Pesanan Ditolak
    </p>

    <hr style="margin:12px 0">

    <p><strong>Kode Order:</strong> ${order.kode_order}</p>
    <p><strong>Laundry:</strong> ${order.mitra?.nama_laundry || '-'}</p>

    <div style="
      margin-top:12px;
      padding:12px;
      background:#fee2e2;
      border-radius:8px;
      color:#7f1d1d;
    ">
      <strong>Alasan Penolakan:</strong><br>
      ${order.alasan_penolakan || 'Tidak ada keterangan dari mitra'}
    </div>
  `;
  }

  /* DEFAULT */
  else {
    htmlContent = `
    <p>Status: <strong>${getStatusLabel(order.status)}</strong></p>
    <p>Kode Order: <strong>${order.kode_order}</strong></p>
  `;
  }

  // if (order.status === 'diterima') {
  //   htmlContent = `
  //     <p style="font-size:18px; font-weight:600; color:#2563eb;">
  //       ✓ Order Diterima Mitra → Harap Antar Laundry
  //     </p>

  //     <p><strong>Waktu Antar:</strong><br>
  //       ${formatTime(waktuAntar)} WIB (${formatDateDay(waktuAntar)})
  //     </p>

  //     <p><strong>Estimasi Selesai:</strong><br>
  //       ${formatTime(estimasiSelesai)} WIB (${formatDateDay(estimasiSelesai)})
  //     </p>

  //     <p style="margin-top:10px; padding:10px; background:#eef2ff; border-radius:8px; font-weight:600; color:#4338ca;">
  //       ${getCountdown(waktuAntar)}
  //     </p>

  //     <hr style="margin:14px 0">

  //     <p><strong>Antar ke Laundry:</strong><br> ${order.mitra?.nama_laundry}</p>
  //     <p><strong>Alamat:</strong><br> ${order.mitra?.alamat_laundry}</p>
  //     <p><strong>Kode Order:</strong> ${order.kode_order}</p>

  //     <div style="margin-top:20px; display:flex; flex-direction:column; gap:10px;">
  //       <button id="btnSudahAntar" style="padding:10px; background:#4f46e5; color:white; border-radius:6px; font-weight:600;">
  //         ✓ Saya Sudah Antar
  //       </button>

  //       <button id="btnChat" style="padding:10px; background:#10b981; color:white; border-radius:6px; font-weight:600;">
  //         💬 Chat Laundry
  //       </button>

  //       <button id="btnMaps" style="padding:10px; background:#2563eb; color:white; border-radius:6px; font-weight:600;">
  //         📍 Lihat Lokasi Laundry
  //       </button>
  //     </div>
  //   `;
  // }
  // else {
  //   htmlContent = `
  //     <p>Status: <strong>${order.status}</strong></p>
  //     <p>Kode Order: <strong>${order.kode_order}</strong></p>
  //   `;
  // }

  setTimeout(() => {
    Swal.fire({
      title: 'Detail Pesanan',
      html: htmlContent,
      showConfirmButton: true,
      confirmButtonText: "Tutup",
      width: '480px'
    }).then(() => { });
  }, 50);

  setTimeout(() => {
    const btnSudahAntar = document.getElementById("btnSudahAntar");
    const btnChat = document.getElementById("btnChat");
    const btnMaps = document.getElementById("btnMaps");

    // if (btnSudahAntar) {
    //   btnSudahAntar.addEventListener('click', () => updateStatusSudahAntar(order));
    // }
    if (!order.foto_struk && btnSudahAntar) {
      btnSudahAntar.addEventListener('click', () => updateStatusSudahAntar(order));
    }


    if (btnChat) {
      btnChat.addEventListener('click', () => chatLaundry(order));
    }

    if (btnMaps) {
      btnMaps.addEventListener('click', () => bukaMaps(order));
    }
  }, 200);
}


// function viewOrderDetail(order) {
//   const createdAt = new Date(order.created_at);

//   // Waktu antar +2 jam dari waktu dibuat
//   const waktuAntar = new Date(createdAt.getTime() + 2 * 60 * 60 * 1000);

//   // Estimasi selesai +6 jam setelah waktu antar
//   const estimasiSelesai = new Date(waktuAntar.getTime() + 6 * 60 * 60 * 1000);

//   let htmlContent = '';

//   if (order.status === 'diterima') {
//     htmlContent = `
//       <p style="font-size:18px; font-weight:600; color:#2563eb;">
//         ✓ Order Diterima Mitra → Harap Antar Laundry
//       </p>

//       <p><strong>Waktu Antar:</strong> 
//         ${formatTime(waktuAntar)} WIB (${formatDateDay(waktuAntar)})
//       </p>

//       <p><strong>Estimasi Selesai:</strong> 
//         ${formatTime(estimasiSelesai)} WIB (${formatDateDay(estimasiSelesai)})
//       </p>

//       <p style="margin-top:10px; padding:10px; background:#eef2ff; border-radius:8px; font-weight:600; color:#4338ca;">
//         ${getCountdown(waktuAntar)}
//       </p>

//       <hr style="margin:14px 0">

//       <p><strong>Antar ke Laundry:</strong> ${order.mitra?.nama_laundry}</p>
//       <p><strong>Alamat:</strong> ${order.mitra?.alamat_laundry}</p>
//       <p><strong>Kode Order:</strong> ${order.kode_order}</p>
//     `;
//   } 
//   else {
//     htmlContent = `
//       <p>Status: <strong>${order.status}</strong></p>
//       <p>Kode Order: <strong>${order.kode_order}</strong></p>
//     `;
//   }

//   Swal.fire({
//     title: 'Detail Pesanan',
//     html: htmlContent,
//     confirmButtonText: 'Tutup',
//     width: '500px'
//   });
// }

// async function viewOrderDetail(order: any) {
//   const statusMessages = {
//     'menunggu_konfirmasi_mitra': `
//       <p style="color: #f59e0b; font-weight: 600; margin-bottom: 12px;">⏳ Menunggu konfirmasi dari mitra</p>
//       <p>Pesanan Anda sedang menunggu konfirmasi dari laundry. Silakan tunggu beberapa saat.</p>
//     `,
//     'diterima': `
//       <p style="color: #3b82f6; font-weight: 600; margin-bottom: 12px;">✓ Diterima mitra</p>
//       <p><strong>Langkah selanjutnya:</strong></p>
//       <ol style="text-align: left; padding-left: 20px; margin-top: 8px;">
//         <li>Antar cucian ke: <strong>${order.mitra?.nama_laundry}</strong></li>
//         <li>Alamat: ${order.mitra?.alamat_laundry}</li>
//         <li>Sebutkan kode order: <strong>${order.kode_order}</strong></li>
//       </ol>
//     `,
//     'diproses': `
//       <p style="color: #8b5cf6; font-weight: 600; margin-bottom: 12px;">⚙️ Sedang diproses</p>
//       <p>Cucian Anda sedang dalam proses pencucian. Tunggu hingga selesai.</p>
//     `,
//     'selesai': `
//       <p style="color: #16a34a; font-weight: 600; margin-bottom: 12px;">✓ Selesai</p>
//       <p>Cucian Anda sudah selesai dan siap diambil!</p>
//     `,
//     'ditolak': `
//       <p style="color: #dc2626; font-weight: 600; margin-bottom: 12px;">✗ Ditolak</p>
//       <p>Mohon maaf, pesanan Anda ditolak oleh mitra. Silakan pesan di laundry lain.</p>
//     `,
//   };

//   Swal.fire({
//     title: 'Detail Pesanan',
//     html: `
//       <div style="text-align: left; padding: 10px;">
//         <p><strong>Kode Order:</strong> ${order.kode_order}</p>
//         <hr style="margin: 12px 0;">
//         <p><strong>Laundry:</strong> ${order.mitra?.nama_laundry || '-'}</p>
//         <p><strong>Alamat:</strong> ${order.mitra?.alamat_laundry || '-'}</p>
//         <p><strong>Layanan:</strong> ${order.jenis_layanan?.nama_layanan || '-'}</p>
//         <hr style="margin: 12px 0;">
//         <p><strong>Berat Estimasi:</strong> ${order.berat_estimasi} kg</p>
//         ${order.berat_aktual ? `<p><strong>Berat Aktual:</strong> ${order.berat_aktual} kg</p>` : ''}
//         <p><strong>Harga/kg:</strong> Rp ${Number(order.jenis_layanan?.harga || 0).toLocaleString('id-ID')}</p>
//         <p><strong>Total:</strong> ${order.harga_final 
//           ? `Rp ${Number(order.harga_final).toLocaleString('id-ID')} (final)` 
//           : `Rp ${(order.berat_estimasi * (order.jenis_layanan?.harga || 0)).toLocaleString('id-ID')} (estimasi)`
//         }</p>
//         ${order.catatan ? `<p><strong>Catatan:</strong> ${order.catatan}</p>` : ''}
//         <p><strong>No. Telepon:</strong> ${order.no_telepon}</p>
//         <hr style="margin: 12px 0;">
//         ${statusMessages[order.status] || ''}
//         <hr style="margin: 12px 0;">
//         <p style="font-size: 12px; color: #666;"><strong>Tanggal Pesan:</strong> ${formatDate(order.created_at)}</p>
//       </div>
//     `,
//     confirmButtonColor: '#667eea',
//     confirmButtonText: 'Tutup',
//   });
// }

async function fetchOrder() {
  loading.value = true;
  try {
    // Sesuaikan endpoint dengan backend Anda
    const response = await axios.get('/order/pelanggan');
    order.value = response.data.data || response.data;
    console.log('Order:', order.value);
  } catch (error) {
    console.error('Error fetching order:', error);
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memuat Data',
      text: error.response?.data?.message || 'Tidak dapat memuat riwayat transaksi.',
    });
  } finally {
    loading.value = false;
  }
}



const goCari = () => {
  router.push({ path: '/beranda', hash: '#cari' });
};



// Lifecycle
onMounted(() => {
  fetchOrder();
});
</script>

<style scoped>
.transaksi-container {
  min-height: 100vh;
  background: #f7f7f7;
  padding-bottom: 80px;
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

/* Header */
.header-section {
  margin: 20px;
  margin-bottom: 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 32px 24px;
  border-radius: 12px;
  margin-bottom: 24px;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.header-section h1 {
  font-size: 28px;
  margin-bottom: 8px;
}

.subtitle {
  opacity: 0.9;
  font-size: 14px;
}

/* Filter Tabs */
.filter-tabs {
  display: flex;
  gap: 8px;
  margin: 0 20px 24px 20px;
  overflow-x: auto;
  padding-bottom: 8px;
}

.tab-btn {
  padding: 10px 20px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 8px;
}

.tab-btn:hover {
  border-color: #667eea;
}

.tab-btn.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.badge {
  background: rgba(0, 0, 0, 0.15);
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 12px;
}

.tab-btn.active .badge {
  background: rgba(255, 255, 255, 0.3);
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.spinner {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #667eea;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Order List */
.order-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
}

.order-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.3s;
}

.order-card:hover {
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
  transform: translateY(-2px);
}

/* Order Header */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
  padding-bottom: 16px;
  border-bottom: 2px solid #f3f4f6;
}

.order-code {
  display: flex;
  flex-direction: column;
}

.code-label {
  font-size: 12px;
  color: #999;
  margin-bottom: 4px;
}

.code-value {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.status-badge.warning {
  background: #fef3c7;
  color: #f59e0b;
}

.status-badge.info {
  background: #dbeafe;
  color: #3b82f6;
}

.status-badge.processing {
  background: #ede9fe;
  color: #8b5cf6;
}

.status-badge.success {
  background: #d1fae5;
  color: #16a34a;
}

.status-badge.danger {
  background: #fee2e2;
  color: #dc2626;
}

/* Order Info */
.order-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 16px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
}

.info-row .label {
  color: #666;
}

.info-row .value {
  font-weight: 600;
  color: #333;
  text-align: right;
}

.info-row.total {
  border-top: 2px solid #f3f4f6;
  padding-top: 12px;
  margin-top: 8px;
  font-size: 16px;
}

.info-row.total .value {
  color: #667eea;
}

/* Order Footer */
.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid #f3f4f6;
}

.order-date {
  font-size: 12px;
  color: #999;
}

.btn-detail {
  background: transparent;
  border: 1px solid #667eea;
  color: #667eea;
  padding: 6px 16px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-detail:hover {
  background: #667eea;
  color: white;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  max-width: 500px;
  margin: 40px auto;
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.empty-state h3 {
  font-size: 20px;
  color: #333;
  margin-bottom: 8px;
}

.empty-state p {
  color: #666;
  margin-bottom: 24px;
}

.btn-primary {
  background: #667eea;
  color: white;
  padding: 12px 32px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: #5568d3;
}

/* Responsive */
@media (max-width: 768px) {
  .transaksi-container {
    padding: 12px;
  }

  .header-section {
    padding: 24px 16px;
  }

  .order-footer {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }

  .btn-detail {
    width: 100%;
  }
}
</style>