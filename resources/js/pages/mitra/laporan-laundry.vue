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
import * as XLSX from 'xlsx';

const mitraId = ref<number | null>(null);
const column = createColumnHelper();
// const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);
const order = ref<Order | null>(null); // Data pelanggan yang terkait dengan user login




const filterType = ref<'daily' | 'weekly' | 'monthly'>('monthly');
const selectedDate = ref<string>(new Date().toISOString().split('T')[0]); // YYYY-MM-DD
const selectedMonth = ref<string>(new Date().toISOString().slice(0, 7)); // YYYY-MM
const selectedWeek = ref<string>('');

  const formatDate = (d: Date) => {
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}


const weekOptions = computed(() => {
  if (!selectedMonth.value) return []

  const [year, month] = selectedMonth.value.split('-').map(Number)
  const weeks: Array<{ label: string; start: string; end: string }> = []

  const firstDay = new Date(year, month - 1, 1)
  const lastDay = new Date(year, month, 0)

  let currentWeek = 1
  let weekStart = new Date(firstDay)

  while (weekStart <= lastDay) {
    const weekEnd = new Date(weekStart)
    weekEnd.setDate(weekStart.getDate() + 6)

    const endDate = weekEnd > lastDay ? lastDay : weekEnd

    weeks.push({
      label: `Minggu ${currentWeek} (${weekStart.getDate()} - ${endDate.getDate()})`,
      start: formatDate(weekStart),
      end: formatDate(endDate)
    })

    weekStart = new Date(endDate)
    weekStart.setDate(endDate.getDate() + 1)
    currentWeek++
  }

  return weeks
})







// AMBIL MITRA_ID DARI PROFILE USER
onMounted(async () => {
  const { data } = await axios.get("/mitra");
  // pastikan API ini mengembalikan user + mitra_id
  mitraId.value = data.mitra_id;
});

const props = defineProps<{ selected: string }>();
const selectedId = ref(null);
const showForm = ref(false);

function openEdit(id) {
  selectedId.value = id;
  showForm.value = true;
}

// const url = "/order-masuk"; // <--- WAJIB ADA

const paginateRef = ref<any>(null);
const refresh = () => paginateRef.value?.refetch();

// const column = createColumnHelper<Order>();

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








const bayar = async (rowData) => {
  if (rowData.isPaying) return;

  rowData.isPaying = true;
  const draft = {
    order_id: rowData.id,
    berat_estimasi: rowData.berat_barang,
    harga_final: rowData.total_harga,
    biaya: rowData.biaya,
    jenis_layanan_id: rowData.selectedService?.id,
  };

  sessionStorage.setItem("draftTransaksi", JSON.stringify(draft));

  try {
    const res = await axios.post(`/api/payment/token/${row.id}`);
    // const res = await axios.post("/payment/create", draft);
    const { snap_token } = res.data;

    if (!snap_token) throw new Error("Token pembayaran tidak ditemukan.");

    window.snap.pay(snap_token, {
      onSuccess: function (result) {
        console.log("✅ Pembayaran berhasil:", result);
        rowData.status = "paid";
      },
      onPending: function (result) {
        console.log("⏳ Menunggu pembayaran:", result);
        rowData.status = "pending";
      },
      onError: function (result) {
        console.error("❌ Pembayaran gagal:", result);
      },
      onClose: function () {
        console.log("❎ Popup ditutup");
      },
    });
  } catch (err) {
    console.error("❌ Terjadi kesalahan:", err);
  } finally {
    rowData.isPaying = false;
  }
};
const getPembayaranBadgeClass = (status: string | undefined) => {
  const map = {
    dibayar: "badge bg-success",
    menunggu_pembayaran: "badge bg-warning text-dark",
    kadaluarsa: "badge bg-secondary",
    dibatalkan: "badge bg-danger",
    dikembalikan: "badge bg-info text-dark",
    belum_dibayar: "badge bg-secondary",
  };

  return map[status?.toLowerCase() ?? ""] || "badge bg-secondary fw-bold";
};

const redirectToPayment = async (orderId: number, snapToken?: string) => {
  try {
    let token = snapToken;

    // 🔥 kalau belum ada token → ambil dari backend
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
        Swal.fire(
          "Berhasil",
          "Pembayaran berhasil. Menunggu konfirmasi sistem.",
          "success"
        );
        refresh(); // tunggu callback
      },

      onPending: () => {
        Swal.fire(
          "Pending",
          "Menunggu pembayaran diselesaikan",
          "info"
        );
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








const url = computed(() => {
  const params = new URLSearchParams()

  // 🔥 TIDAK PAKAI exclude_status
  // semua status otomatis ikut

  // FILTER (samakan dengan backend)
  params.append('filter_type', filterType.value)

  if (filterType.value === 'daily' && selectedDate.value) {
    params.append('date', selectedDate.value)
  }

  if (filterType.value === 'weekly' && selectedWeek.value) {
    const [start, end] = selectedWeek.value.split('|')
    params.append('start_date', start)
    params.append('end_date', end)
  }

  if (filterType.value === 'monthly' && selectedMonth.value) {
    params.append('month', selectedMonth.value)
  }

  return `/laporan-order?${params.toString()}`
})








const exportToExcel = async () => {
  const response = await axios.get(
    `/laporan-order/export-excel?${url.value.split('?')[1]}`,
    { responseType: 'blob' }
  )

  saveAs(response.data, 'laporan-order.xlsx')
}


watch(selectedMonth, () => {
  selectedWeek.value = ''
})
watch(selectedMonth, () => {
  selectedWeek.value = ''
})


const exportToPDF = async () => {
  const params: any = {
    filter_type: filterType.value,
  }

  if (filterType.value === 'daily') {
    params.date = selectedDate.value
  }

  if (filterType.value === 'weekly') {
    const [start, end] = selectedWeek.value.split('|')
    params.start_date = start
    params.end_date   = end
  }

  if (filterType.value === 'monthly') {
    params.month = selectedMonth.value
  }

  const response = await axios.get('/laporan-order/export-pdf', {
    params,
    responseType: 'blob'
  })

  saveAs(response.data, 'laporan-order.pdf')
}




// const exportToPDF = async () => {
//   try {
//     const params = new URLSearchParams();

//     // Filter berdasarkan tipe
//     if (filterType.value === 'daily' && selectedDate.value) {
//       params.append('filter_date', selectedDate.value);
//     } else if (filterType.value === 'weekly' && selectedWeek.value) {
//       const [start, end] = selectedWeek.value.split('|');
//       params.append('filter_start_date', start);
//       params.append('filter_end_date', end);
//     } else if (filterType.value === 'monthly' && selectedMonth.value) {
//       params.append('filter_month', selectedMonth.value);
//     }

//     const response = await axios.get(`/laporan-order/export-pdf?${params.toString()}`, {
//       responseType: 'blob'
//     });


//     // Generate filename
//     let filename = 'Laporan_Order_';
//     if (filterType.value === 'daily') {
//       filename += `Harian_${selectedDate.value}`;
//     } else if (filterType.value === 'weekly') {
//       const [start, end] = selectedWeek.value.split('|');
//       filename += `Mingguan_${start}_${end}`;
//     } else {
//       filename += `Bulanan_${selectedMonth.value}`;
//     }
//     filename += '.pdf';

//     saveAs(response.data, filename);
//     Swal.fire('Berhasil', 'Laporan berhasil diekspor ke PDF', 'success');
//   } catch (error) {
//     console.error('Error exporting to PDF:', error);
//     Swal.fire('Error', 'Gagal mengekspor ke PDF', 'error');
//   }
// };








const handleSearch = () => {
  refresh()
}


// const handleSearch = () => {
//   let params: any = {
//     filter_type: filterType.value,
//   }

//   if (filterType.value === 'daily') {
//     params.date = selectedDate.value
//   }

//   if (filterType.value === 'weekly') {
//     const [start, end] = selectedWeek.value.split('|')
//     params.start_date = start
//     params.end_date = end
//   }

//   if (filterType.value === 'monthly') {
//     params.month = selectedMonth.value
//   }

//   fetchData(params)
// }
// const fetchData = async (params: any) => {
//   const res = await axios.get('/laporan-order', {
//     params
//   })
//   data.value = res.data
// }
















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

const noKode = ref("");

const columns = [
  column.accessor("no", { header: "No" }),
  // column.accessor("pelanggan.user.name", { header: "Nama Pelanggan" }),
  column.accessor(row => row.pelanggan?.name ?? "-", {
    header: "Nama Pelanggan",
  }),

  column.accessor("mitra.nama_laundry", { header: "Nama Laundry" }),
  column.accessor("jenis_layanan.nama_layanan", { header: "Jenis Layanan" }),

  column.accessor("kode_order", { header: "Kode Order" }),
  column.accessor("berat_estimasi", { header: "Berat Estimasi" }),
  column.accessor("berat_aktual", { header: "Berat Aktual" }),
  column.accessor("harga_final", { header: "Harga" }),
  column.accessor("catatan", { header: "Catatan" }),
  // column.accessor("alasan_penolakan", { header: "Alasan Penolakan" }),
  // column.accessor("alasan_penolakan", {
  //   header: "Alasan Penolakan",
  //   cell: ({ getValue, row }) => {
  //     const alasan = getValue();

  //     // Kalau status diterima / bukan ditolak
  //     if (!alasan && row.original.status !== "ditolak") {
  //       return h(
  //         "span",
  //         { style: "color:#888; font-style:italic;" },
  //         "Tidak ada alasan penolakan"
  //       );
  //     }
  //     return alasan || "-";
  //   },
  // }),

  // column.accessor("waktu_pelanggan_antar", { header: "Waktu Antar" }),
  column.accessor("waktu_pelanggan_antar", {
    header: "Waktu Antar",
    cell: ({ getValue }) => {
      const val = getValue()
      return val
        ? new Date(val).toLocaleString("id-ID")
        : "-"
    }
  }),

  column.accessor("waktu_diambil", { header: "Waktu Diambil" }),


  column.accessor("status", {
    header: "Status",
    cell: ({ row }) => {
      const val = row.original.status;
      const color = statusColors[val] || "bg-secondary";

      return h(
        "button",
        {
          class: `badge ${color} text-white border-0 cursor-pointer`,
          onClick: () => updateStatus(row)
        },
        val.replaceAll("_", " ")
      );
    }
  }),
 

  // column.display({
  //   id: "struk",
  //   header: "Struk",
  //   cell: ({ row }) => {
  //     const noKode = row.original.kode_order;

  //     return h("div", { class: "d-flex gap-2" }, [

  //       h(
  //         "button",
  //         {
  //           class: "btn btn-sm btn-secondary",
  //           onClick: () => downloadReceipt(noKode),
  //           title: "Download PDF"
  //         },
  //         [
  //           h("i", { class: "la la-download me-1" }),
  //           "Download"
  //         ]
  //       )
  //     ]);
  //   },
  // }),


  column.accessor(
    row => row.transaksi?.status_pembayaran ?? 'belum_dibayar',
    {
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

        return h(
          "span",
          { class: map[status] },
          status.replaceAll("_", " ")
        );
      }
    }
  ),

   column.accessor("id", {
    header: "Aksi",
    cell: (cell) => {
      const row = cell.row.original;
      const actions: any[] = [];

      // === Tombol Hapus (selalu ada) ===
      actions.push(
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-danger",
            onClick: () => deleteOrder(`order/${cell.getValue()}`),
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      );

      // 
      return h(
        "div",
        { class: "d-flex gap-2 flex-nowrap align-items-center" },
        actions
      );
    },


  }),

];
onMounted(() => {
  if (!window.snap) {
    const script = document.createElement("script");
    script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
    script.setAttribute(
      "data-client-key",
      import.meta.env.VITE_MIDTRANS_CLIENT_KEY
    );
    script.async = true;
    document.body.appendChild(script);
  }
});
// onMounted(refresh);
onMounted(async () => {
  await nextTick();
  refresh();
});

</script>


<template>


  <Form v-if="openForm" :selected="selected" @close="openForm = false" @refresh="refresh" />
  <div class="card">
    <div class="card-header align-items-center">
      <h2 class="mb-0">Laporan Laundry</h2>
    </div>
    <!-- <paginate ref="paginateRef" :url="`/order-masuk`" :columns="columns" /> -->


    <div class="card-body border-bottom">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label fw-bold">Tipe Filter</label>
          <select v-model="filterType" class="form-select">
            <option value="daily">Harian</option>
            <option value="weekly">Mingguan</option>
            <option value="monthly">Bulanan</option>
          </select>
        </div>

        <!-- Filter Harian -->
        <div v-if="filterType === 'daily'" class="col-md-3">
          <label class="form-label fw-bold">Pilih Tanggal</label>
          <input v-model="selectedDate" type="date" class="form-control" />
        </div>

        <!-- Filter Mingguan -->
        <div v-if="filterType === 'weekly'" class="col-md-3">
          <label class="form-label fw-bold">Pilih Bulan</label>
          <input v-model="selectedMonth" type="month" class="form-control" />
        </div>
        <div v-if="filterType === 'weekly'" class="col-md-3">
          <label class="form-label fw-bold">Pilih Minggu</label>
          <select v-model="selectedWeek" class="form-select" @change="refresh">
            <option v-for="week in weekOptions" :key="week.start" :value="`${week.start}|${week.end}`">
              {{ week.label }}
            </option>
          </select>
        </div>





        <!-- Tombol Cari -->
        <div class="col-md-2">
          <label class="form-label fw-bold invisible">Cari</label>
          <button class="btn btn-primary w-100" @click="handleSearch">
            <i class="la la-search fs-4"></i> Cari
          </button>
        </div>





        <!-- Filter Bulanan -->
        <div v-if="filterType === 'monthly'" class="col-md-3">
          <label class="form-label fw-bold">Pilih Bulan</label>
          <input v-model="selectedMonth" type="month" class="form-control" @change="refresh" />
        </div>

        <!-- Tombol Export -->
        <div class="col-md-3">
          <div class="d-flex gap-2">
            <button @click="exportToExcel" class="btn btn-success" title="Export ke Excel">
              <i class="la la-file-excel fs-3"></i> Excel
            </button>
            <button @click="exportToPDF" class="btn btn-danger" title="Export ke PDF">
              <i class="la la-file-pdf fs-3"></i> PDF
            </button>
          </div>
        </div>




      </div>
    </div>



    <!-- <paginate ref="paginateRef" :url="url" :columns="columns" /> -->
     <paginate
  ref="paginateRef"
  :url="url"
  :columns="columns"
  response-key="data"
/>



    <!-- <paginate ref="paginateRef" id="table-order" :url="url" :columns="columns" /> -->
  </div>
</template>

<style scoped>
.btn {
  margin-top: 1rem;
  padding: 0.5rem 1.5rem;
}

.laporan-card {
  font-size: 14px;
}

/* HEADER ATAS */
.laporan-header {
  background: #f1f4f5;
  padding: 12px 16px;
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  font-weight: 600;
  border-bottom: 1px solid #ddd;
}

/* TABLE */
.laporan-table table {
  width: 100%;
  border-collapse: collapse;
}

.laporan-table th {
  background: #34495e;
  color: white;
  text-align: center;
  font-weight: 600;
}

.laporan-table td,
.laporan-table th {
  padding: 8px;
  border: 1px solid #ccc;
  vertical-align: middle;
}

/* BADGE STATUS */
.badge {
  font-size: 12px;
  padding: 6px 10px;
  border-radius: 6px;
}

/* RINGKASAN */
.ringkasan-box {
  background: #eef7fa;
  margin: 16px;
  border: 1px solid #cfe3ea;
}

.ringkasan-box h5 {
  background: #d9edf3;
  padding: 10px;
  margin: 0;
  font-weight: 700;
}

.ringkasan-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 12px;
  border-top: 1px solid #cfe3ea;
}

/* FOOTER */
.laporan-footer {
  text-align: center;
  font-size: 12px;
  color: #666;
  padding: 10px;
}

</style>