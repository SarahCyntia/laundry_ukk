<script setup lang="ts">
import { h, ref, watch, onMounted } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { datapelanggan } from "@/types";
import axios from "axios"; // Pastikan axios sudah terinstall

const column = createColumnHelper();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);
const datapelangganData = ref<datapelanggan | null>(null); // Data pelanggan yang terkait dengan user login

const { delete: deletedatapelanggan } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
    column.accessor("no", { header: "No" }),
    column.accessor("user.name", { header: "Nama" }),
    column.accessor("user.email", { header: "Email" }),
    column.accessor("user.phone", { header: "No. Telp" }),
    column.accessor("alamat", { header: "Alamat" }),
   
column.accessor("user.photo", {
    header: "Foto",
    cell: (cell) => {
        const val = cell.getValue();
        console.log("Photo path:", val);
        return h("img", {
            src: val ? `/storage/${val}` : "/img/default.png",
            alt: "Foto pelanggan",
            class: "img-thumbnail",
            style: "width: 50px; height: 50px;",
        });
    },
}),
    column.accessor("id", {
        header: "Aksi",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
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
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-danger",
                        onClick: () => deletedatapelanggan(`datapelanggan/${cell.getValue()}`),
                    },
                    h("i", { class: "la la-trash fs-2" })
                ),
            ]),
    }),
];
const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0, 0);
});
</script>

<template>
    <Form
        :selected="selected"
        @close="openForm = false"
        v-if="openForm"
        @refresh="refresh"
    />

    <div class="card">
        <div class="card-header align-items-center">
            <h2 class="mb-0">Data pelanggan</h2>
        </div>
        <div class="card-body">
            <!-- <p v-if="pelangganData">Data pelanggan: {{ pelangganData }}</p> -->
            <paginate
                ref="paginateRef"
                id="table-datapelanggan"
                url="datapelanggan"
                :columns="columns"
            ></paginate>
        </div>
    </div>
</template>
