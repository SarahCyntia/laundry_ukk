<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import { createColumnHelper, type Row } from "@tanstack/vue-table";
import type { Order } from "@/types";
import { useDelete } from "@/libs/hooks";
import { h } from "vue";
import Swal from "sweetalert2";
import axios from "@/libs/axios";

const column = createColumnHelper();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

// Data user dan mitra
const currentUser = ref<any>(null);
const mitraList = ref<any[]>([]);
const selectedMitraId = ref<number | null>(null); // Filter mitra yang dipilih

onMounted(async () => {
  // Ambil data user yang sedang login
  const { data: userData } = await axios.get("/pelanggan");
  currentUser.value = userData;

  // Jika admin, ambil daftar semua mitra
  if (userData.role === 'admin') {
    const { data: mitraData } = await axios.get("/mitra");
    mitraList.value = mitraData;
  }
});

const refresh = () => paginateRef.value?.refetch();

const { delete: deleteOrder } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
});

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

// URL dengan filter mitra untuk admin
const url = computed(() => {
  if (currentUser.value?.role === 'admin' && selectedMitraId.value) {
    return `/data-order?mitra_id=${selectedMitraId.value}`;
  }
  return `/data-order`;
});

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

// Reset filter
const resetFilter = () => {
  selectedMitraId.value = null;
  refresh();
};

// Watch perubahan filter
watch(selectedMitraId, () => {
  refresh();
});

const columns = [
 column.display({
  id: "no",
  header: "No",
  cell: ({ row }) => row.index + 1,
}),

  
  column.accessor(row => row.pelanggan?.user?.name ?? "-", {
    header: "Nama Pelanggan",
  }),

  column.accessor("mitra.nama_laundry", { 
    header: "Nama Laundry",
    cell: ({ getValue }) => {
      const nama = getValue();
      return h(
        "span",
        { 
          class: "badge bg-primary",
          style: "font-size: 0.9rem; padding: 0.5rem 0.75rem;"
        },
        nama
      );
    }
  }),

  column.accessor("jenis_layanan.nama_layanan", { header: "Jenis Layanan" }),
  column.accessor("kode_order", { header: "Kode Order" }),
  column.accessor("berat_estimasi", { header: "Berat Estimasi (kg)" }),
  column.accessor("berat_aktual", { header: "Berat Aktual (kg)" }),
  
  column.accessor("harga_final", { 
    header: "Harga",
    cell: ({ getValue }) => {
      const value = getValue();
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(value || 0);
    }
  }),
  


  // column.accessor("status", {
  //   header: "Status",
  //   cell: ({ row }) => {
  //     const val = row.original.status;
  //     const color = statusColors[val] || "bg-secondary";
      
  //     const isMitra = currentUser.value?.role === 'mitra';

  //     return h(
  //       "span",
  //       {
  //         class: `badge ${color} text-white`,
  //         style: "font-size: 0.85rem; padding: 0.5rem 0.75rem;"
  //       },
  //       statusLabels[val] || val.replaceAll("_", " ")
  //     );
  //   }
  // }),

  // column.accessor("id", {
  //   header: "Aksi",
  //   cell: (cell) => {
  //     const row = cell.row.original;
  //     const actions: any[] = [];

  //     // Detail button
  //     actions.push(
  //       h(
  //         "button",
  //         {
  //           class: "btn btn-sm btn-icon btn-info",
  //           onClick: () => {
  //             Swal.fire({
  //               title: 'Detail Order',
  //               html: `
  //                 <div class="text-start">
  //                   <p><strong>Kode Order:</strong> ${row.kode_order}</p>
  //                   <p><strong>Laundry:</strong> ${row.mitra?.nama_laundry}</p>
  //                   <p><strong>Pelanggan:</strong> ${row.pelanggan?.nama}</p>
  //                   <p><strong>Status:</strong> ${statusLabels[row.status]}</p>
  //                   <p><strong>Total:</strong> ${new Intl.NumberFormat('id-ID', {
  //                     style: 'currency',
  //                     currency: 'IDR'
  //                   }).format(row.harga_final)}</p>
  //                 </div>
  //               `,
  //               confirmButtonText: 'Tutup'
  //             });
  //           },
  //         },
  //         h("i", { class: "la la-eye fs-2" })
  //       )
  //     );

  //     // Delete button (hanya admin)
  //     if (currentUser.value?.role === 'admin') {
  //       actions.push(
  //         h(
  //           "button",
  //           {
  //             class: "btn btn-sm btn-icon btn-danger",
  //             onClick: () => deleteOrder(`order/${cell.getValue()}`),
  //           },
  //           h("i", { class: "la la-trash fs-2" })
  //         )
  //       );
  //     }

  //     return h(
  //       "div",
  //       { class: "d-flex gap-2 flex-nowrap align-items-center" },
  //       actions
  //     );
  //   },
  // }),
];

watch(paginateRef, (val) => {
  if (val) {
    val.refetch();
  }
});

</script>

<template>
  <Form v-if="openForm" :selected="selected" @close="openForm = false" @refresh="refresh" />
  
  <div class="card">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
          {{ currentUser?.role === 'admin' ? 'Semua Order' : 
             currentUser?.role === 'mitra' ? 'Order Laundry Saya' : 
             'Data Order ' }}
        </h2>
        
        <span v-if="currentUser?.role === 'mitra'" class="badge bg-primary fs-5">
          {{ currentUser?.mitra?.nama_laundry }}
        </span>
      </div>

      <!-- Filter untuk Admin -->
      <div v-if="currentUser?.role === 'admin'" class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Filter berdasarkan Mitra:</label>
          <select 
            v-model="selectedMitraId" 
            class="form-select form-select-lg"
          >
            <option :value="null">📊 Semua Mitra</option>
            <option 
              v-for="mitra in mitraList" 
              :key="mitra.id" 
              :value="mitra.id"
            >
              🏪 {{ mitra.nama_laundry }}
            </option>
          </select>
        </div>
        
        <div class="col-md-6 d-flex align-items-end">
          <button 
            v-if="selectedMitraId" 
            @click="resetFilter" 
            class="btn btn-secondary btn-lg"
          >
            <i class="la la-refresh"></i> Reset Filter
          </button>
        </div>
      </div>

      <!-- Info filter aktif -->
      <div v-if="currentUser?.role === 'admin' && selectedMitraId" class="alert alert-info mt-3">
        <i class="la la-info-circle"></i>
        Menampilkan order dari: 
        <strong>{{ mitraList.find(m => m.id === selectedMitraId)?.nama_laundry }}</strong>
      </div>
    </div>
    
    <paginate ref="paginateRef" :url="url" :columns="columns" />
  </div>
</template>

<style scoped>
.btn {
  padding: 0.5rem 1.5rem;
}

.form-select-lg {
  font-size: 1rem;
  padding: 0.75rem;
}
</style>