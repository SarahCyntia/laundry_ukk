<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { jenisitem } from "@/types"; // ganti type sesuai typescript mu
import ApiService from "@/core/services/ApiService";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const dataItem = ref<jenisitem>({} as jenisitem);
const formRef = ref();

// ✅ Validasi form
const formSchema = Yup.object().shape({
  nama: Yup.string().required("Nama harus diisi"),
  deskripsi: Yup.string().nullable(),
});

function getEdit() {
  block(document.getElementById("form-jenis_item"));
  ApiService.get(`/jenis_item/${props.selected}`)
    .then(({ data }) => {
      dataItem.value = data;
    })
    .catch((err: any) => {
      toast.error(err.response?.data?.message || "Gagal memuat data");
    })
    .finally(() => {
      unblock(document.getElementById("form-jenis_item"));
    });
}

function submit() {
  const formData = new FormData();
  formData.append("nama", dataItem.value.nama);
  formData.append("deskripsi", dataItem.value.deskripsi ?? "");

  if (props.selected) {
    formData.append("_method", "PUT");
  }

  block(document.getElementById("form-jenis_item"));
  axios({
    method: "post",
    url: props.selected
      ? `/jenis_item/store/${props.selected}`
      : "/jenis_item/store",
    data: formData,
  })
    .then(() => {
      emit("close");
      emit("refresh");
      toast.success("Data berhasil disimpan");
      formRef.value.resetForm();
    })
    .catch((err: any) => {
      formRef.value.setErrors(err.response?.data?.errors || {});
      toast.error(err.response?.data?.message || "Terjadi kesalahan");
    })
    .finally(() => {
      unblock(document.getElementById("form-jenis_item"));
    });
}

onMounted(() => {
  if (props.selected) getEdit();
});

watch(
  () => props.selected,
  () => {
    if (props.selected) getEdit();
  }
);
</script>

<template>
  <VForm
    class="form card mb-10"
    @submit="submit"
    :validation-schema="formSchema"
    id="form-jenis_item"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Jenis Item</h2>
      <button
        type="button"
        class="btn btn-sm btn-light-danger ms-auto"
        @click="emit('close')"
      >
        Batal <i class="la la-times-circle p-0"></i>
      </button>
    </div>

    <div class="card-body">
      <div class="row">
        <!-- Nama -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Nama</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            name="nama"
            v-model="dataItem.nama"
            placeholder="Masukkan Nama Jenis Item"
          />
          <ErrorMessage name="nama" class="fv-help-block text-danger" />
        </div>

        <!-- Deskripsi -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6">Deskripsi</label>
          <Field
            as="textarea"
            class="form-control form-control-lg form-control-solid"
            name="deskripsi"
            v-model="dataItem.deskripsi"
            placeholder="Deskripsi item (opsional)"
          />
          <ErrorMessage name="deskripsi" class="fv-help-block text-danger" />
        </div>

    
      </div>
    </div>

    <div class="card-footer d-flex">
      <button type="submit" class="btn btn-primary btn-sm ms-auto">
        Simpan
      </button>
    </div>
  </VForm>
</template>
