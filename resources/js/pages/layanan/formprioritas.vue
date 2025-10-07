<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { layananprioritas } from "@/types";
import ApiService from "@/core/services/ApiService";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const dataLayananPrioritas = ref<layananprioritas>({} as layananprioritas);
const formRef = ref();

// ✅ Validasi form
const formSchema = Yup.object().shape({
  nama: Yup.string().required("Nama harus diisi"),
  deskripsi: Yup.string().nullable(),
  harga: Yup.number().typeError("Harga harus angka").required("Harga wajib diisi"),
  prioritas: Yup.number().typeError("Prioritas harus angka").required("Prioritas wajib diisi"),
});

function getEdit() {
  block(document.getElementById("form-layanan_prioritas"));
  ApiService.get(`/layanan_prioritas/${props.selected}`)
    .then(({ data }) => {
      dataLayananPrioritas.value = data;
    })
    .catch((err: any) => {
      toast.error(err.response?.data?.message || "Gagal memuat data");
    })
    .finally(() => {
      unblock(document.getElementById("form-layanan_prioritas"));
    });
}

function submit() {
  const formData = new FormData();
  formData.append("nama", dataLayananPrioritas.value.nama);
  formData.append("deskripsi", dataLayananPrioritas.value.deskripsi ?? "");
  formData.append("harga", dataLayananPrioritas.value.harga?.toString() ?? "0");
  formData.append("prioritas", dataLayananPrioritas.value.prioritas?.toString() ?? "0");

  if (props.selected) formData.append("_method", "PUT");

  block(document.getElementById("form-layanan_prioritas"));
  axios({
    method: "post",
    url: props.selected ? `/layanan_prioritas/store/${props.selected}` : "/layanan_prioritas/store",
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
      unblock(document.getElementById("form-layanan_prioritas"));
    });
}

onMounted(() => {
  if (props.selected) getEdit();
});

watch(() => props.selected, () => {
  if (props.selected) getEdit();
});
</script>

<template>
  <VForm
    class="form card mb-10"
    @submit="submit"
    :validation-schema="formSchema"
    id="form-layanan_prioritas"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Layanan Prioritas</h2>
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
            v-model="dataLayananPrioritas.nama"
            placeholder="Masukkan Nama Layanan Prioritas"
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
            v-model="dataLayananPrioritas.deskripsi"
            placeholder="Deskripsi (opsional)"
          />
          <ErrorMessage name="deskripsi" class="fv-help-block text-danger" />
        </div>

        <!-- Harga -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Harga</label>
          <Field
            type="number"
            class="form-control form-control-lg form-control-solid"
            name="harga"
            v-model="dataLayananPrioritas.harga"
            placeholder="Masukkan Harga"
          />
          <ErrorMessage name="harga" class="fv-help-block text-danger" />
        </div>

        <!-- Prioritas -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Prioritas</label>
          <Field
            type="number"
            class="form-control form-control-lg form-control-solid"
            name="prioritas"
            v-model="dataLayananPrioritas.prioritas"
            placeholder="Masukkan Prioritas"
          />
          <ErrorMessage name="prioritas" class="fv-help-block text-danger" />
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
