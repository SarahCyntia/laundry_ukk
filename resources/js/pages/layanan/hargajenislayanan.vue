<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./formharga.vue";           // ⬅️ ganti form sesuai harga
import { createColumnHelper } from "@tanstack/vue-table";
import type { hargajenislayanan } from "@/types"; // ⬅️ type baru
import axios from "axios";
import Swal from "sweetalert2";

const column = createColumnHelper<hargajenislayanan>();

// ref utama
const paginateRef = ref<any>(null);
const trashRef    = ref<any>(null);     // ✅ tabel sampah
const selected    = ref<string>("");
const openForm    = ref<boolean>(false);

// hapus biasa (soft delete)
const { delete: deleteHarga } = useDelete({
    onSuccess: () => refresh(),
});

// hapus permanen
const { delete: forceDeleteHarga } = useDelete({
    onSuccess: () => trashRef.value.refetch(),
});

// restore data
const restoreHarga = (url: string) => {
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
    column.accessor("harga", { header: "Harga" }),
    column.accessor("jenis_satuan", { header: "Jenis Satuan" }),
    // relasi ke jenis layanan
    column.accessor("jenis_layanan.nama_layanan", {
        header: "Jenis Layanan",
    }),
    // relasi ke jenis pakaian/item
    column.accessor("jenis_item.nama", {
        header: "Jenis Item",
    }),
    column.accessor("id", {
        header: "Aksi",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
                // edit
                h("button", {
                    class: "btn btn-sm btn-icon btn-info",
                    onClick: () => {
                        selected.value = String(cell.getValue());
                        openForm.value = true;
                    },
                }, h("i", { class: "la la-pencil fs-2" })),
                // hapus (soft delete)
                h("button", {
                    class: "btn btn-sm btn-icon btn-danger",
                    onClick: () => {
                        Swal.fire({
                            title: "Pindahkan ke Sampah?",
                            text: "Data akan dipindahkan ke sampah dan masih bisa dipulihkan.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Ya, pindahkan",
                            cancelButtonText: "Batal",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                deleteHarga(`/harga_jenis_layanan/${cell.getValue()}`)
                                    .then(() => trashRef.value.refetch());
                            }
                        });
                    },
                }, h("i", { class: "la la-trash fs-2" })),
            ]),
    }),
];

// ✅ kolom tabel sampah
const trashColumns = [
    column.accessor("harga", { header: "Harga" }),
    column.accessor("jenis_satuan", { header: "Jenis Satuan" }),
    column.accessor("jenis_layanan.nama_layanan", { header: "Jenis Layanan" }),
    column.accessor("jenis_item.nama", { header: "Jenis Item" }),
    column.accessor("created_at", { header: "Created At" }),
    column.accessor("deleted_at", { header: "Deleted At" }),
    column.accessor("id", {
        header: "Aksi",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
                h("button", {
                    class: "btn btn-sm btn-icon btn-primary",
                    title: "Restore",
                    onClick: () =>
                        restoreHarga(`/harga_jenis_layanan/${cell.getValue()}/restore`),
                }, h("i", { class: "la la-undo fs-2" })),
                h("button", {
                    class: "btn btn-sm btn-icon btn-danger",
                    title: "Hapus Permanen",
                    onClick: () =>
                        forceDeleteHarga(`/harga_jenis_layanan/${cell.getValue()}/force-delete`),
                }, h("i", { class: "la la-trash fs-2" })),
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

    <!-- 🔹 Data aktif -->
    <div class="card mb-10">
        <div class="card-header align-items-center">
            <h2 class="mb-0">List Harga Jenis Layanan</h2>
            <button type="button" class="btn btn-sm btn-primary ms-auto"
                v-if="!openForm"
                @click="openForm = true">
                Tambah <i class="la la-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-harga_jenis_layanan"
                url="/harga_jenis_layanan"
                :columns="columns"
            />
        </div>
    </div>

    <!-- 🔹 Data Sampah -->
    <div class="card border-danger">
        <div class="card-header align-items-center">
            <h2 class="mb-0 text-danger">
                 Harga Jenis Layanan Trash <small>(data yang telah dihapus)</small>
            </h2>
        </div>
        <div class="card-body">
            <paginate
                ref="trashRef"
                id="table-harga_jenis_layanan_trash"
                url="/harga_jenis_layanan/trash"
                :columns="trashColumns"
            />
        </div>
    </div>
</template>

