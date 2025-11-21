<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { JenisLayanan } from "@/types";

const columnHelper = createColumnHelper<JenisLayanan>();
const paginateRef = ref<any>(null);
const selected = ref<string | number>("");
const openForm = ref<boolean>(false);

// ================== DELETE HANDLER ==================
const { delete: deleteLayanan } = useDelete({
  onSuccess: () => paginateRef.value?.refetch(),
});

// ====================== TABLE COLUMNS ======================
const columns = [
  // Nomor urut otomatis
  columnHelper.display({
    id: "no",
    header: "#",
    cell: (cell) => cell.row.index + 1,
  }),

  // Nama Layanan
  columnHelper.accessor("nama_layanan", {
    header: "Nama Layanan",
  }),

  // Deskripsi
  columnHelper.accessor("deskripsi", {
    header: "Deskripsi",
    cell: (cell) => cell.getValue() || "-",
  }),

  // Satuan (kiloan / satuan)
  columnHelper.accessor("satuan", {
    header: "Satuan",
    cell: (cell) => cell.getValue()?.toUpperCase(),
  }),

  // Harga
  columnHelper.accessor("harga", {
    header: "Harga",
    cell: (cell) => `Rp ${Number(cell.getValue()).toLocaleString()}`,
  }),

  // Aksi
  columnHelper.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2" }, [
        // Tombol Edit
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
        ),

        // Tombol Hapus
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-danger",
            onClick: () => {
              const id = cell.getValue();
              deleteLayanan(`/jenis-layanan/${id}`); // ✔ endpoint benar
            },
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      ]),
  }),
];

// Refresh table
const refresh = () => paginateRef.value?.refetch();

// Auto scroll + reset selected
watch(openForm, (val) => {
  if (!val) selected.value = "";
  window.scrollTo({ top: 0, behavior: "smooth" });
});
</script>

<template>
  <!-- FORM TAMBAH / EDIT -->
  <Form
    v-if="openForm"
    :selected="selected"
    @close="openForm = false"
    @refresh="refresh"
  />

  <!-- TABLE -->
  <div class="card">
    <div class="card-header align-items-center">
      <h2 class="mb-0">Jenis Layanan Laundry</h2>

      <button
        v-if="!openForm"
        type="button"
        class="btn btn-sm btn-primary ms-auto"
        @click="openForm = true"
      >
        Tambah Layanan
        <i class="la la-plus"></i>
      </button>
    </div>

    <div class="card-body">
      <paginate
        ref="paginateRef"
        id="table-jenis-layanan"
        url="/jenis-layanan"    
        :columns="columns"
      />
    </div>
  </div>
</template>
