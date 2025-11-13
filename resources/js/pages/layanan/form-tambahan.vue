<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { layanantambahan } from "@/types"; // ganti type sesuai typescript mu
import ApiService from "@/core/services/ApiService";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const dataTambahan = ref<layanantambahan>({} as layanantambahan);
const formRef = ref();

// ✅ Validasi form
const formSchema = Yup.object().shape({
  nama: Yup.string().required("Nama harus diisi"),
  harga: Yup.number().required("Harga harus diisi"),
});

function getEdit() {
  block(document.getElementById("form-layanan_tambahan"));
  ApiService.get(`/layanan_tambahan/${props.selected}`)
    .then(({ data }) => {
      dataTambahan.value = data;
    })
    .catch((err: any) => {
      toast.error(err.response?.data?.message || "Gagal memuat data");
    })
    .finally(() => {
      unblock(document.getElementById("form-layanan_tambahan"));
    });
}

function submit() {
  const formData = new FormData();
  formData.append("nama", dataTambahan.value.nama);
  formData.append("harga", String(dataTambahan.value.harga));

  if (props.selected) {
    formData.append("_method", "PUT");
  }

  block(document.getElementById("form-layanan_tambahan"));
  axios({
    method: "post",
    url: props.selected
      ? `/layanan_tambahan/store/${props.selected}`
      : "/layanan_tambahan/store",
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
      unblock(document.getElementById("form-layanan_tambahan"));
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
    id="form-layanan_tambahan"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Layanan Tambahan</h2>
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
            v-model="dataTambahan.nama"
            placeholder="Masukkan Nama Layanan Tambaha"
          />
          <ErrorMessage name="nama" class="fv-help-block text-danger" />
        </div>

        <!-- Harga -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Harga</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            name="harga"
            v-model="dataTambahan.harga"
            placeholder="Masukkan harga"
            type="number"
          />
          <ErrorMessage name="harga" class="fv-help-block text-danger" />
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
