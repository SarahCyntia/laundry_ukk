<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const selectedPeriod = ref('today');
const customStartDate = ref('');
const customEndDate = ref('');
const loading = ref(false);

const report = ref({
  period: '',
  summary: {
    total_pendapatan: 0,
    total_transaksi: 0,
    rata_rata_per_transaksi: 0
  },
  by_payment_method: {},
  by_service: {},
  transaksi: []
});

const comparison = ref({
  monthly_comparison: {
    this_month: { pendapatan: 0, transaksi: 0 },
    last_month: { pendapatan: 0, transaksi: 0 }
  },
  weekly_comparison: {
    this_week: { pendapatan: 0, transaksi: 0 },
    last_week: { pendapatan: 0, transaksi: 0 }
  },
  daily_comparison: {
    today: { pendapatan: 0, transaksi: 0 },
    yesterday: { pendapatan: 0, transaksi: 0 }
  }
});

const periods = [
  { value: 'today', label: 'Hari Ini' },
  { value: 'yesterday', label: 'Kemarin' },
  { value: 'this_week', label: 'Minggu Ini' },
  { value: 'last_week', label: 'Minggu Lalu' },
  { value: 'this_month', label: 'Bulan Ini' },
  { value: 'last_month', label: 'Bulan Lalu' },
  { value: 'custom', label: 'Custom' }
];

const dailyDifference = computed(() => {
  return comparison.value.daily_comparison.today.pendapatan - 
         comparison.value.daily_comparison.yesterday.pendapatan;
});

const weeklyDifference = computed(() => {
  return comparison.value.weekly_comparison.this_week.pendapatan - 
         comparison.value.weekly_comparison.last_week.pendapatan;
});

const monthlyDifference = computed(() => {
  return comparison.value.monthly_comparison.this_month.pendapatan - 
         comparison.value.monthly_comparison.last_month.pendapatan;
});

const selectPeriod = (period) => {
  selectedPeriod.value = period;
  if (period !== 'custom') {
    loadReport();
  }
};

const loadReport = async () => {
  try {
    loading.value = true;
    const params = { period: selectedPeriod.value };
    
    if (selectedPeriod.value === 'custom') {
      params.start_date = customStartDate.value;
      params.end_date = customEndDate.value;
    }

    const response = await axios.get('/api/finance/report', { params });
    report.value = response.data.data;
  } catch (error) {
    console.error('Error loading report:', error);
    alert('Gagal memuat laporan keuangan');
  } finally {
    loading.value = false;
  }
};

const loadComparison = async () => {
  try {
    const response = await axios.get('/api/finance/comparison');
    comparison.value = response.data.data;
  } catch (error) {
    console.error('Error loading comparison:', error);
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
};

const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatPaymentMethod = (method) => {
  const methods = {
    'bank_transfer': 'Transfer Bank',
    'qris': 'QRIS',
    'gopay': 'GoPay',
    'shopeepay': 'ShopeePay',
    'ovo': 'OVO',
    'credit_card': 'Kartu Kredit',
    'cstore': 'Convenience Store'
  };
  return methods[method] || method;
};

onMounted(() => {
  loadReport();
  loadComparison();
});
</script>

<template>
  <!-- <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100"> -->
    <div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-semibold mb-4 text-gray-800">Laporan Keuangan Laundry</h1>

      <!-- Filter Periode -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-200">
        <h2 class="text-2xl font-bold mb-5 text-gray-900">Pilih Periode</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
          <button
            v-for="period in periods"
            :key="period.value"
            @click="selectPeriod(period.value)"
            :class="[
              'px-4 py-3 rounded-lg font-semibold transition-all duration-200 text-sm',
              selectedPeriod === period.value
                ? 'bg-blue-600 text-white shadow-md transform scale-105'
                : 'bg-gray-100 text-gray-800 hover:bg-gray-200 border border-gray-300'
            ]"
          >
            {{ period.label }}
          </button>
        </div>

        <!-- Custom Date Range -->
        <div v-if="selectedPeriod === 'custom'" class="mt-6 flex flex-wrap gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">Dari Tanggal</label>
            <input
              type="date"
              v-model="customStartDate"
              class="border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
            />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">Sampai Tanggal</label>
            <input
              type="date"
              v-model="customEndDate"
              class="border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="loadReport"
              class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 font-semibold shadow-md transition-all"
            >
              Tampilkan
            </button>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-transform">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-bold">Total Pendapatan</h3>
            <svg class="w-10 h-10 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <p class="text-4xl font-bold mb-2">{{ formatCurrency(report.summary.total_pendapatan) }}</p>
          <p class="text-sm font-medium opacity-90">{{ report.period }}</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-transform">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-bold">Total Transaksi</h3>
            <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <p class="text-4xl font-bold mb-2">{{ report.summary.total_transaksi }}</p>
          <p class="text-sm font-medium opacity-90">Transaksi</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-xl p-6 text-white transform hover:scale-105 transition-transform">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-bold">Rata-rata/Transaksi</h3>
            <svg class="w-10 h-10 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <p class="text-4xl font-bold mb-2">{{ formatCurrency(report.summary.rata_rata_per_transaksi) }}</p>
          <p class="text-sm font-medium opacity-90">Per order</p>
        </div>
      </div>

      <!-- Comparison Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Daily Comparison -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
          <h2 class="text-xl font-bold mb-5 text-gray-900">Hari Ini vs Kemarin</h2>
          <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
              <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-gray-900">Hari Ini</span>
                <span class="text-xl font-bold text-blue-600">
                  {{ formatCurrency(comparison.daily_comparison.today.pendapatan) }}
                </span>
              </div>
              <div class="text-sm font-medium text-gray-700">
                {{ comparison.daily_comparison.today.transaksi }} transaksi
              </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-gray-400">
              <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-gray-900">Kemarin</span>
                <span class="text-xl font-bold text-gray-700">
                  {{ formatCurrency(comparison.daily_comparison.yesterday.pendapatan) }}
                </span>
              </div>
              <div class="text-sm font-medium text-gray-600">
                {{ comparison.daily_comparison.yesterday.transaksi }} transaksi
              </div>
            </div>
            <div class="pt-3 border-t-2 border-gray-200">
              <div class="flex justify-between items-center">
                <span class="font-bold text-gray-900">Selisih</span>
                <span :class="[
                  'text-xl font-bold',
                  dailyDifference >= 0 ? 'text-green-600' : 'text-red-600'
                ]">
                  {{ dailyDifference >= 0 ? '+' : '' }}{{ formatCurrency(Math.abs(dailyDifference)) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Weekly Comparison -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
          <h2 class="text-xl font-bold mb-5 text-gray-900">Minggu Ini vs Minggu Lalu</h2>
          <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
              <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-gray-900">Minggu Ini</span>
                <span class="text-xl font-bold text-blue-600">
                  {{ formatCurrency(comparison.weekly_comparison.this_week.pendapatan) }}
                </span>
              </div>
              <div class="text-sm font-medium text-gray-700">
                {{ comparison.weekly_comparison.this_week.transaksi }} transaksi
              </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-gray-400">
              <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-gray-900">Minggu Lalu</span>
                <span class="text-xl font-bold text-gray-700">
                  {{ formatCurrency(comparison.weekly_comparison.last_week.pendapatan) }}
                </span>
              </div>
              <div class="text-sm font-medium text-gray-600">
                {{ comparison.weekly_comparison.last_week.transaksi }} transaksi
              </div>
            </div>
            <div class="pt-3 border-t-2 border-gray-200">
              <div class="flex justify-between items-center">
                <span class="font-bold text-gray-900">Selisih</span>
                <span :class="[
                  'text-xl font-bold',
                  weeklyDifference >= 0 ? 'text-green-600' : 'text-red-600'
                ]">
                  {{ weeklyDifference >= 0 ? '+' : '' }}{{ formatCurrency(Math.abs(weeklyDifference)) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Monthly Comparison -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
          <h2 class="text-xl font-bold mb-5 text-gray-900">Bulan Ini vs Bulan Lalu</h2>
          <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
              <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-gray-900">Bulan Ini</span>
                <span class="text-xl font-bold text-blue-600">
                  {{ formatCurrency(comparison.monthly_comparison.this_month.pendapatan) }}
                </span>
              </div>
              <div class="text-sm font-medium text-gray-700">
                {{ comparison.monthly_comparison.this_month.transaksi }} transaksi
              </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-gray-400">
              <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-gray-900">Bulan Lalu</span>
                <span class="text-xl font-bold text-gray-700">
                  {{ formatCurrency(comparison.monthly_comparison.last_month.pendapatan) }}
                </span>
              </div>
              <div class="text-sm font-medium text-gray-600">
                {{ comparison.monthly_comparison.last_month.transaksi }} transaksi
              </div>
            </div>
            <div class="pt-3 border-t-2 border-gray-200">
              <div class="flex justify-between items-center">
                <span class="font-bold text-gray-900">Selisih</span>
                <span :class="[
                  'text-xl font-bold',
                  monthlyDifference >= 0 ? 'text-green-600' : 'text-red-600'
                ]">
                  {{ monthlyDifference >= 0 ? '+' : '' }}{{ formatCurrency(Math.abs(monthlyDifference)) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Breakdown Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- By Payment Method -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
          <h2 class="text-2xl font-bold mb-5 text-gray-900">Berdasarkan Metode Pembayaran</h2>
          <div class="space-y-3">
            <div v-for="(data, method) in report.by_payment_method" :key="method" 
                 class="flex justify-between items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200 hover:shadow-md transition-shadow">
              <div>
                <p class="font-bold text-gray-900 text-base">{{ formatPaymentMethod(method) }}</p>
                <p class="text-sm font-medium text-gray-700">{{ data.jumlah_transaksi }} transaksi</p>
              </div>
              <p class="font-bold text-blue-600 text-lg">{{ formatCurrency(data.total) }}</p>
            </div>
            <div v-if="Object.keys(report.by_payment_method).length === 0" class="text-center text-gray-600 py-8 font-medium">
              Tidak ada data
            </div>
          </div>
        </div>

        <!-- By Service -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
          <h2 class="text-2xl font-bold mb-5 text-gray-900">Berdasarkan Jenis Layanan</h2>
          <div class="space-y-3">
            <div v-for="(data, service) in report.by_service" :key="service" 
                 class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 hover:shadow-md transition-shadow">
              <div>
                <p class="font-bold text-gray-900 text-base">{{ service }}</p>
                <p class="text-sm font-medium text-gray-700">{{ data.jumlah_transaksi }} transaksi</p>
              </div>
              <p class="font-bold text-green-600 text-lg">{{ formatCurrency(data.total) }}</p>
            </div>
            <div v-if="Object.keys(report.by_service).length === 0" class="text-center text-gray-600 py-8 font-medium">
              Tidak ada data
            </div>
          </div>
        </div>
      </div>

      <!-- Transaction List -->
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Riwayat Transaksi</h2>
          <span class="text-sm font-bold text-gray-700 bg-gray-200 px-4 py-2 rounded-full">{{ report.transaksi.length }} transaksi</span>
        </div>
        
        <div v-if="report.transaksi.length === 0" class="text-center py-16 text-gray-500">
          <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
          </svg>
          <p class="text-xl font-semibold text-gray-700">Tidak ada transaksi pada periode ini</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gradient-to-r from-gray-100 to-gray-200">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">Kode Order</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">Pelanggan</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">Layanan</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">Berat</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">Metode</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-gray-800 uppercase tracking-wider">Total</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="transaksi in report.transaksi" :key="transaksi.id" class="hover:bg-blue-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ formatDateTime(transaksi.waktu_bayar) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                  {{ transaksi.kode_order }}
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                  {{ transaksi.nama_pelanggan }}
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-700">
                  {{ transaksi.jenis_layanan }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                  {{ transaksi.berat_aktual }} kg
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-200 text-blue-900">
                    {{ formatPaymentMethod(transaksi.metode_pembayaran) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600 text-base">
                  {{ formatCurrency(transaksi.total_bayar) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ================= GLOBAL ================= */
.min-h-screen {
  min-height: 100vh;
  background: #f3f4f6;
}

.container {
  max-width: 1280px;
  margin: 0 auto;
}

h1 {
  color: #1f2937;
}

/* ================= CARD ================= */
.bg-white {
  background: #ffffff;
}

.border {
  border: 1px solid #e5e7eb;
}

.rounded-xl {
  border-radius: 12px;
}

.shadow-lg {
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.shadow-xl {
  box-shadow: 0 15px 30px rgba(0,0,0,0.12);
}

/* ================= BUTTON ================= */
button {
  cursor: pointer;
}

.bg-blue-600 {
  background: #2563eb;
}

.bg-blue-700:hover {
  background: #1d4ed8;
}

.text-white {
  color: #ffffff;
}

/* ================= SUMMARY CARD GRADIENT ================= */
.from-green-500 {
  background: linear-gradient(135deg, #22c55e, #16a34a);
}

.from-blue-500 {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.from-purple-500 {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

/* ================= TEXT ================= */
.text-gray-900 { color: #111827; }
.text-gray-800 { color: #1f2937; }
.text-gray-700 { color: #374151; }
.text-gray-600 { color: #4b5563; }
.text-gray-500 { color: #6b7280; }

.text-blue-600 { color: #2563eb; }
.text-green-600 { color: #16a34a; }
.text-red-600 { color: #dc2626; }

/* ================= BADGE ================= */
.bg-blue-200 {
  background: #bfdbfe;
}

.text-blue-900 {
  color: #1e3a8a;
}

/* ================= TABLE ================= */
table {
  border-collapse: collapse;
  width: 100%;
}

thead {
  background: #e5e7eb;
}

th {
  font-weight: 700;
  font-size: 12px;
}

td {
  font-size: 14px;
}

tbody tr:hover {
  background: #eff6ff;
}

/* ================= EMPTY STATE ================= */
svg.w-20 {
  width: 48px !important;
  height: 48px !important;
  opacity: 0.6;
}

/* ================= ICON FIX ================= */
svg.w-10 {
  width: 24px !important;
  height: 24px !important;
}

svg.w-4 {
  width: 18px !important;
  height: 18px !important;
}

/* ================= TRANSITION ================= */
.transition-all,
.transition-transform,
.transition-colors {
  transition: all 0.2s ease;
}

.hover\:scale-105:hover {
  transform: scale(1.03);
}
</style>
