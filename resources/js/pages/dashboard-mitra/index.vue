<template>
  <div class="dashboard-mitra">
    <!-- Header Section -->
    <div class="header-section">
      <div class="welcome-text">
        <h1>Dashboard Mitra</h1>
        <p class="subtitle">Kelola pesanan laundry Anda dengan mudah</p>
      </div>
      <div class="quick-actions">
        <button class="btn-refresh" @click="loadDashboard">

          <span class="icon">🔄</span>
          Refresh
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card pending">
        <div class="stat-icon">📥</div>
        <div class="stat-content">
          <h3>Order Masuk</h3>
          <p class="stat-number">{{ order.menunggu_konfirmasi_mitra }}</p>
          <span class="stat-label">Menunggu konfirmasi</span>
        </div>
      </div>

      <div class="stat-card processing">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <h3>Diterima</h3>
          <p class="stat-number">{{ order.diterima }}</p>
          <span class="stat-label">Order Diterima</span>
        </div>
      </div>
      <div class="stat-card processing">
        <div class="stat-icon">❌</div>
        <div class="stat-content">
          <h3>Ditolak</h3>
          <p class="stat-number">{{ order.ditolak }}</p>
          <span class="stat-label">Order Ditolak</span>
        </div>
      </div>
      <div class="stat-card processing">
        <div class="stat-icon">⚙️</div>
        <div class="stat-content">
          <h3>Diproses</h3>
          <p class="stat-number">{{ order.diproses }}</p>
          <span class="stat-label">Sedang dikerjakan</span>
        </div>
      </div>

      <div class="stat-card ready">
        <div class="stat-icon">📦</div>
        <div class="stat-content">
          <h3>Siap Diambil</h3>
          <p class="stat-number">{{ order.siap_diambil }}</p>
          <span class="stat-label">Siap untuk pelanggan</span>
        </div>
      </div>

      <div class="stat-card completed">
        <div class="stat-icon">🎉</div>
        <div class="stat-content">
          <h3>Selesai Hari Ini</h3>
          <p class="stat-number">{{ order.selesai }}</p>
          <span class="stat-label">Transaksi selesai</span>
        </div>
      </div>
    </div>

    <!-- order Section -->
    <div class="order-section">
      <div class="order-card total">
        <div class="order-icon">📊</div>
        <div class="order-content">
          <h4>Total Order Aktif</h4>
          <p class="order-number">{{ totalOrderAktif }}</p>
        </div>
      </div>

      <div class="order-card info">
        <div class="order-icon">💡</div>
        <div class="order-content">
          <h4>Status</h4>
          <p class="order-text">{{ statusMessage }}</p>
        </div>
      </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
      <div class="chart-card">
        <h3>📈 Statistik Order Mingguan</h3>
        <div class="chart-container">
          <div v-for="(day, index) in weeklyStats" :key="index" class="chart-bar">
            <div class="bar-fill" :style="{ height: getBarHeight(day.count) + '%' }">
              <span class="bar-value">{{ day.count }}</span>
            </div>
            <span class="bar-label">{{ day.day }}</span>
          </div>
        </div>
      </div>

      <div class="revenue-card">
        <h3>💰 Pendapatan Hari Ini</h3>
        <p class="revenue-amount">Rp {{ formatCurrency(todayRevenue) }}</p>
        <div class="revenue-detail">
          <span class="detail-item">
            <span class="detail-icon">📦</span>
            <span>{{ order.selesai }} transaksi</span>
          </span>
          <span class="detail-item">
            <span class="detail-icon">📊</span>
            <span>Rata-rata: Rp {{ formatCurrency(averageOrder) }}</span>
          </span>
        </div>
      </div>
    </div>

    <div class="recent-orders-section">
      <div class="section-header">
        <h3>📋 Data Order</h3>
        <!-- <button class="btn-view-all" @click="viewAllOrders">Lihat Semua</button> -->
      </div>

      <div class="orders-list">
        <div v-for="order in pesananTerbaru" :key="order.id" class="order-item">
          <div class="order-info">
            <div class="order-id">Order {{ order.id }}</div>
            <div class="order-kode">Kode Order : {{ order.kode_order }}</div>
            <div class="order-customer">Pelanggan {{ order.pelanggan_id }}</div>
            <!-- <div class="order-time">{{ order.time }} </div> -->
            <div class="order-time">
              {{ order.datetime }} • {{ order.time_ago }}
            </div>

            <!-- <div class="order-time">
  {{ order.datetime }}
</div>
<div class="order-time text-muted">
  {{ order.time_ago }}
</div> -->

            <!-- <div class="order-time">
  {{ order.tanggal }} {{ order.jam }}
</div> -->

          </div>

          <div class="order-status" :class="order.status">
            {{ getStatusLabel(order.status) }}
          </div>

          <!-- DETAIL MUNCUL DI BAWAH -->
          <div v-if="openedOrderId === order.id" class="order-detail">
            <p><b>Status:</b> {{ getStatusLabel(order.status) }}</p>
            <p><b>Waktu:</b> {{ order.time }}</p>

            <button class="btn-detail" @click.stop="navigateTo(`/order/${order.id}`)">
              Lihat Detail
            </button>
          </div>
        </div>
      </div>
    </div>


    <!-- Quick Actions Panel -->
    <!-- <div class="quick-actions-panel">
      <h3>⚡ Aksi Cepat</h3>
      <div class="actions-grid">
        <button class="action-btn" @click="navigateTo('/orders')">
          <span class="action-icon">📥</span>
          <span class="action-text">Kelola Order</span>
        </button>
        <button class="action-btn" @click="navigateTo('/products')">
          <span class="action-icon">🏷️</span>
          <span class="action-text">Atur Harga</span>
        </button>
        <button class="action-btn" @click="navigateTo('/schedule')">
          <span class="action-icon">📅</span>
          <span class="action-text">Jadwal Pickup</span>
        </button>
        <button class="action-btn" @click="navigateTo('/reports')">
          <span class="action-icon">📊</span>
          <span class="action-text">Laporan</span>
        </button>
      </div>
    </div> -->

    <!-- Performance Tips -->
    <!-- <div class="tips-section">
      <div class="tip-card">
        <span class="tip-icon">💡</span>
        <div class="tip-content">
          <h4>Tips Hari Ini</h4>
          <p>{{ dailyTip }}</p>
        </div>
      </div>
    </div> -->

    <!-- Notification Badge -->
    <div v-if="notif" class="notification-badge">
      <span class="badge-icon">🔔</span>
      <span class="badge-text">Ada {{ jumlahOrderBaru }} order baru!</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import Swal from "sweetalert2";

const notif = ref(false);
const jumlahOrderBaru = ref(0);
const weeklyStats = ref([]);
const pesananTerbaru = ref([]);
const todayRevenue = ref(0);

const checkNotif = async () => {
  try {
    const res = await axios.get("/notif-order");

    const adaOrderBaru = res.data.new_order;
    const jumlah = res.data.count;

    // ⬇️ Notifikasi ditaruh di sini
    if (adaOrderBaru) {
      toast.info(`📢 Ada ${jumlah} order baru masuk!`);
      // loadOrder(); // refresh tabel order
    }

  } catch (e) {
    console.error("Gagal cek notifikasi", e);
  }
};

const openedOrderId = ref<number | null>(null);

// const toggleOrder = (id: number) => {
//   openedOrderId.value = openedOrderId.value === id ? null : id;
// };


const order = ref({
  menunggu_konfirmasi_mitra: 0,
  ditolak: 0,
  diterima: 0,
  diproses: 0,
  siap_diambil: 0,
  selesai: 0,
});


const getBarHeight = (count: number) => {
  const max = Math.max(...weeklyStats.value.map(d => d.count));

  if (max === 0) return 0;

  const height = (count / max) * 100;

  return count > 0 ? Math.max(height, 10) : 0;
};




// const getBarHeight = (count: number) => {
//   if (!weeklyStats.value.length) return 0;

//   const maxCount = Math.max(...weeklyStats.value.map(s => s.count));

//   if (maxCount === 0) return 0;

//   return (count / maxCount) * 100;
// };


const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID').format(value);
};

const navigateTo = (path: string) => {
  console.log('Navigate to:', path);
  // Implementasi navigasi sesuai router Vue Anda
};

const viewAllOrders = () => {
  navigateTo('/order-masuk');
};

const labels = {
  pending: 'Menunggu',
  ditolak: 'Ditolak',
  diterima: 'Diterima',
  processing: 'Sedang Diproses',
  ready: 'Siap',
  completed: 'Selesai'
};
const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    menunggu_konfirmasi_mitra: 'Menunggu Konfirmasi',
    ditolak: 'Ditolak',
    diterima: 'Diterima',
    diproses: 'Sedang Diproses',
    siap_diambil: 'Siap Diambil',
    selesai: 'Selesai'
  };
  return labels[status] || status;
};


const loadDashboard = async () => {
  try {
    const res = await axios.get("/dashboard-data");

    order.value = {
      menunggu_konfirmasi_mitra: res.data.order?.menunggu_konfirmasi_mitra ?? 0,
      diterima: res.data.order?.diterima ?? 0,
      ditolak: res.data.order?.ditolak ?? 0,
      diproses: res.data.order?.diproses ?? 0,
      siap_diambil: res.data.order?.siap_diambil ?? 0,
      selesai: res.data.order?.selesai ?? 0,
    };

    weeklyStats.value = res.data.weekly ?? [];
    todayRevenue.value = res.data.today_revenue ?? 0;
    pesananTerbaru.value = res.data.pesananTerbaru ?? [];

  } catch (e) {
    toast.error("Gagal memuat dashboard");
  }
};


// Computed properties
const totalOrderAktif = computed(() => {
  return order.value.menunggu_konfirmasi_mitra +
    order.value.diterima +
    order.value.ditolak +
    order.value.diproses +
    order.value.siap_diambil;
});

const averageOrder = computed(() => {
  if (order.value.selesai === 0) return 0;
  return Math.round(todayRevenue.value / order.value.selesai);
});

const statusMessage = computed(() => {
  if (order.value.menunggu_konfirmasi_mitra > 0) {
    return "Ada order menunggu konfirmasi";
  } else if (order.value.siap_diambil > 0) {
    return "Ada order siap diambil";
  } else if (order.value.diproses > 0) {
    return "Semua berjalan lancar";
  } else if (order.value.ditolak > 0) {
    return "Order ada yang ditolak";
  } else if (order.value.diterima > 0) {
    return "Ada order yang diterima";
  }
  return "Tidak ada order aktif";
});


onMounted(() => {
  loadDashboard();
  setInterval(checkNotif, 20000);
});

</script>

<style scoped>
.chart-container {
  display: flex;
  align-items: flex-end;
  height: 180px;
  gap: 12px;
}

.chart-bar {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  height: 35%;
}

.bar-fill {
  width: 100%;
  background: linear-gradient(180deg, #7c3aed, #4f46e5);
  border-radius: 8px 8px 0 0;
  transition: height 0.3s ease;
  position: relative;
}

.bar-value {
  color: white;
  font-size: 12px;
  text-align: center;
  position: absolute;
  bottom: 4px;
  width: 100%;
}

.bar-label {
  margin-top: 6px;
  text-align: center;
  font-size: 13px;
}










.order-status {
  pointer-events: none;
}


.order-item {
  position: relative;
  border-bottom: 1px solid #eee;
  padding: 12px;
}

.order-item.clickable {
  cursor: pointer;
}

.order-detail {
  margin-top: 10px;
  padding: 10px;
  background: #f8fafc;
  border-radius: 8px;
  font-size: 14px;
}

.btn-detail {
  margin-top: 8px;
  padding: 6px 10px;
  background: #2563eb;
  color: white;
  border-radius: 6px;
}




.dashboard-mitra {
  padding: 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

/* Header Section */
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  flex-wrap: wrap;
  gap: 16px;
}

.welcome-text h1 {
  color: white;
  font-size: 32px;
  font-weight: 700;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.subtitle {
  color: rgba(255, 255, 255, 0.9);
  margin: 8px 0 0 0;
  font-size: 16px;
}

.quick-actions {
  display: flex;
  gap: 12px;
}

.btn-refresh {
  background: white;
  border: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

.btn-refresh:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.icon {
  font-size: 18px;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: all 0.3s ease;
  border-left: 4px solid;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.15);
}

.stat-card.pending {
  border-left-color: #f59e0b;
}

.stat-card.processing {
  border-left-color: #3b82f6;
}

.stat-card.ready {
  border-left-color: #10b981;
}

.stat-card.completed {
  border-left-color: #8b5cf6;
}

.stat-icon {
  font-size: 48px;
  line-height: 1;
}

.stat-content {
  flex: 1;
}

.stat-content h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.stat-number {
  margin: 8px 0 4px 0;
  font-size: 36px;
  font-weight: 700;
  color: #111827;
  line-height: 1;
}

.stat-label {
  font-size: 13px;
  color: #6b7280;
}

/* order Section */
.order-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.order-card {
  background: white;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  gap: 20px;
}

.order-icon {
  font-size: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  width: 64px;
  height: 64px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.order-content h4 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.order-number {
  margin: 8px 0 0 0;
  font-size: 32px;
  font-weight: 700;
  color: #111827;
}

.order-text {
  margin: 8px 0 0 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

/* Notification Badge */
.notification-badge {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #10b981;
  color: white;
  padding: 16px 24px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
  animation: slideIn 0.3s ease;
  z-index: 1000;
}

.badge-icon {
  font-size: 24px;
  animation: ring 2s infinite;
}

@keyframes slideIn {
  from {
    transform: translateX(400px);
    opacity: 0;
  }

  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes ring {

  0%,
  100% {
    transform: rotate(0deg);
  }

  10%,
  30% {
    transform: rotate(-10deg);
  }

  20%,
  40% {
    transform: rotate(10deg);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-mitra {
    padding: 16px;
  }

  .welcome-text h1 {
    font-size: 24px;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .stat-card {
    padding: 20px;
  }

  .stat-icon {
    font-size: 40px;
  }

  .stat-number {
    font-size: 28px;
  }

  .notification-badge {
    bottom: 16px;
    right: 16px;
    left: 16px;
    justify-content: center;
  }
}

/* Chart Section */
.chart-section {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-bottom: 24px;
}

.chart-card,
.revenue-card {
  background: white;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.chart-card h3,
.revenue-card h3 {
  margin: 0 0 20px 0;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.chart-container {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 200px;
  gap: 8px;
  padding: 10px 0;
}

.chart-bar {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.bar-fill {
  width: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 8px 8px 0 0;
  position: relative;
  min-height: 20px;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding-top: 8px;
  transition: all 0.3s ease;
}

.bar-fill:hover {
  opacity: 0.8;
  transform: translateY(-4px);
}

.bar-value {
  color: white;
  font-weight: 600;
  font-size: 12px;
}

.bar-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
}

.revenue-amount {
  font-size: 36px;
  font-weight: 700;
  color: #10b981;
  margin: 16px 0;
}

.revenue-detail {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #6b7280;
}

.detail-icon {
  font-size: 18px;
}

/* Recent Orders */
.recent-orders-section {
  background: white;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.btn-view-all {
  background: transparent;
  border: 2px solid #667eea;
  color: #667eea;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-view-all:hover {
  background: #667eea;
  color: white;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: #f9fafb;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.order-item:hover {
  background: #f3f4f6;
  transform: translateX(4px);
}

.order-info {
  flex: 1;
}

.order-id {
  font-weight: 600;
  color: #1f2937;
  font-size: 14px;
}

.order-kode {
  font-weight: 600;
  color: #1f2937;
  font-size: 14px;
}

.order-customer {
  color: #6b7280;
  font-size: 13px;
  margin-top: 4px;
}

.order-time {
  color: #9ca3af;
  font-size: 12px;
  margin-top: 2px;
}

.order-status {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.order-status.pending {
  background: #fef3c7;
  color: #d97706;
}

.order-status.processing {
  background: #dbeafe;
  color: #2563eb;
}

.order-status.ready {
  background: #d1fae5;
  color: #059669;
}

.order-status.completed {
  background: #e0e7ff;
  color: #7c3aed;
}

/* Quick Actions Panel */
.quick-actions-panel {
  background: white;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 24px;
}

.quick-actions-panel h3 {
  margin: 0 0 20px 0;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 16px;
}

.action-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  padding: 20px;
  border-radius: 12px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  transition: all 0.3s ease;
  color: white;
}

.action-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
}

.action-icon {
  font-size: 32px;
}

.action-text {
  font-size: 14px;
  font-weight: 600;
}

/* Tips Section */
.tips-section {
  margin-bottom: 24px;
}

.tip-card {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
  padding: 20px 24px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 4px 20px rgba(251, 191, 36, 0.3);
}

.tip-icon {
  font-size: 32px;
}

.tip-content h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: white;
}

.tip-content p {
  margin: 8px 0 0 0;
  font-size: 14px;
  color: rgba(255, 255, 255, 0.95);
  line-height: 1.5;
}

@media (max-width: 1024px) {
  .chart-section {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
