<script setup lang="ts">
import { ref, onMounted, h } from "vue";
import axios from "@/libs/axios";
import Swal from "sweetalert2";
import { createColumnHelper } from "@tanstack/vue-table";
import { useDelete } from "@/libs/hooks";
import Form from "./form.vue"; // modal form tambah/edit transaksi

// Data & State
const items = ref([]);
const loading = ref(false);
const showForm = ref(false);
const selected = ref<string | null>(null);

// Load data transaksi
async function loadData() {
  loading.value = true;
  try {
    const { data } = await axios.get("/transaksi");
    items.value = data;
  } catch (err: any) {
    console.error(err);
  }
  loading.value = false;
}

// Buka Modal Tambah / Edit
function addData() {
  selected.value = null;
  showForm.value = true;
}
function editData(id: string) {
  selected.value = id;
  showForm.value = true;
}

// Hapus data
async function deleteData(id: string) {
  Swal.fire({
    title: "Hapus Data?",
    text: "Data yang dihapus tidak bisa dikembalikan!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await axios.delete(`/transaksi/${id}`);
        Swal.fire("Berhasil", "Data telah dihapus", "success");
        loadData();
      } catch (err: any) {
        Swal.fire("Gagal", err.response?.data?.message || "Terjadi kesalahan", "error");
      }
    }
  });
}

// TanStack Columns
const columnHelper = createColumnHelper<any>();
const columns = [
  columnHelper.accessor("no", {
    header: "No",
    cell: ({ row }) => row.index + 1,
  }),
  columnHelper.accessor("pelanggan.name", {
    header: "Pelanggan",
    cell: ({ row }) => row.original.pelanggan?.name || "-",
  }),
  columnHelper.accessor("mitra.nama_laundry", {
    header: "Mitra Laundry",
    cell: ({ row }) => row.original.mitra?.nama_laundry || "-",
  }),
  columnHelper.accessor("kurir.name", {
    header: "Kurir",
    cell: ({ row }) => row.original.kurir?.name || "-",
  }),
  columnHelper.accessor("jenis_layanan.nama_layanan", {
    header: "Jenis Layanan",
    cell: ({ row }) => row.original.jenis_layanan?.nama_layanan || "-",
  }),
  columnHelper.accessor("berat", {
    header: "Berat (Kg)",
    cell: ({ row }) => row.original.berat + " Kg",
  }),
  columnHelper.accessor("total_harga", {
    header: "Total Harga",
    cell: ({ row }) =>
      "Rp " + row.original.total_harga.toLocaleString("id-ID"),
  }),
  columnHelper.accessor("status", {
    header: "Status",
    cell: ({ row }) => {
      const status = row.original.status;
      const badgeClass =
        status === "selesai"
          ? "badge badge-success"
          : status === "proses"
          ? "badge badge-warning"
          : status === "diambil"
          ? "badge badge-info"
          : "badge badge-secondary";

      return h("span", { class: badgeClass }, status.toUpperCase());
    },
  }),
  columnHelper.display({
    id: "actions",
    header: "Aksi",
    cell: ({ row }) =>
      h("div", { class: "d-flex gap-2" }, [
        h(
          "button",
          {
            class: "btn btn-sm btn-light-primary",
            onClick: () => editData(row.original.id),
          },
          "Edit"
        ),
        h(
          "button",
          {
            class: "btn btn-sm btn-light-danger",
            onClick: () => deleteData(row.original.id),
          },
          "Hapus"
        ),
      ]),
  }),
];

onMounted(() => {
  loadData();
});
</script>

<template>
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="mb-0">Data Transaksi</h3>

      <button class="btn btn-primary btn-sm ms-auto" @click="addData">
        + Tambah Transaksi
      </button>
    </div>

    <div class="card-body">
      <!-- Loading -->
      <div v-if="loading" class="text-center py-10">
        <div class="spinner-border"></div>
      </div>

      <!-- Tabel -->
      <TanStackTable
        v-else
        :data="items"
        :columns="columns"
        class="table table-row-dashed"
      />
    </div>
  </div>

  <!-- Modal Form -->
  <Form
    v-if="showForm"
    :selected="selected"
    @close="showForm = false"
    @refresh="loadData"
  />
</template>
