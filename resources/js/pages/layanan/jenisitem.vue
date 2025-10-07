<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./formitem.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { jenisitem } from "@/types";
import axios from "axios";
import Swal from "sweetalert2";  

const column = createColumnHelper<jenisitem>();

// ref utama
const paginateRef = ref<any>(null);
const trashRef    = ref<any>(null);     // ✅ tabel sampah
const selected    = ref<string>("");
const openForm    = ref<boolean>(false);

// hapus biasa
const { delete: deleteJenisItem } = useDelete({
    onSuccess: () => refresh(),
});

// hapus permanen (sampah)
const { delete: forceDeleteJenis } = useDelete({
    onSuccess: () => trashRef.value.refetch(),
});

// restore data
// const { post: restoreJenis } = useDelete({
//     onSuccess: () => trashRef.value.refetch(),
// });

// const restoreJenis = (url: string) => {
//     axios.post(url).then(() => {
//         trashRef.value.refetch()
//         refresh()
//     });
// }

const restoreJenis = (url: string) => {
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
                refresh();  // otomatis refresh kedua tabel
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
    column.accessor("nama", { header: "Jenis Item" }),
    column.accessor("deskripsi", { header: "Deskripsi" }),
    column.accessor("id", {
        header: "Aksi",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
                h("button", {
                    class: "btn btn-sm btn-icon btn-info",
                    onClick: () => {
                        selected.value = String(cell.getValue());
                        openForm.value = true;
                    },
                }, h("i", { class: "la la-pencil fs-2" })),
                h("button", {
                    class: "btn btn-sm btn-icon btn-danger",
                    onClick: () =>{
                        
                        deleteJenisItem(`/jenis_item/${cell.getValue()}`)
                        trashRef.value.refetch()
                    }
                }, h("i", { class: "la la-trash fs-2" })),
            ]),
    }),
];

// ✅ kolom tabel sampah
const trashColumns = [
    // column.accessor("no",       { header: "No" }),
    column.accessor("nama",     { header: "Jenis Item" }),
    column.accessor("created_at",{ header: "Created At" }),
    column.accessor("deleted_at",{ header: "Deleted At" }),
    column.accessor("id", {
        header: "Aksi",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
                h("button", {
                    class: "btn btn-sm btn-icon btn-primary",
                    title: "Restore",
                    onClick: () =>
                        restoreJenis(`/jenis_item/${cell.getValue()}/restore`),
                }, h("i", { class: "la la-undo fs-2" })),
                h("button", {
                    class: "btn btn-sm btn-icon btn-danger",
                    title: "Hapus Permanen",
                    onClick: () =>
                        forceDeleteJenis(`/jenis_item/${cell.getValue()}/force-delete`),
                }, h("i", { class: "la la-trash fs-2" })),
            ]),
    }),
];

const refresh = () => {
    paginateRef.value.refetch();
    trashRef.value.refetch();
}

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

    <!-- 🔹 Data aktif -->
    <div class="card mb-10">
        <div class="card-header align-items-center">
            <h2 class="mb-0">List Jenis Item</h2>
            <button type="button" class="btn btn-sm btn-primary ms-auto"
                v-if="!openForm"
                @click="openForm = true">
                Tambah <i class="la la-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-jenis_item"
                url="/jenis_item"
                :columns="columns"
            />
        </div>
    </div>

    <!-- 🔹 Data Sampah -->
    <div class="card border-danger">
        <div class="card-header align-items-center">
            <h2 class="mb-0 text-danger">
                Jenis Pakaian Trash <small>(data yang telah dihapus)</small>
            </h2>
        </div>
        <div class="card-body">
            <paginate
                ref="trashRef"
                id="table-jenis_item_trash"
                url="/jenis_item/trash"
                :columns="trashColumns"
            />
        </div>
    </div>
</template>
