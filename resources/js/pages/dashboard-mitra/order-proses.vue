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

// const url = "/order";

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



const pollingInterval = ref<number | null>(null);

const startAutoRefresh = () => {
  // Jalankan pertama kali
  refresh();

  // Set interval polling setiap 5 detik (5000ms)
  pollingInterval.value = setInterval(() => {
    refresh(); // Memanggil fungsi refresh tabel
  }, 5000);
};






const url = computed(() => {
  const params = new URLSearchParams();
  params.append("status", "diproses");

  return `/order?${params.toString()}`;
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
  column.accessor("berat_aktual", { header: "Berat Aktual" }),
  column.accessor("harga_final", { header: "Harga" }),
  // column.accessor("foto_struk", {
  //   header: "Foto Struk",
  //   cell: ({ getValue }) => {
  //     const foto = getValue();

  //     if (!foto) {
  //       return h("span", { style: "color:#888;" }, "Tidak ada foto");
  //     }

  //     const url = `http://localhost:8000/storage/${foto}`;


  //     console.log("URL FINAL:", url);

  //     return h("img", {
  //       src: url,
  //       style: "width: 80px; height: 80px; object-fit: cover; border-radius: 8px;",
  //     });
  //   }
  // }),


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
    script.setAttribute("data-client-key", "SB-Mid-client-XXXXX"); // ganti sesuai client key kamu
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
      <h2 class="mb-0">Orderan Proses</h2>
    </div>
    <!-- <paginate ref="paginateRef" :url="`/order-masuk`" :columns="columns" /> -->

    <paginate ref="paginateRef" :url="url" :columns="columns" />


    <!-- <paginate ref="paginateRef" id="table-order" :url="url" :columns="columns" /> -->
  </div>
</template>

<style scoped>
.btn {
  margin-top: 1rem;
  padding: 0.5rem 1.5rem;
}
</style>