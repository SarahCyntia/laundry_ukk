<script setup lang="ts">
import { h, ref, watch, onMounted } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { kecamatan } from "@/types";
import axios from "axios";
import Swal from "sweetalert2";  

const column = createColumnHelper<kecamatan>();

// ref utama
const paginateRef = ref<any>(null);
const trashRef    = ref<any>(null);     // ✅ tabel sampah
const selected    = ref<string>("");
const openForm    = ref<boolean>(false);



const { delete: deleteKecamatan } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});


// const restoreKecamatan = (url: string) => {
//     Swal.fire({
//         title: "Pulihkan data?",
//         text: "Data akan dikembalikan ke daftar utama.",
//         icon: "question",
//         showCancelButton: true,
//         confirmButtonText: "Ya, pulihkan",
//         cancelButtonText: "Batal",
//     }).then((result) => {
//         if (result.isConfirmed) {
//             axios.post(url).then(() => {
//                 Swal.fire({
//                     icon: "success",
//                     title: "Berhasil",
//                     text: "Data berhasil dipulihkan!",
//                     timer: 1500,
//                     showConfirmButton: false,
//                 });
//                 refresh();  // otomatis refresh kedua tabel
//             }).catch(() => {
//                 Swal.fire({
//                     icon: "error",
//                     title: "Gagal",
//                     text: "Terjadi kesalahan saat memulihkan data.",
//                 });
//             });
//         }
//     });
// };
// kolom tabel utama
const columns = [
    column.accessor("no", { header: "No" }),
    column.accessor("nama", { header: "Nama Kecamatan" }),
    column.accessor("id", {
  header: "Aksi",
  cell: (cell) =>
    h("div", { class: "d-flex gap-2" }, [
    //   h(
    //     "button",
    //     {
    //       class: "btn btn-sm btn-icon btn-info",
    //       onClick: () => {
    //         selected.value = String(cell.getValue());
    //         openForm.value = true;
    //       },
    //     },
    //     h("i", { class: "la la-pencil fs-2" })
    //   ),
      h(
        "button",
        {
          class: "btn btn-sm btn-icon btn-danger",
          onClick: () =>
            deleteKecamatan(`/kecamatan/${cell.getValue()}`),
        },
        h("i", { class: "la la-trash fs-2" })
      ),
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
            <h2 class="mb-0">List Kecamatan</h2>
            <button type="button" class="btn btn-sm btn-primary ms-auto"
                v-if="!openForm"
                @click="openForm = true">
                Tambah <i class="la la-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <paginate
                ref="paginateRef"
                id="table-kecamatan"
                url="/kecamatan"
                :columns="columns"
            />
        </div>
    </div>


</template>
