<script setup lang="ts">
import { h, ref, watch, onMounted } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { wilayah } from "@/types";
import axios from "axios";
import Swal from "sweetalert2";  

const column = createColumnHelper<wilayah>();

// ref utama
const paginateRef = ref<any>(null);
const trashRef    = ref<any>(null);     // ✅ tabel sampah
const selected    = ref<string>("");
const openForm    = ref<boolean>(false);

// hapus biasa
const { delete: deletewilayah } = useDelete({
    onSuccess: () => refresh(),
});

// hapus permanen (sampah)
const { delete: forceDeleteWilayah } = useDelete({
    onSuccess: () => trashRef.value.refetch(),
});

// restore data
// const { post: restoreWilayah } = useDelete({
//     onSuccess: () => trashRef.value.refetch(),
// });

// const restoreWilayah = (url: string) => {
//     axios.post(url).then(() => {
//         trashRef.value.refetch()
//         refresh()
//     });
// }

const restoreWilayah = (url: string) => {
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
    column.accessor("nama", { header: "Nama Wilayah" }),
    column.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
        h("div", { class: "d-flex gap-2" }, [
            // Tombol Edit
            h(
                "button",
                {
                    type: "button", // <=== WAJIB
                    class: "btn btn-sm btn-icon btn-info",
                    onClick: () => {
                        selected.value = String(cell.getValue());
                        openForm.value = true;
                    },
                },
                h("i", { class: "la la-pencil fs-2" })
            ),

            // Tombol Hapus (Soft Delete → masuk sampah)
            h("button", {
    type: "button",
    class: "btn btn-sm btn-icon btn-danger",
    "data-kt-ecommerce-delete": null, // matikan metronic
    "data-bs-toggle": null,
    "data-bs-target": null,
    onClick: (e: Event) => {
        e.stopPropagation();

        Swal.fire({
            title: "Hapus data?",
            text: "Data akan dipindahkan ke sampah.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                deletewilayah(`/wilayah/${cell.getValue()}`);
                paginateRef.value.refetch();
                trashRef.value.refetch();
            }
        });
    },
}, h("i", { class: "la la-trash fs-2" }))

            // h(
            //     "button",
            //     {
            //         type: "button", // <=== WAJIB biar tidak memicu modal default!
            //         class: "btn btn-sm btn-icon btn-danger",
            //         onClick: () => {
            //             Swal.fire({
            //                 title: "Hapus data?",
            //                 text: "Data akan dipindahkan ke sampah.",
            //                 icon: "warning",
            //                 showCancelButton: true,
            //                 confirmButtonText: "Ya, hapus",
            //                 cancelButtonText: "Batal",
            //             }).then((result) => {
            //                 if (result.isConfirmed) {
            //                     deletewilayah(`/wilayah/${cell.getValue()}`);
            //                     trashRef.value.refetch(); // refresh trash
            //                     paginateRef.value.refetch(); // refresh list utama
            //                 }
            //             });
            //         }
            //     },
            //     h("i", { class: "la la-trash fs-2" })
            // ),
        ]),
})

    // column.accessor("id", {
    //     header: "Aksi",
    //     cell: (cell) =>
    //         h("div", { class: "d-flex gap-2" }, [
    //             h("button", {
    //                 class: "btn btn-sm btn-icon btn-info",
    //                 onClick: () => {
    //                     selected.value = String(cell.getValue());
    //                     openForm.value = true;
    //                 },
    //             }, h("i", { class: "la la-pencil fs-2" })),
    //             h("button", {
    //                 class: "btn btn-sm btn-icon btn-danger",
    //                 onClick: () =>{
                        
    //                     deletewilayah(`/wilayah/${cell.getValue()}`)
    //                     trashRef.value.refetch()
    //                 }
    //             }, h("i", { class: "la la-trash fs-2" })),
    //         ]),
    // }),
];

// ✅ kolom tabel sampah
const trashColumns = [
    // column.accessor("no",       { header: "No" }),
    column.accessor("nama",     { header: "Nama Wilayah" }),
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
                        restoreWilayah(`/wilayah/${cell.getValue()}/restore`),
                }, h("i", { class: "la la-undo fs-2" })),
                h("button", {
                    class: "btn btn-sm btn-icon btn-danger",
                    title: "Hapus Permanen",
                    onClick: () =>
                        forceDeleteWilayah(`/wilayah/${cell.getValue()}/force-delete`),
                }, h("i", { class: "la la-trash fs-2" })),
            ]),
    }),
];

const refresh = () => {
    paginateRef.value.refetch();
    trashRef.value.refetch();
}
onMounted(() => {
    document
        .querySelectorAll("[data-kt-ecommerce-delete]")
        .forEach((el) => el.removeAttribute("data-kt-ecommerce-delete"));

    document.removeEventListener("kt.ecommerce.delete", () => {});
});

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
            <h2 class="mb-0">List Wilayah</h2>
            <button type="button" class="btn btn-sm btn-primary ms-auto"
                v-if="!openForm"
                @click="openForm = true">
                Tambah <i class="la la-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-wilayah"
                url="/wilayah"
                :columns="columns"
            />
        </div>
    </div>

    <!-- 🔹 Data Sampah -->
    <div class="card border-danger">
        <div class="card-header align-items-center">
            <h2 class="mb-0 text-danger">
                Wilayah <small>(data yang telah dihapus)</small>
            </h2>
        </div>
        <div class="card-body">
            <paginate
                ref="trashRef"
                id="table-wilayah_trash"
                url="/wilayah/trash"
                :columns="trashColumns"
            />
        </div>
    </div>
</template>
