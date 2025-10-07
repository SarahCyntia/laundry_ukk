<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { datapelanggan } from "@/types"; // gunakan type pelanggan
import ApiService from "@/core/services/ApiService";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const datapelanggan = ref<datapelanggan>({} as datapelanggan);
const formRef = ref();

const formSchema = Yup.object().shape({
  nama: Yup.string().required("Nama harus diisi"),
  jenis_kelamin: Yup.string()
    .oneOf(["L", "P"], "Pilih jenis kelamin")
    .required("Jenis kelamin harus dipilih"),
  telepon: Yup.string().required("Nomor Telepon harus diisi"),
  alamat: Yup.string().required("Alamat harus diisi"),
});

function getEdit() {
  block(document.getElementById("form-datapelanggan"));
  ApiService.get("/datapelanggan", props.selected)
    .then(({ data }) => {
      datapelanggan.value = data.pelanggan;
    })
    .catch((err: any) => {
      toast.error(err.response.data.message);
    })
    .finally(() => {
      unblock(document.getElementById("form-datapelanggan"));
    });
}

function submit() {
  const formData = new FormData();
  formData.append("nama", datapelanggan.value.nama);
  formData.append("jenis_kelamin", datapelanggan.value.jenis_kelamin);
  formData.append("telepon", datapelanggan.value.telepon);
  formData.append("alamat", datapelanggan.value.alamat);

  if (props.selected) {
    formData.append("_method", "PUT");
  }

  block(document.getElementById("form-datapelanggan"));
  axios({
    method: "post",
    url: props.selected
      ? `/datapelanggan/store/${props.selected}`
      : "/datapelanggan/store",
    data: formData,
  })
    .then(() => {
      emit("close");
      emit("refresh");
      toast.success("Data berhasil disimpan");
      formRef.value.resetForm();
    })
    .catch((err: any) => {
      formRef.value.setErrors(err.response.data.errors);
      toast.error(err.response.data.message);
    })
    .finally(() => {
      unblock(document.getElementById("form-datapelanggan"));
    });
}

onMounted(() => {
  if (props.selected) {
    getEdit();
  }
});

watch(
  () => props.selected,
  () => {
    if (props.selected) {
      getEdit();
    }
  }
);
</script>

<template>
  <VForm
    class="form card mb-10"
    @submit="submit"
    :validation-schema="formSchema"
    id="form-datapelanggan"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Pelanggan</h2>
      <button
        type="button"
        class="btn btn-sm btn-light-danger ms-auto"
        @click="emit('close')"
      >
        Batal
        <i class="la la-times-circle p-0"></i>
      </button>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <!-- Nama -->
          <div class="fv-row mb-7">
            <label class="form-label fw-bold fs-6 required">Nama</label>
            <Field
              class="form-control form-control-lg form-control-solid"
              type="text"
              name="nama"
              v-model="datapelanggan.nama"
              placeholder="Masukkan Nama"
            />
            <ErrorMessage name="nama" class="fv-help-block text-danger" />
          </div>
        </div>

        <div class="col-md-6">
          <!-- Jenis Kelamin -->
          <div class="fv-row mb-7">
            <label class="form-label fw-bold fs-6 required">Jenis Kelamin</label>
            <div class="d-flex gap-3">
              <label class="form-check form-check-inline">
                <Field
                  type="radio"
                  name="jenis_kelamin"
                  value="L"
                  v-model="datapelanggan.jenis_kelamin"
                  class="form-check-input"
                />
                Laki-laki
              </label>
              <label class="form-check form-check-inline">
                <Field
                  type="radio"
                  name="jenis_kelamin"
                  value="P"
                  v-model="datapelanggan.jenis_kelamin"
                  class="form-check-input"
                />
                Perempuan
              </label>
            </div>
            <ErrorMessage name="jenis_kelamin" class="fv-help-block text-danger" />
          </div>
        </div>

        <div class="col-md-6">
          <!-- Telepon -->
          <div class="fv-row mb-7">
            <label class="form-label fw-bold fs-6 required">Nomor Telepon</label>
            <Field
              class="form-control form-control-lg form-control-solid"
              type="text"
              name="telepon"
              v-model="datapelanggan.telepon"
              placeholder="089"
            />
            <ErrorMessage name="telepon" class="fv-help-block text-danger" />
          </div>
        </div>

        <div class="col-md-6">
          <!-- Alamat -->
          <div class="fv-row mb-7">
            <label class="form-label fw-bold fs-6 required">Alamat</label>
            <Field
              as="textarea"
              class="form-control form-control-lg form-control-solid"
              name="alamat"
              v-model="datapelanggan.alamat"
              placeholder="Masukkan alamat lengkap"
            />
            <ErrorMessage name="alamat" class="fv-help-block text-danger" />
          </div>
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
