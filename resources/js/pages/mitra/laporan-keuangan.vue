<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

// State
const order = ref<any[]>([]);
const loading = ref(false);
const filterPeriod = ref("semua"); // semua, hari-ini, minggu-ini, bulan-ini, tahun-ini, custom
const customStartDate = ref("");
const customEndDate = ref("");

// Fetch data order
const fetchorder = async () => {
  loading.value = true;
  try {
    const response = await axios.post("/laporan-keuangan"); // sesuaikan endpoint
    order.value = response.data.data || response.data;
  } catch (error) {
    console.error(error);
    Swal.fire("Error", "Gagal memuat data order", "error");
  } finally {
    loading.value = false;
  }
};

// Filter berdasarkan periode
const filteredorder = computed(() => {
  const now = new Date();
  
  return order.value.filter((order) => {
    const orderDate = new Date(order.created_at);
    
    switch (filterPeriod.value) {
      case "hari-ini":
        return (
          orderDate.getDate() === now.getDate() &&
          orderDate.getMonth() === now.getMonth() &&
          orderDate.getFullYear() === now.getFullYear()
        );
      
      case "minggu-ini":
        const startOfWeek = new Date(now);
        startOfWeek.setDate(now.getDate() - now.getDay());
        startOfWeek.setHours(0, 0, 0, 0);
        return orderDate >= startOfWeek;
      
      case "bulan-ini":
        return (
          orderDate.getMonth() === now.getMonth() &&
          orderDate.getFullYear() === now.getFullYear()
        );
      
      case "tahun-ini":
        return orderDate.getFullYear() === now.getFullYear();
      
      case "custom":
        if (!customStartDate.value || !customEndDate.value) return true;
        const start = new Date(customStartDate.value);
        const end = new Date(customEndDate.value);
        end.setHours(23, 59, 59, 999);
        return orderDate >= start && orderDate <= end;
      
      default: // semua
        return true;
    }
  });
});

// Hitung total pendapatan
const totalPendapatan = computed(() => {
  return filteredorder.value.reduce((sum, order) => {
    // Sesuaikan dengan field total di database Anda
    return sum + (parseFloat(order.total_harga) || 0);
  }, 0);
});

// Statistik tambahan
const totalOrder = computed(() => filteredorder.value.length);

const orderBerdasarkanStatus = computed(() => {
  const stats = {
    selesai: 0,
    proses: 0,
    menunggu: 0,
    dibatalkan: 0,
  };
  
  filteredorder.value.forEach((order) => {
    const status = order.status?.toLowerCase();
    if (stats.hasOwnProperty(status)) {
      stats[status]++;
    }
  });
  
  return stats;
});

// Format currency
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
  }).format(value);
};

// Format date
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

const downloadPdf = () => {
  axios.post('/laporan-keuangan/pdf', {
    filter_period: filterPeriod.value,
    start_date: customStartDate.value,
    end_date: customEndDate.value
  }, { responseType: 'blob' }).then(res => {
    const url = window.URL.createObjectURL(res.data)
    const a = document.createElement('a')
    a.href = url
    a.download = 'laporan-keuangan.pdf'
    a.click()
  })
}
const exportToExcel = () => {
  axios.post('/laporan-keuangan/excel', {
    filter_period: filterPeriod.value,
    start_date: customStartDate.value,
    end_date: customEndDate.value
  }, { responseType: 'blob' }).then(res => {
    const url = window.URL.createObjectURL(res.data)
    const a = document.createElement('a')
    a.href = url
    a.download = 'laporan-keuangan.xlsx'
    a.click()
  })
}



// Print laporan
const printReport = () => {
  window.print();
};

onMounted(() => {
  fetchorder();
});
</script>

<template>
  <div class="card mb-10">
    <div class="card-header">
      <h2 class="mb-0">Laporan Keuangan</h2>
    </div>
    
    <div class="card-body">
      <!-- Filter Periode -->
      <div class="row mb-5">
        <div class="col-md-4">
          <label class="form-label fw-bold">Periode Laporan</label>
          <select v-model="filterPeriod" class="form-select">
            <option value="semua">Semua Waktu</option>
            <option value="hari-ini">Hari Ini</option>
            <option value="minggu-ini">Minggu Ini</option>
            <option value="bulan-ini">Bulan Ini</option>
            <option value="tahun-ini">Tahun Ini</option>
            <option value="custom">Pilih Tanggal</option>
          </select>
        </div>
        
        <!-- Custom Date Range -->
        <template v-if="filterPeriod === 'custom'">
          <div class="col-md-3">
            <label class="form-label fw-bold">Dari Tanggal</label>
            <input 
              type="date" 
              v-model="customStartDate" 
              class="form-control"
            />
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Sampai Tanggal</label>
            <input 
              type="date" 
              v-model="customEndDate" 
              class="form-control"
            />
          </div>
        </template>
        
        <div class="col-md-2 d-flex align-items-end">
          <button @click="fetchorder" class="btn btn-primary w-100">
            <i class="la la-refresh"></i> Refresh
          </button>
        </div>
      </div>

      <!-- Ringkasan Keuangan -->
      <div class="row mb-5">
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h6 class="text-white mb-2">Total Pendapatan</h6>
              <h3 class="text-white mb-0">{{ formatCurrency(totalPendapatan) }}</h3>
            </div>
          </div>
        </div>
        
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h6 class="text-white mb-2">Total Order</h6>
              <h3 class="text-white mb-0">{{ totalOrder }}</h3>
            </div>
          </div>
        </div>
        
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h6 class="text-white mb-2">Order Selesai</h6>
              <h3 class="text-white mb-0">{{ orderBerdasarkanStatus.selesai }}</h3>
            </div>
          </div>
        </div>
        
        <div class="col-md-3">
          <div class="card bg-warning text-white">
            <div class="card-body">
              <h6 class="text-white mb-2">Order Proses</h6>
              <h3 class="text-white mb-0">{{ orderBerdasarkanStatus.proses }}</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Tombol Aksi -->
      <div class="d-flex gap-2 mb-4">
        <button @click="printReport" class="btn btn-secondary">
          <i class="la la-print"></i> Print
        </button>
          <button @click="downloadPdf" class="btn btn-danger">
    <i class="la la-file-pdf"></i> Download PDF
  </button>
        <button @click="exportToExcel" class="btn btn-success">
          <i class="la la-file-excel"></i> Export Excel
        </button>
      </div>

      <!-- Tabel Detail Order -->
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>No Order</th>
              <th>Pelanggan</th>
              <th>Mitra</th>
              <th>Status</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="text-center">Loading...</td>
            </tr>
            <tr v-else-if="filteredorder.length === 0">
              <td colspan="7" class="text-center">Tidak ada data</td>
            </tr>
            <tr v-else v-for="(order, index) in filteredorder" :key="order.id">
              <td>{{ index + 1 }}</td>
              <td>{{ formatDate(order.created_at) }}</td>
              <td>{{ order.no_order || order.id }}</td>
              <td>{{ order.pelanggan?.name || '-' }}</td>
              <td>{{ order.mitra?.nama_laundry || '-' }}</td>
              <td>
                <span 
                  :class="{
                    'badge bg-success': order.status === 'selesai',
                    'badge bg-warning': order.status === 'proses',
                    'badge bg-secondary': order.status === 'menunggu',
                    'badge bg-danger': order.status === 'dibatalkan'
                  }"
                >
                  {{ order.status?.toUpperCase() }}
                </span>
              </td>
              <td class="text-end fw-bold">{{ formatCurrency(order.total_harga) }}</td>
            </tr>
          </tbody>
          <tfoot v-if="filteredorder.length > 0" class="table-light">
            <tr>
              <td colspan="6" class="text-end fw-bold">TOTAL PENDAPATAN:</td>
              <td class="text-end fw-bold text-primary fs-5">
                {{ formatCurrency(totalPendapatan) }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
@media print {
  .btn, .form-select, .form-control {
    display: none;
  }
}
</style>