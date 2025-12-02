<script setup lang="ts">
import { computed, onMounted, ref, watch, nextTick } from "vue";
import { createColumnHelper, type Row } from "@tanstack/vue-table";
import type { Order } from "@/types";
import { useDelete } from "@/libs/hooks";
import { h } from "vue";
import Form from "./form-order-masuk.vue";
import Swal from "sweetalert2";
import axios from "@/libs/axios";

const url = "/order";

const mitraId = ref<number | null>(null);
const column = createColumnHelper();
// const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);
const order = ref<Order | null>(null); // Data pelanggan yang terkait dengan user login

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
  diterima: "question",
  ditolak: "error",
  diproses: "info",
  dicuci: "info",
  dikeringkan: "warning",
  disetrika: "info",
  siap_diambil: "info",
  selesai: "success"
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


const columns = [
  column.accessor("no", { header: "No" }),
  // column.accessor("pelanggan.user.name", { header: "Nama Pelanggan" }),
column.accessor(row => row.pelanggan?.name ?? "-", {
  header: "Nama Pelanggan",
}),

  column.accessor("mitra.nama_laundry", { header: "Nama Laundry" }),
  column.accessor("jenis_layanan.nama_layanan", { header: "Jenis Layanan" }),


//   column.accessor("pelanggan.user.name", {
//   header: "Nama Pelanggan",
//   cell: ({ row }) => row.original.pelanggan?.user?.name ?? "-",
// }),
//   column.accessor("mitra_id", { header: "Nama Laundry" }),
//   // column.accessor("jenis_layanan_id", { header: "Jenis Layanan" }),
//   column.accessor("jenis_layanan.nama_layanan", {
//     header: "Jenis Layanan",
//     cell: ({ row }) => row.original.jenis_layanan?.nama_layanan ?? "-",
//   }),
  column.accessor("kode_order", { header: "Kode Order" }),
  column.accessor("berat_estimasi", { header: "Berat Estimasi" }),
  column.accessor("berat_aktual", { header: "Berat Aktual" }),
  column.accessor("harga_final", { header: "Harga Final" }),
  column.accessor("catatan", { header: "Catatan" }),
  column.accessor("alasan_penolakan", { header: "Alasan Penolakan" }),
  column.accessor("waktu_pelanggan_antar", { header: "Waktu Antar" }),
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
    column.accessor("id", {
  header: "Aksi",
  cell: (cell) => {
  const row = cell.row.original;
  const actions = [];

  // === Jika status masih menunggu konfirmasi ===
  if (row.status === "menunggu_konfirmasi_mitra") {
    actions.push(
      h(
        "button",
        {
          class: "btn btn-sm btn-success",
          onClick: async () => {
            const ok = await Swal.fire({
              icon: "question",
              title: "Terima order ini?",
              showCancelButton: true
            }).then(r => r.isConfirmed);

            if (!ok) return;

            await axios.post(`/order/${row.id}/konfirmasi`, {
              status: "diterima"
            });

            Swal.fire("Berhasil", "Order diterima!", "success");
            await refresh();
          },
        },
        "Terima"
      )
    );

    // Tombol Tolak
    actions.push(
      h(
        "button",
        {
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
              alasan_penolakan: alasan
            });

            Swal.fire("Ditolak", "Order berhasil ditolak", "success");
            await refresh();
          },
        },
        "Tolak"
      )
    );
  }

  // === Jika status ditolak -> hanya bisa hapus ===
  if (row.status !== "ditolak") {
    // Tombol Edit
    actions.push(
      h(
        "button",
        {
          class: "btn btn-sm btn-icon btn-info",
          onClick: () => {
            selected.value = cell.getValue();
            openForm.value = true;
          },
        },
        h("i", { class: "la la-pencil fs-2" })
      )
    );
  }

  // === Tombol hapus tetap ada untuk semua kecuali selesai (opsional) ===
  actions.push(
    h(
      "button",
      {
        class: "btn btn-sm btn-icon btn-danger",
        onClick: () => deleteOrder(`order/${cell.getValue()}`),
      },
      h("i", { class: "la la-trash fs-2" })
    )
  );

  return h("div", { class: "d-flex gap-2" }, actions);
},

  // cell: (cell) => {
  //   const row = cell.row.original;
  //   const actions = [];

  //   // === Tombol TERIMA kalau status menunggu konfirmasi ===
  //   if (row.status === "menunggu_konfirmasi_mitra") {
  //     actions.push(
  //       h(
  //         "button",
  //         {
  //           class: "btn btn-sm btn-success",
  //           onClick: async () => {
  //             const ok = await Swal.fire({
  //               icon: "question",
  //               title: "Terima order ini?",
  //               showCancelButton: true
  //             }).then(r => r.isConfirmed);

  //             if (!ok) return;

  //             await axios.post(`/order/${row.id}/konfirmasi`, {
  //               status: "diterima"
  //             });

  //             Swal.fire("Berhasil", "Order diterima!", "success");
  //             await refresh();
  //           },
  //         },
  //         "Terima"
  //       )
  //     );

  //     // === Tombol TOLAK ===
  //     actions.push(
  //       h(
  //         "button",
  //         {
  //           class: "btn btn-sm btn-danger",
  //           onClick: async () => {
  //             const { value: alasan } = await Swal.fire({
  //               title: "Alasan penolakan",
  //               input: "text",
  //               inputPlaceholder: "Tulis alasan...",
  //               showCancelButton: true,
  //             });

  //             if (!alasan) return;

  //             await axios.post(`/order/${row.id}/tolak`, {
  //               status: "ditolak",
  //               alasan_penolakan: alasan
  //             });

  //             Swal.fire("Ditolak", "Order berhasil ditolak", "success");
  //             await refresh();
  //           },
  //         },
  //         "Tolak"
  //       )
  //     );
  //   }

  //   // === Tombol Edit ===
  //   actions.push(
  //     h(
  //       "button",
  //       {
  //         class: "btn btn-sm btn-icon btn-info",
  //         onClick: () => {
  //           selected.value = cell.getValue();
  //           openForm.value = true;
  //         },
  //       },
  //       h("i", { class: "la la-pencil fs-2" })
  //     )
  //   );

  //   // === Tombol Hapus ===
  //   actions.push(
  //     h(
  //       "button",
  //       {
  //         class: "btn btn-sm btn-icon btn-danger",
  //         onClick: () => deleteOrder(`order/${cell.getValue()}`),
  //       },
  //       h("i", { class: "la la-trash fs-2" })
  //     )
  //   );

  //   return h("div", { class: "d-flex gap-2" }, actions);
  // },
}),

];

onMounted(refresh);
</script>


<template>


  <Form v-if="openForm" :selected="selected" @close="openForm = false" @refresh="refresh" />
  <div class="card">
    <div class="card-header align-items-center">
      <h2 class="mb-0">Orderan</h2>
    </div>
    <paginate ref="paginateRef" :url="`/order-masuk`" :columns="columns" />

    <!-- <paginate ref="paginateRef" id="table-order" :url="url" :columns="columns" /> -->
  </div>
</template>

<style scoped>
.btn {
  margin-top: 1rem;
  padding: 0.5rem 1.5rem;
}
</style>