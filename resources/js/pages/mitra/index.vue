<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Mitra } from "@/types";
import axios from "axios";
import Swal from "sweetalert2";

const column = createColumnHelper<Mitra>();
const paginateRef = ref<any>(null);
const trashRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);
const mitraData = ref<Mitra | null>(null);

// Hapus data aktif
const { delete: deleteMitra } = useDelete({
  onSuccess: () => refresh(),
});

// Restore data dari sampah
const restoreMitra = (url: string) => {
  Swal.fire({
    title: "Pulihkan data?",
    text: "Data akan dikembalikan ke daftar utama.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, pulihkan",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      axios.post(url)
        .then(() => {
          Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: "Data berhasil dipulihkan!",
            timer: 1500,
            showConfirmButton: false,
          });
          refresh();
        })
        .catch(() => {
          Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Terjadi kesalahan saat memulihkan data.",
          });
        });
    }
  });
};

// 🔹 Update status mitra (aktif / ditolak)
const updateStatus = (id: number, status: string) => {
  const label =
    status === "aktif" ? "Terima" : status === "ditolak" ? "Tolak" : "Ubah";

  Swal.fire({
    title: `${label} mitra ini?`,
    text: `Status akan diubah menjadi ${status}.`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: `Ya, ${label}`,
    cancelButtonText: "Batal",
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await axios.put(`/mitra/${id}/status`, { status }); // 👈 hanya update di tabel mitra
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: `Mitra berhasil di${status === "aktif" ? "terima" : "tolak"}.`,
          timer: 1500,
          showConfirmButton: false,
        });
        refresh();
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text: "Terjadi kesalahan saat mengubah status.",
        });
      }
    }
  });
};

// 🔹 Kolom tabel utama
const columns = [
  column.accessor("no", { header: "No" }),
  column.accessor("nama_laundry", { header: "Nama Laundry" }),
  column.accessor("user.name", { header: "Nama Pemilik" }),
  column.accessor("user.email", { header: "Email" }),
  column.accessor("user.phone", { header: "No HP" }),
  column.accessor("alamat_laundry", { header: "Alamat Laundry" }),

  column.accessor("foto_ktp", {
    header: "Foto KTP",
    cell: (cell) => {
      const val = cell.getValue();
      return h("img", {
        src: val ? `/storage/${val}` : "/img/default.png",
        alt: "Foto KTP",
        class: "img-thumbnail",
        style: "width: 50px; height: 50px;",
      });
    },
  }),

  column.accessor("status", {
    header: "Status",
    cell: (cell) => {
      const status = cell.getValue();
      const id = cell.row.original.id;

      if (status === "pending") {
        return h("div", { class: "d-flex gap-2" }, [
          h(
            "button",
            {
              class: "btn btn-sm btn-success",
              onClick: () => updateStatus(id, "aktif"),
            },
            "Terima"
          ),
          h(
            "button",
            {
              class: "btn btn-sm btn-danger",
              onClick: () => updateStatus(id, "ditolak"),
            },
            "Tolak"
          ),
        ]);
      }

      const color =
        status === "aktif"
          ? "badge-success"
          : status === "ditolak"
          ? "badge-danger"
          : "badge-secondary";

      return h("span", { class: `badge ${color}` }, status.toUpperCase());
    },
  }),

  column.accessor("status_toko", {
    header: "Status Toko",
    cell: ({ getValue }) => {
      const status = getValue() as string;
      const color =
        status === "buka"
          ? "bg-success text-white"
          : "bg-danger text-white";
      return h("span", { class: `px-2 py-1 rounded ${color}` }, status.toUpperCase());
    },
  }),

  column.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2" }, [
        h(
          "button",
          {
            class: "btn btn-sm btn-info",
            onClick: () => {
              selected.value = String(cell.getValue());
              openForm.value = true;
            },
          },
          h("i", { class: "la la-pencil fs-2" })
        ),
        h(
          "button",
          {
            class: "btn btn-sm btn-danger",
            onClick: () => deleteMitra(`/mitra/${cell.getValue()}`),
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      ]),
  }),
];

const refresh = () => {
  paginateRef.value.refetch();
  trashRef.value?.refetch?.();
};

watch(openForm, (val) => {
  if (!val) selected.value = "";
  window.scrollTo(0, 0);
});
</script>

<template>
  <Form
    :selected="selected"
    v-if="openForm"
    @close="openForm = false"
    @refresh="refresh"
  />

  <div class="card mb-10">
    <div class="card-header align-items-center">
      <h2 class="mb-0">List Mitra</h2>
    </div>
    <div class="card-body">
      <paginate
        ref="paginateRef"
        id="table-mitra"
        url="/mitra"
        :columns="columns"
      />
    </div>
  </div>
</template>
