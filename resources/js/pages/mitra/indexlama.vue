<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { mitra } from "@/types";
import axios from "axios";
import Swal from "sweetalert2";  

const column = createColumnHelper<mitra>();

// refs
const paginateRef = ref<any>(null);
const trashRef    = ref<any>(null);
const selected    = ref<string>("");
const openForm    = ref<boolean>(false);

// hapus data aktif
const { delete: deleteMitra } = useDelete({
  onSuccess: () => refresh(),
});

// hapus permanen (sampah)
const { delete: forceDeleteMitra } = useDelete({
  onSuccess: () => trashRef.value.refetch(),
});

// restore data
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
      axios.post(url).then(() => {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Data berhasil dipulihkan!",
          timer: 1500,
          showConfirmButton: false,
        });
        refresh();
      }).catch(() => {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: "Terjadi kesalahan saat memulihkan data.",
        });
      });
    }
  });
};

// kolom tabel utama
const columns = [
  column.accessor("no", { header: "No" }),
  column.accessor("nama_mitra", { header: "Nama Mitra" }),
  column.accessor("pemilik", { header: "Pemilik" }),
  column.accessor("email", { header: "Email" }),
  column.accessor("no_hp", { header: "No Hp" }),
  column.accessor("alamat", { header: "alamat" }),
  column.accessor("password", { header: "Password" }),
  column.accessor("status", { header: "Status" }),
  column.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2" }, [
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-info",
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
            class: "btn btn-sm btn-icon btn-danger",
            onClick: () => {
              deleteMitra(`/mitra/${cell.getValue()}`);
              trashRef.value.refetch();
            },
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      ]),
  }),
];

// kolom tabel sampah
const trashColumns = [
  column.accessor("nama_mitra", { header: "Nama Mitra" }),
  column.accessor("pemilik", { header: "Pemilik" }),
  column.accessor("email", { header: "Email" }),
  column.accessor("no_hp", { header: "No Hp" }),
  column.accessor("alamat", { header: "alamat" }),
  column.accessor("password", { header: "Password" }),
  column.accessor("status", { header: "Status" }),
  column.accessor("created_at", { header: "Created At" }),
  column.accessor("deleted_at", { header: "Deleted At" }),
  column.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2" }, [
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-primary",
            title: "Restore",
            onClick: () =>
              restoreMitra(`/mitra/${cell.getValue()}/restore`),
          },
          h("i", { class: "la la-undo fs-2" })
        ),
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-danger",
            title: "Hapus Permanen",
            onClick: () =>
              forceDeleteMitra(`/mitra/${cell.getValue()}/force-delete`),
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      ]),
  }),
];

const refresh = () => {
  paginateRef.value.refetch();
  trashRef.value.refetch();
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

  <!-- 🔹 Data Aktif -->
  <div class="card mb-10">
    <div class="card-header align-items-center">
      <h2 class="mb-0">List Mitra</h2>
      <button
        type="button"
        class="btn btn-sm btn-primary ms-auto"
        v-if="!openForm"
        @click="openForm = true"
      >
        Tambah <i class="la la-plus"></i>
      </button>
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

  <!-- 🔹 Data Sampah -->
  <div class="card border-danger">
    <div class="card-header align-items-center">
      <h2 class="mb-0 text-danger">
       Mitra (Trash)
        <small>(data yang telah dihapus)</small>
      </h2>
    </div>
    <div class="card-body">
      <paginate
        ref="trashRef"
        id="table-mitra_trash"
        url="/mitra/trash"
        :columns="trashColumns"
      />
    </div>
  </div>
</template>
