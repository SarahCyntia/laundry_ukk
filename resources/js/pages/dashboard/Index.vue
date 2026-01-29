<template>
  <div class="container py-4">
    <div class="dashboard-header">
      <div>
        <h1 class="mb-2">Dashboard Admin</h1>
        <p class="text-muted">Kelola seluruh sistem Slaundry</p>
      </div>
      <div class="header-actions">
        <span class="date-badge">
          <i class="far fa-calendar-alt me-2"></i>
          {{ currentDate }}
        </span>
      </div>
    </div>

    <!-- Alert untuk notifikasi -->
    <div v-if="showSuccessAlert" class="alert alert-success">
      <button type="button" class="btn-close" @click="showSuccessAlert = false"></button>
      {{ alertMessage }}
    </div>

    <!-- Overview Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3" v-for="card in overviewCards" :key="card.title">
        <div class="card-dashboard h-100" :class="card.colorClass">
          <i :class="card.icon"></i>
          <h5>{{ card.title }}</h5>
          <p>{{ card.value }}</p>
          <span class="card-trend" v-if="card.trend">
            <i :class="card.trend > 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
            {{ Math.abs(card.trend) }}%
          </span>
        </div>
      </div>
    </div>

    <!-- System Stats -->
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="stat-card">
          <div class="stat-header">
            <h4><i class="fas fa-store me-2"></i>Statistik Mitra</h4>
          </div>
          <div class="stat-body">
            <div class="stat-item">
              <span class="stat-label">Total Mitra Terdaftar</span>
              <span class="stat-value">{{ totalMitra }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Mitra Aktif (Buka)</span>
              <span class="stat-value text-success">{{ mitraAktif }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Mitra Tutup</span>
              <span class="stat-value text-danger">{{ mitraTutup }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Menunggu Verifikasi</span>
              <span class="stat-value text-warning">{{ mitraPending }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="stat-card">
          <div class="stat-header">
            <h4><i class="fas fa-users me-2"></i>Statistik Pengguna</h4>
          </div>
          <div class="stat-body">
            <div class="stat-item">
              <span class="stat-label">Total Pelanggan</span>
              <span class="stat-value">{{ totalPelanggan }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Pelanggan Aktif Bulan Ini</span>
              <span class="stat-value text-success">{{ pelangganAktif }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Pengguna Baru Hari Ini</span>
              <span class="stat-value text-info">{{ penggunaBaru }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Total Admin</span>
              <span class="stat-value">{{ totalAdmin }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Transaction & Revenue -->
    <!-- <div class="row g-4 mb-4">
      <div class="col-md-8">
        <div class="stat-card">
          <div class="stat-header">
            <h4><i class="fas fa-chart-line me-2"></i>Transaksi & Pendapatan</h4>
            <select v-model="selectedPeriod" class="period-select">
              <option value="today">Hari Ini</option>
              <option value="week">Minggu Ini</option>
              <option value="month">Bulan Ini</option>
            </select>
          </div>
          <div class="stat-body">
            <div class="revenue-overview">
              <div class="revenue-item">
                <span class="revenue-label">Total Transaksi</span>
                <h3 class="revenue-value">{{ totalTransaksi }}</h3>
              </div>
              <div class="revenue-item">
                <span class="revenue-label">Total Pendapatan</span>
                <h3 class="revenue-value text-success">{{ totalPendapatan }}</h3>
              </div>
              <div class="revenue-item">
                <span class="revenue-label">Rata-rata Transaksi</span>
                <h3 class="revenue-value text-info">{{ rataRataTransaksi }}</h3>
              </div>
            </div>
            <div class="transaction-breakdown mt-4">
              <div class="breakdown-item">
                <span class="breakdown-label">
                  <span class="status-dot status-pending"></span>
                  Menunggu Konfirmasi
                </span>
                <span class="breakdown-value">{{ transaksiMenunggu }}</span>
              </div>
              <div class="breakdown-item">
                <span class="breakdown-label">
                  <span class="status-dot status-process"></span>
                  Sedang Diproses
                </span>
                <span class="breakdown-value">{{ transaksiProses }}</span>
              </div>
              <div class="breakdown-item">
                <span class="breakdown-label">
                  <span class="status-dot status-done"></span>
                  Selesai
                </span>
                <span class="breakdown-value">{{ transaksiSelesai }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-header">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>Perlu Perhatian</h4>
          </div>
          <div class="stat-body">
            <div class="alert-item alert-warning">
              <i class="fas fa-clock"></i>
              <div>
                <strong>{{ komplainBelumDitangani }}</strong>
                <span>Komplain belum ditangani</span>
              </div>
            </div>
            <div class="alert-item alert-info">
              <i class="fas fa-user-check"></i>
              <div>
                <strong>{{ mitraPending }}</strong>
                <span>Mitra menunggu verifikasi</span>
              </div>
            </div>
            <div class="alert-item alert-danger">
              <i class="fas fa-ban"></i>
              <div>
                <strong>{{ laporanBelumDitinjau }}</strong>
                <span>Laporan belum ditinjau</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <!-- Quick Actions -->
    <div class="quick-actions-card">
      <h4 class="mb-3"><i class="fas fa-bolt me-2"></i>Aksi Cepat</h4>
      <div class="action-buttons">
        <button class="action-btn btn-primary" @click="goToPage('mitra')">
          <i class="fas fa-store"></i>
          <span>Kelola Mitra</span>
        </button>
        <button class="action-btn btn-success" @click="goToPage('datapelanggan')">
          <i class="fas fa-users"></i>
          <span>Kelola Pelanggan</span>
        </button>
        <!-- <button class="action-btn btn-warning" @click="goToPage('admin.transaksi')">
          <i class="fas fa-receipt"></i>
          <span>Lihat Transaksi</span>
        </button> -->
        <!-- <button class="action-btn btn-info" @click="goToPage('admin.laporan')">
          <i class="fas fa-chart-bar"></i>
          <span>Laporan</span>
        </button> -->
        <!-- <button class="action-btn btn-danger" @click="goToPage('admin.komplain')">
          <i class="fas fa-comments"></i>
          <span>Komplain</span>
        </button> -->
        <!-- <button class="action-btn btn-secondary" @click="goToPage('admin.settings')">
          <i class="fas fa-cog"></i>
          <span>Pengaturan</span>
        </button> -->
      </div>
    </div>
  </div>

  <!-- LAPORAN KEUANGAN ADMIN -->
  <!-- <div class="stat-card mt-4">
    <div class="stat-header">
      <h4>
        <i class="fas fa-file-invoice-dollar me-2"></i>
        Laporan Keuangan (Transaksi Dibayar)
      </h4>

      <select v-model="laporanPeriod" class="period-select" @change="fetchLaporanKeuangan">
        <option value="today">Hari Ini</option>
        <option value="week">Minggu Ini</option>
        <option value="month">Bulan Ini</option>
      </select>
    </div>

    <div class="stat-body">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Kode Order</th>
            <th>Pelanggan</th>
            <th>Mitra Laundry</th>
            <th>Metode Bayar</th>
            <th>Status</th>
            <th>Total</th>
          </tr>
        </thead>


        <tbody>
          <tr v-for="(item, index) in laporanKeuangan" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td data-label="Kode Order">{{ item.kode_order }}</td>
            <td data-label="Pelanggan">{{ item.nama_pelanggan }}</td>
            <td data-label="Mitra">{{ item.nama_mitra }}</td>
            <td data-label="Metode">{{ item.metode_pembayaran }}</td>
            <td data-label="Status">
              <span class="badge bg-success">{{ item.status_pembayaran }}</span>
            </td>
            <td data-label="Total">{{ formatCurrency(item.total_harga) }}</td>
          </tr>

          LOADING
          <tr v-if="loading">
            <td colspan="7" class="text-center">
              Memuat data...
            </td>
          </tr>
          JIKA KOSONG
          <tr v-if="!loading && laporanKeuangan.length === 0">
            <td colspan="7" class="text-center text-muted">
              Tidak ada transaksi dibayar
            </td>
          </tr>

        </tbody> -->


        <!-- <tbody>
          <tr v-for="(item, index) in laporanKeuangan" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td data-label="Kode Order">{{ item.kode_order }}</td>
            <td data-label="Pelanggan">{{ item.nama_pelanggan }}</td>
            <td data-label="Mitra">{{ item.nama_mitra }}</td>
            <td data-label="Metode">{{ item.metode_pembayaran }}</td>
            <td data-label="Status">
              <span class="badge bg-success">{{ item.status_pembayaran }}</span>
            </td>
            <td data-label="Total">{{ formatCurrency(item.total_harga) }}</td>

          </tr>

          <tr v-if="!LaporanKeuangan.length">
            <td colspan="7" class="text-center">
              Tidak ada transaksi dibayar
            </td>
          </tr>
        </tbody> -->
      <!-- </table>
    </div>


  </div> -->

</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import axios from "@/libs/axios";
import type { Pelanggan } from "@/types";
import LaporanKeuangan from "../mitra/laporan-keuangan.vue";
import { vLoading } from "element-plus";

const router = useRouter();

// Alert
const showSuccessAlert = ref(false);
const alertMessage = ref("");

// Date
const currentDate = ref(new Date().toLocaleDateString("id-ID", {
  weekday: "long",
  year: "numeric",
  month: "long",
  day: "numeric",
}));

// Period selection
const selectedPeriod = ref("today");

// Overview data
const totalTransaksiHariIni = ref(0);
const totalPendapatanHariIni = ref(0);
const totalMitra = ref(0);
const totalPelanggan = ref(0);

// Mitra stats
const mitraAktif = ref(0);
const mitraTutup = ref(0);
const mitraPending = ref(0);

// User stats
const pelangganAktif = ref(0);
const penggunaBaru = ref(0);
const totalAdmin = ref(0);

// Transaction stats
const totalTransaksi = ref(0);
const totalPendapatan = ref("Rp 0");
const rataRataTransaksi = ref("Rp 0");
const transaksiMenunggu = ref(0);
const transaksiProses = ref(0);
const transaksiSelesai = ref(0);

// Alerts
const komplainBelumDitangani = ref(0);
const laporanBelumDitinjau = ref(0);



const laporanKeuangan = ref<any[]>([])
const laporanPeriod = ref("today")

const fetchLaporanKeuangan = async () => {
  try {
    const { data } = await axios.get("/admin-laporan", {
      params: {
        period: laporanPeriod.value,
        status_pembayaran: "dibayar"
      }
    })

    laporanKeuangan.value = data.data || []
  } catch (err) {
    console.error("Gagal mengambil laporan keuangan", err)
  }
}
// Overview cards computed
const overviewCards = computed(() => [
  // {
  //   title: "Total Transaksi Hari Ini",
  //   icon: "fas fa-shopping-cart",
  //   value: totalTransaksiHariIni.value,
  //   colorClass: "card-blue",
  //   trend: 12,
  // },
  // {
  //   title: "Pendapatan Hari Ini",
  //   icon: "fas fa-money-bill-wave",
  //   value: formatCurrency(totalPendapatanHariIni.value),
  //   colorClass: "card-green",
  //   trend: 8,
  // },
  {
    title: "Total Mitra",
    icon: "fas fa-store",
    value: totalMitra.value,
    colorClass: "card-purple",
    // trend: 5,
  },
  {
    title: "Total Pelanggan",
    icon: "fas fa-users",
    value: totalPelanggan.value,
    colorClass: "card-orange",
    // trend: 15,
  },
]);

// Functions
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(amount);
};

const fetchAdminData = async () => {
  try {
    const { data } = await axios.get("/dashboard/admin-data", {
      params: { period: selectedPeriod.value },
    });
    //     axios.get("/dashboard/admin-data", {
    //   params: { period: selectedPeriod.value }
    // });


    // Update all stats
    totalTransaksiHariIni.value = data.totalTransaksiHariIni || 0;
    totalPendapatanHariIni.value = data.totalPendapatanHariIni || 0;
    totalMitra.value = data.totalMitra || 0;
    totalPelanggan.value = data.totalPelanggan || 0;

    mitraAktif.value = data.mitraAktif || 0;
    mitraTutup.value = data.mitraTutup || 0;
    mitraPending.value = data.mitraPending || 0;

    pelangganAktif.value = data.pelangganAktif || 0;
    penggunaBaru.value = data.penggunaBaru || 0;
    totalAdmin.value = data.totalAdmin || 0;

    totalTransaksi.value = data.totalTransaksi || 0;
    totalPendapatan.value = data.totalPendapatanFormatted || "Rp 0";
    rataRataTransaksi.value = data.rataRataTransaksiFormatted || "Rp 0";

    transaksiMenunggu.value = data.transaksiMenunggu || 0;
    transaksiProses.value = data.transaksiProses || 0;
    transaksiSelesai.value = data.transaksiSelesai || 0;

    komplainBelumDitangani.value = data.komplainBelumDitangani || 0;
    laporanBelumDitinjau.value = data.laporanBelumDitinjau || 0;
  } catch (error) {
    console.error("Error fetching admin data:", error);
  }
};

const goToPage = (routeName: string) => {
  router.push({ name: routeName });
};

let interval: any = null;
onMounted(() => {
  fetchAdminData()
  fetchLaporanKeuangan()

  interval = setInterval(fetchAdminData, 30000)
})

// onMounted(() => {
//   fetchAdminData();
//   interval = setInterval(fetchAdminData, 30000); // Update every 30 seconds
// });

onUnmounted(() => {
  clearInterval(interval);
});
</script>

<style scoped>
/* ===============================
   LAPORAN KEUANGAN ADMIN
================================ */

.table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
  background: #ffffff;
  border-radius: 10px;
  overflow: hidden;
}

.table thead {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.table thead th {
  color: #ffffff;
  font-size: 13px;
  font-weight: 600;
  padding: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  text-align: left;
}

.table tbody td {
  padding: 12px;
  font-size: 14px;
  color: #2c3e50;
  border-bottom: 1px solid #f1f1f1;
}

.table tbody tr:hover {
  background-color: #f8f9ff;
}

.table-striped tbody tr:nth-child(odd) {
  background-color: #fafbff;
}

/* Status badge */
.badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.bg-success {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  color: #ffffff;
}

/* Empty state */
.table tbody tr td[colspan] {
  padding: 25px;
  font-style: italic;
  color: #6c757d;
}

/* Responsive table */
@media (max-width: 768px) {
  .table thead {
    display: none;
  }

  .table,
  .table tbody,
  .table tr,
  .table td {
    display: block;
    width: 100%;
  }

  .table tr {
    margin-bottom: 15px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    padding: 10px;
    background: #fff;
  }

  .table td {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    font-size: 13px;
    border-bottom: 1px dashed #e0e0e0;
  }

  .table td::before {
    content: attr(data-label);
    font-weight: 600;
    color: #6c757d;
  }

  .table td:last-child {
    border-bottom: none;
  }
}















/* Container & Layout */
.container {
  max-width: 1400px;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #e9ecef;
}

.dashboard-header h1 {
  font-size: 32px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.text-muted {
  color: #6c757d;
  font-size: 14px;
}

.date-badge {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 10px 20px;
  border-radius: 25px;
  font-size: 14px;
  font-weight: 600;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* Alert */
.alert {
  padding: 12px 16px;
  margin-bottom: 20px;
  border-radius: 8px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-success {
  background-color: rgba(212, 237, 218, 0.9);
  border: 1px solid rgba(198, 233, 202, 0.9);
  color: #0f5132;
}

.btn-close {
  background: none;
  border: none;
  font-size: 18px;
  color: inherit;
  cursor: pointer;
  opacity: 0.7;
  padding: 0;
  width: 20px;
  height: 20px;
}

.btn-close:hover {
  opacity: 1;
}

.btn-close::before {
  content: '×';
  font-size: 20px;
}

/* Dashboard Cards */
.card-dashboard {
  background: white;
  border-radius: 15px;
  padding: 25px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.card-dashboard::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-dashboard.card-blue::before {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.card-dashboard.card-green::before {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.card-dashboard.card-purple::before {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-dashboard.card-orange::before {
  background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.card-dashboard:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.card-dashboard i {
  font-size: 42px;
  margin-bottom: 15px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.card-dashboard h5 {
  color: #6c757d;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.card-dashboard p {
  color: #2c3e50;
  font-size: 32px;
  font-weight: 700;
  margin: 0;
}

.card-trend {
  display: inline-block;
  margin-top: 10px;
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: 600;
  background: rgba(67, 233, 123, 0.1);
  color: #43e97b;
}

/* Stat Cards */
.stat-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
  height: 100%;
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f0f0f0;
}

.stat-header h4 {
  font-size: 18px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

.period-select {
  padding: 6px 12px;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  font-size: 14px;
  color: #495057;
  background: white;
  cursor: pointer;
}

.stat-body {
  padding: 10px 0;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
}

.stat-item:last-child {
  border-bottom: none;
}

.stat-label {
  color: #6c757d;
  font-size: 14px;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: #2c3e50;
}

.text-success {
  color: #28a745 !important;
}

.text-danger {
  color: #dc3545 !important;
}

.text-warning {
  color: #ffc107 !important;
}

.text-info {
  color: #17a2b8 !important;
}

/* Revenue Overview */
.revenue-overview {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  padding: 20px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 10px;
}

.revenue-item {
  text-align: center;
}

.revenue-label {
  display: block;
  font-size: 13px;
  color: #6c757d;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.revenue-value {
  font-size: 24px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
}

/* Transaction Breakdown */
.transaction-breakdown {
  padding-top: 20px;
}

.breakdown-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 15px;
  margin-bottom: 10px;
  background: #f8f9fa;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.breakdown-item:hover {
  background: #e9ecef;
  transform: translateX(5px);
}

.breakdown-label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: #495057;
}

.breakdown-value {
  font-size: 18px;
  font-weight: 700;
  color: #2c3e50;
}

.status-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}

.status-pending {
  background: #ffc107;
}

.status-process {
  background: #17a2b8;
}

.status-done {
  background: #28a745;
}

/* Alert Items */
.alert-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  margin-bottom: 12px;
  border-radius: 10px;
  transition: all 0.2s ease;
}

.alert-item:hover {
  transform: translateX(5px);
}

.alert-item i {
  font-size: 24px;
  width: 35px;
  text-align: center;
}

.alert-item div {
  flex: 1;
}

.alert-item strong {
  display: block;
  font-size: 20px;
  margin-bottom: 2px;
}

.alert-item span {
  font-size: 13px;
  color: #6c757d;
}

.alert-warning {
  background: rgba(255, 193, 7, 0.1);
  border-left: 4px solid #ffc107;
}

.alert-warning i {
  color: #ffc107;
}

.alert-info {
  background: rgba(23, 162, 184, 0.1);
  border-left: 4px solid #17a2b8;
}

.alert-info i {
  color: #17a2b8;
}

.alert-danger {
  background: rgba(220, 53, 69, 0.1);
  border-left: 4px solid #dc3545;
}

.alert-danger i {
  color: #dc3545;
}

/* Quick Actions */
.quick-actions-card {
  background: white;
  border-radius: 15px;
  padding: 30px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.quick-actions-card h4 {
  font-size: 20px;
  font-weight: 700;
  color: #2c3e50;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 15px;
}

.action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 20px;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
  font-weight: 600;
  color: white;
}

.action-btn i {
  font-size: 28px;
}

.action-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-success {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.btn-warning {
  background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.btn-info {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.btn-danger {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.btn-secondary {
  background: linear-gradient(135deg, #a8a8a8 0%, #7d7d7d 100%);
}

/* Responsive */
@media (max-width: 1200px) {
  .revenue-overview {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .col-md-3,
  .col-md-6,
  .col-md-8,
  .col-md-4 {
    flex: 0 0 100%;
    max-width: 100%;
  }

  .action-buttons {
    grid-template-columns: repeat(2, 1fr);
  }

  .revenue-value {
    font-size: 20px;
  }
}

/* Utility classes */
.me-2 {
  margin-right: 0.5rem;
}

.me-3 {
  margin-right: 1rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.mb-3 {
  margin-bottom: 1rem;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

.mt-4 {
  margin-top: 1.5rem;
}

.py-4 {
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}

.row {
  display: flex;
  flex-wrap: wrap;
  margin: -0.75rem;
}

.g-4>* {
  padding: 0.75rem;
}

.col-md-3 {
  flex: 0 0 25%;
  max-width: 25%;
  padding: 0.75rem;
}

.col-md-4 {
  flex: 0 0 33.333333%;
  max-width: 33.333333%;
  padding: 0.75rem;
}

.col-md-6 {
  flex: 0 0 50%;
  max-width: 50%;
  padding: 0.75rem;
}

.col-md-8 {
  flex: 0 0 66.666667%;
  max-width: 66.666667%;
  padding: 0.75rem;
}

.h-100 {
  height: 100%;
}
</style>