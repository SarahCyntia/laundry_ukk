<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { wilayah } from "@/types"; // ganti type sesuai typescript mu
import ApiService from "@/core/services/ApiService";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const dataItem = ref<wilayah>({} as wilayah);
const formRef = ref();

// ✅ Validasi form
const formSchema = Yup.object().shape({
  nama: Yup.string().required("Nama harus diisi"),
  deskripsi: Yup.string().nullable(),
});

function getEdit() {
  block(document.getElementById("form-wilayah"));
  ApiService.get(`/wilayah/${props.selected}`)
    .then(({ data }) => {
      dataItem.value = data;
    })
    .catch((err: any) => {
      toast.error(err.response?.data?.message || "Gagal memuat data");
    })
    .finally(() => {
      unblock(document.getElementById("form-wilayah"));
    });
}

function submit() {
  const formData = new FormData();
  formData.append("nama", dataItem.value.nama);

  if (props.selected) {
    formData.append("_method", "PUT");
  }

  block(document.getElementById("form-wilayah"));
  axios({
    method: "post",
    url: props.selected
      ? `/wilayah/store/${props.selected}`
      : "/wilayah/store",
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
      unblock(document.getElementById("form-wilayah"));
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
    id="form-wilayah"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Wilayah</h2>
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
            placeholder="Masukkan Nama Wilayah"
          />
          <ErrorMessage name="nama" class="fv-help-block text-danger" />
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
