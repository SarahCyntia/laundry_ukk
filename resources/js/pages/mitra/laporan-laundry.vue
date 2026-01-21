<script setup lang="ts">
import { computed, onMounted, ref, watch, nextTick } from "vue";
import { createColumnHelper, type Row } from "@tanstack/vue-table";
import type { Order } from "@/types";
import { useDelete } from "@/libs/hooks";
import { h } from "vue";
// import Form from "./form-order-masuk.vue";
import Swal from "sweetalert2";
import axios from "@/libs/axios";
import { saveAs } from 'file-saver';
// import * as XLSX from 'xlsx';

const mitraId = ref<number | null>(null);
const column = createColumnHelper();
const selected = ref<string>("");
const openForm = ref<boolean>(false);
const order = ref<Order | null>(null);

// ===== FILTER LAPORAN =====
const filterType = ref<'daily' | 'weekly' | 'monthly'>('monthly');
const selectedDate = ref<string>(new Date().toISOString().split('T')[0]); // YYYY-MM-DD
const selectedMonth = ref<string>(new Date().toISOString().slice(0, 7)); // YYYY-MM
const selectedWeek = ref<string>('');

// Generate minggu dalam bulan
const weekOptions = computed(() => {
  if (!selectedMonth.value) return [];
  const [year, month] = selectedMonth.value.split('-').map(Number);
  const weeks: Array<{label: string, start: string, end: string}> = [];
  
  const firstDay = new Date(year, month - 1, 1);
  const lastDay = new Date(year, month, 0);
  
  let currentWeek = 1;
  let weekStart = new Date(firstDay);
  
  while (weekStart <= lastDay) {
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekStart.getDate() + 6);
    
    if (weekEnd > lastDay) {
      weeks.push({
        label: `Minggu ${currentWeek} (${weekStart.getDate()} - ${lastDay.getDate()})`,
        start: weekStart.toISOString().split('T')[0],
        end: lastDay.toISOString().split('T')[0]
      });
    } else {
      weeks.push({
        label: `Minggu ${currentWeek} (${weekStart.getDate()} - ${weekEnd.getDate()})`,
        start: weekStart.toISOString().split('T')[0],
        end: weekEnd.toISOString().split('T')[0]
      });
    }
    
    weekStart = new Date(weekEnd);
    weekStart.setDate(weekEnd.getDate() + 1);
    currentWeek++;
  }
  
  return weeks;
});

// AMBIL MITRA_ID DARI PROFILE USER
onMounted(async () => {
  const { data } = await axios.get("/mitra");
  mitraId.value = data.mitra_id;
  
  // Set default minggu pertama
  if (weekOptions.value.length > 0) {
    selectedWeek.value = `${weekOptions.value[0].start}|${weekOptions.value[0].end}`;
  }
});

// Watch untuk update selectedWeek saat bulan berubah
watch(selectedMonth, () => {
  if (weekOptions.value.length > 0) {
    selectedWeek.value = `${weekOptions.value[0].start}|${weekOptions.value[0].end}`;
  }
});

const paginateRef = ref<any>(null);
const refresh = () => paginateRef.value?.refetch();

const { delete: deleteOrder } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
});

const inputData = ref<Order | null>(null);

const statusSteps = [
  "menunggu_konfirmasi_mitra",
  "ditunggu_mitra",
  "diterima",
  "ditolak",
  "diproses",
  "dicuci",
  "dikeringkan",
  "disetrika",
  "siap_diambil",
  "selesai"
] as const;

const statusLabels = {
  menunggu_konfirmasi_mitra: "Menunggu Konfirmasi Mitra",
  ditunggu_mitra: "Ditunggu Mitra",
  diterima: "Diterima",
  ditolak: "Ditolak",
  diproses: "Diproses",
  dicuci: "Dicuci",
  dikeringkan: "Dikeringkan",
  disetrika: "Disetrika",
  siap_diambil: "Siap Diambil",
  selesai: "Selesai"
};

const statusColors = {
  menunggu_konfirmasi_mitra: "bg-info",
  ditunggu_mitra: "bg-info",
  diterima: "bg-success",
  ditolak: "bg-danger",
  diproses: "bg-warning",
  dicuci: "bg-muted",
  dikeringkan: "bg-warning",
  disetrika: "bg-primary",
  siap_diambil: "bg-info",
  selesai: "bg-success"
};

const statusIcons = {
  menunggu_konfirmasi_mitra: "info",
  ditunggu_mitra: "info",
  diterima: "question",
  ditolak: "error",
  diproses: "info",
  dicuci: "info",
  dikeringkan: "warning",
  disetrika: "info",
  siap_diambil: "info",
  selesai: "success"
};

// URL dengan filter tanggal
const url = computed(() => {
  const params = new URLSearchParams();

  // Status yang tidak ditampilkan
  [
    'diproses',
    'dicuci',
    'dikeringkan',
    'disetrika',
    'siap_ambil',
    'selesai'
  ].forEach(status => {
    params.append('exclude_status[]', status);
  });

  // Filter berdasarkan tipe
  if (filterType.value === 'daily' && selectedDate.value) {
    params.append('filter_date', selectedDate.value);
  } else if (filterType.value === 'weekly' && selectedWeek.value) {
    const [start, end] = selectedWeek.value.split('|');
    params.append('filter_start_date', start);
    params.append('filter_end_date', end);
  } else if (filterType.value === 'monthly' && selectedMonth.value) {
    params.append('filter_month', selectedMonth.value);
  }

  return `/order?${params.toString()}`;
});

// ===== EXPORT FUNCTIONS =====
const exportToExcel = async () => {
  try {
    const params = new URLSearchParams();
    
    // Ambil semua data tanpa pagination
    params.append('per_page', '9999');
    
    // Filter berdasarkan tipe
    if (filterType.value === 'daily' && selectedDate.value) {
      params.append('filter_date', selectedDate.value);
    } else if (filterType.value === 'weekly' && selectedWeek.value) {
      const [start, end] = selectedWeek.value.split('|');
      params.append('filter_start_date', start);
      params.append('filter_end_date', end);
    } else if (filterType.value === 'monthly' && selectedMonth.value) {
      params.append('filter_month', selectedMonth.value);
    }

    const { data } = await axios.get(`/order?${params.toString()}`);
    const orders = data.data || [];

    if (orders.length === 0) {
      Swal.fire('Info', 'Tidak ada data untuk diekspor', 'info');
      return;
    }

    // Format data untuk Excel
    const excelData = orders.map((order, index) => ({
      'No': index + 1,
      'Nama Pelanggan': order.pelanggan?.name || '-',
      'Nama Laundry': order.mitra?.nama_laundry || '-',
      'Jenis Layanan': order.jenis_layanan?.nama_layanan || '-',
      'Kode Order': order.kode_order,
      'Berat Estimasi': order.berat_estimasi,
      'Berat Aktual': order.berat_aktual,
      'Harga': order.harga_final,
      'Catatan': order.catatan || '-',
      'Waktu Antar': order.waktu_pelanggan_antar 
        ? new Date(order.waktu_pelanggan_antar).toLocaleString('id-ID')
        : '-',
      'Status': order.status?.replaceAll('_', ' ') || '-',
      'Status Pembayaran': order.transaksi?.status_pembayaran?.replaceAll('_', ' ') || 'belum_dibayar'
    }));

    // Buat workbook
    const ws = XLSX.utils.json_to_sheet(excelData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Laporan Order');

    // Generate filename
    let filename = 'Laporan_Order_';
    if (filterType.value === 'daily') {
      filename += `Harian_${selectedDate.value}`;
    } else if (filterType.value === 'weekly') {
      const [start, end] = selectedWeek.value.split('|');
      filename += `Mingguan_${start}_${end}`;
    } else {
      filename += `Bulanan_${selectedMonth.value}`;
    }
    filename += '.xlsx';

    // Download
    XLSX.writeFile(wb, filename);
    
    Swal.fire('Berhasil', 'Laporan berhasil diekspor ke Excel', 'success');
  } catch (error) {
    console.error('Error exporting to Excel:', error);
    Swal.fire('Error', 'Gagal mengekspor ke Excel', 'error');
  }
};

const exportToPDF = async () => {
  try {
    const params = new URLSearchParams();
    
    // Filter berdasarkan tipe
    if (filterType.value === 'daily' && selectedDate.value) {
      params.append('filter_date', selectedDate.value);
    } else if (filterType.value === 'weekly' && selectedWeek.value) {
      const [start, end] = selectedWeek.value.split('|');
      params.append('filter_start_date', start);
      params.append('filter_end_date', end);
    } else if (filterType.value === 'monthly' && selectedMonth.value) {
      params.append('filter_month', selectedMonth.value);
    }

    const response = await axios.get(`/order/export-pdf?${params.toString()}`, {
      responseType: 'blob'
    });

    // Generate filename
    let filename = 'Laporan_Order_';
    if (filterType.value === 'daily') {
      filename += `Harian_${selectedDate.value}`;
    } else if (filterType.value === 'weekly') {
      const [start, end] = selectedWeek.value.split('|');
      filename += `Mingguan_${start}_${end}`;
    } else {
      filename += `Bulanan_${selectedMonth.value}`;
    }
    filename += '.pdf';

    saveAs(response.data, filename);
    Swal.fire('Berhasil', 'Laporan berhasil diekspor ke PDF', 'success');
  } catch (error) {
    console.error('Error exporting to PDF:', error);
    Swal.fire('Error', 'Gagal mengekspor ke PDF', 'error');
  }
};

const redirectToPayment = async (orderId: number, snapToken?: string) => {
  try {
    let token = snapToken;

    if (!token) {
      const { data } = await axios.post(`/payment/token/${orderId}`);
      token = data.snap_token;
    }

    if (!token) {
      Swal.fire("Error", "Snap token tidak tersedia", "error");
      return;
    }

    window.snap.pay(token, {
      onSuccess: () => {
        Swal.fire("Berhasil", "Pembayaran berhasil. Menunggu konfirmasi sistem.", "success");
        refresh();
      },
      onPending: () => {
        Swal.fire("Pending", "Menunggu pembayaran diselesaikan", "info");
        refresh();
      },
      onError: () => {
        Swal.fire("Gagal", "Pembayaran gagal", "error");
      },
      onClose: () => {
        Swal.fire("Ditutup", "Popup pembayaran ditutup", "warning");
      },
    });
  } catch (err) {
    console.error(err);
    Swal.fire("Error", "Gagal mengambil snap token", "error");
  }
};

const updateStatus = async (row: Row<Order>) => {
  const currentStatus = row.original.status;
  const currentIndex = statusSteps.indexOf(currentStatus);
  if (currentIndex === -1 || currentStatus === "selesai") return;

  const nextStatus = statusSteps[currentIndex + 1];

  const confirmed = await Swal.fire({
    icon: statusIcons[nextStatus],
    title: `Ubah Status ke "${statusLabels[nextStatus]}"?`,
    showCancelButton: true
  }).then(r => r.isConfirmed);

  if (!confirmed) return;

  await axios.put(`/order/${row.original.id}/status`, {
    status: nextStatus
  });

  Swal.fire("Berhasil", "Status diperbarui", "success");
  await refresh();
};

const downloadReceipt = async (noKode: string) => {
  const response = await axios.get(`/download-struk/${noKode}`, {
    responseType: 'blob'
  });
  saveAs(response.data, `struk-${noKode}.pdf`);
};

const columns = [
  column.accessor("no", { header: "No" }),
  column.accessor(row => row.pelanggan?.name ?? "-", { header: "Nama Pelanggan" }),
  column.accessor("mitra.nama_laundry", { header: "Nama Laundry" }),
  column.accessor("jenis_layanan.nama_layanan", { header: "Jenis Layanan" }),
  column.accessor("kode_order", { header: "Kode Order" }),
  column.accessor("berat_estimasi", { header: "Berat Estimasi" }),
  column.accessor("berat_aktual", { header: "Berat Aktual" }),
  column.accessor("harga_final", { header: "Harga" }),
  column.accessor("catatan", { header: "Catatan" }),
  column.accessor("alasan_penolakan", {
    header: "Alasan Penolakan",
    cell: ({ getValue, row }) => {
      const alasan = getValue();
      if (!alasan && row.original.status !== "ditolak") {
        return h("span", { style: "color:#888; font-style:italic;" }, "Tidak ada alasan penolakan");
      }
      return alasan || "-";
    },
  }),
  column.accessor("waktu_pelanggan_antar", {
    header: "Waktu Antar",
    cell: ({ getValue }) => {
      const val = getValue();
      return val ? new Date(val).toLocaleString("id-ID") : "-";
    }
  }),
  column.accessor("waktu_diambil", { header: "Waktu Diambil" }),
  column.accessor("status", {
    header: "Status",
    cell: ({ row }) => {
      const val = row.original.status;
      const color = statusColors[val] || "bg-secondary";
      return h("button", {
        class: `badge ${color} text-white border-0 cursor-pointer`,
        onClick: () => updateStatus(row)
      }, val.replaceAll("_", " "));
    }
  }),
  column.accessor("id", {
    header: "Aksi",
    cell: (cell) => {
      const row = cell.row.original;
      const actions: any[] = [];

      if (row.status === "menunggu_konfirmasi_mitra") {
        actions.push(
          h("button", {
            class: "btn btn-sm btn-success",
            onClick: async () => {
              const ok = await Swal.fire({
                icon: "question",
                title: "Terima order ini?",
                showCancelButton: true,
              }).then((r) => r.isConfirmed);
              if (!ok) return;
              await axios.post(`/order/${row.id}/konfirmasi`, { status: "ditunggu_mitra" });
              Swal.fire("Berhasil", "Order diterima!", "success");
              await refresh();
            },
          }, "Terima")
        );

        actions.push(
          h("button", {
            class: "btn btn-sm btn-danger",
            onClick: async () => {
              const { value: alasan } = await Swal.fire({
                title: "Alasan penolakan",
                input: "text",
                inputPlaceholder: "Tulis alasan...",
                showCancelButton: true,
              });
              if (!alasan) return;
              await axios.post(`/order/${row.id}/tolak`, {
                status: "ditolak",
                alasan_penolakan: alasan,
              });
              Swal.fire("Ditolak", "Order berhasil ditolak", "success");
              await refresh();
            },
          }, "Tolak")
        );
      }

      if (row.status?.trim() === "diterima") {
        actions.push(
          h("button", {
            class: "btn btn-sm btn-icon btn-info",
            onClick: () => {
              selected.value = cell.getValue();
              openForm.value = true;
            },
          }, h("i", { class: "la la-pencil fs-2" }))
        );
      }

      actions.push(
        h("button", {
          class: "btn btn-sm btn-icon btn-danger",
          onClick: () => deleteOrder(`order/${cell.getValue()}`),
        }, h("i", { class: "la la-trash fs-2" }))
      );

      return h("div", { class: "d-flex gap-2 flex-nowrap align-items-center" }, actions);
    },
  }),
  column.display({
    id: "struk",
    header: "Struk",
    cell: ({ row }) => {
      const noKode = row.original.kode_order;
      return h("div", { class: "d-flex gap-2" }, [
        h("button", {
          class: "btn btn-sm btn-secondary",
          onClick: () => downloadReceipt(noKode),
          title: "Download PDF"
        }, [h("i", { class: "la la-download me-1" }), "Download"])
      ]);
    },
  }),
  column.display({
    id: "paymentAction",
    header: "Pembayaran",
    cell: ({ row }) => {
      const transaksi = row.original.transaksi;
      const status = transaksi?.status_pembayaran;

      if (status === "menunggu_pembayaran" && transaksi?.snap_token) {
        return h("button", {
          class: "btn btn-sm btn-warning",
          onClick: () => window.snap.pay(transaksi.snap_token),
        }, [h("i", { class: "bi bi-arrow-repeat me-1" }), "Lanjut Bayar"]);
      }

      if (status === "dibayar") {
        return h("span", { class: "badge bg-success" }, "Lunas");
      }

      return h("button", {
        class: "btn btn-sm btn-success",
        onClick: () => redirectToPayment(row.original.id),
      }, [h("i", { class: "bi bi-credit-card me-1" }), "Bayar"]);
    },
  }),
  column.accessor(row => row.transaksi?.status_pembayaran ?? 'belum_dibayar', {
    header: "Status Bayar",
    cell: ({ getValue }) => {
      const status = getValue();
      const map: Record<string, string> = {
        dibayar: "badge bg-success",
        menunggu_pembayaran: "badge bg-warning text-dark",
        kadaluarsa: "badge bg-secondary",
        dibatalkan: "badge bg-danger",
        dikembalikan: "badge bg-info text-dark",
        belum_dibayar: "badge bg-secondary",
      };
      return h("span", { class: map[status] }, status.replaceAll("_", " "));
    }
  }),
];

onMounted(() => {
  if (!window.snap) {
    const script = document.createElement("script");
    script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
    script.setAttribute("data-client-key", import.meta.env.VITE_MIDTRANS_CLIENT_KEY);
    script.async = true;
    document.body.appendChild(script);
  }
});

onMounted(async () => {
  await nextTick();
  refresh();
});
</script>

<template>
  <Form v-if="openForm" :selected="selected" @close="openForm = false" @refresh="refresh" />
  
  <div class="card">
    <div class="card-header">
      <h2 class="mb-0">Orderan</h2>
    </div>

    <!-- Filter Laporan -->
    <div class="card-body border-bottom">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label fw-bold">Tipe Filter</label>
          <select v-model="filterType" class="form-select" @change="refresh">
            <option value="daily">Harian</option>
            <option value="weekly">Mingguan</option>
            <option value="monthly">Bulanan</option>
          </select>
        </div>

        <!-- Filter Harian -->
        <div v-if="filterType === 'daily'" class="col-md-3">
          <label class="form-label fw-bold">Pilih Tanggal</label>
          <input 
            v-model="selectedDate" 
            type="date" 
            class="form-control"
            @change="refresh"
          />
        </div>

        <!-- Filter Mingguan -->
        <div v-if="filterType === 'weekly'" class="col-md-3">
          <label class="form-label fw-bold">Pilih Bulan</label>
          <input 
            v-model="selectedMonth" 
            type="month" 
            class="form-control"
          />
        </div>
        <div v-if="filterType === 'weekly'" class="col-md-3">
          <label class="form-label fw-bold">Pilih Minggu</label>
          <select v-model="selectedWeek" class="form-select" @change="refresh">
            <option 
              v-for="week in weekOptions" 
              :key="week.start" 
              :value="`${week.start}|${week.end}`"
            >
              {{ week.label }}
            </option>
          </select>
        </div>

        <!-- Filter Bulanan -->
        <div v-if="filterType === 'monthly'" class="col-md-3">
          <label class="form-label fw-bold">Pilih Bulan</label>
          <input 
            v-model="selectedMonth" 
            type="month" 
            class="form-control"
            @change="refresh"
          />
        </div>

        <!-- Tombol Export -->
        <div class="col-md-3">
          <div class="d-flex gap-2">
            <button 
              @click="exportToExcel" 
              class="btn btn-success"
              title="Export ke Excel"
            >
              <i class="la la-file-excel fs-3"></i> Excel
            </button>
            <button 
              @click="exportToPDF" 
              class="btn btn-danger"
              title="Export ke PDF"
            >
              <i class="la la-file-pdf fs-3"></i> PDF
            </button>
          </div>
        </div>
      </div>
    </div>

    <paginate ref="paginateRef" :url="url" :columns="columns" />
  </div>
</template>

<style scoped>
.btn {
  padding: 0.5rem 1.5rem;
}
.form-label {
  margin-bottom: 0.5rem;
}
</style>