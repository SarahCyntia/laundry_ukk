<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { mitra } from "@/types";
import ApiService from "@/core/services/ApiService";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const dataMitra = ref<mitra>({} as mitra);
const formRef = ref();

// ✅ Validasi form
const formSchema = Yup.object().shape({
  nama_mitra: Yup.string().required("Nama mitra harus diisi"),
  pemilik: Yup.string().required("Nama pemilik harus diisi"),
  email: Yup.string().email("Email tidak valid").required("Email harus diisi"),
  no_hp: Yup.string().nullable(),
  alamat: Yup.string().nullable(),
  password: Yup.string().when([], {
    is: () => !props.selected,
    then: (schema) => schema.required("Password wajib diisi"),
    otherwise: (schema) => schema.nullable(),
  }),
  status: Yup.string().oneOf(["aktif", "nonaktif"]).required(),
});

// 🔹 Ambil data untuk edit
function getEdit() {
  block(document.getElementById("form-mitra"));
  ApiService.get(`/mitra/${props.selected}`)
    .then(({ data }) => {
      dataMitra.value = data;
    })
    .catch((err: any) => {
      toast.error(err.response?.data?.message || "Gagal memuat data");
    })
    .finally(() => {
      unblock(document.getElementById("form-mitra"));
    });
}

// 🔹 Submit form (create / update)
function submit() {
  const formData = new FormData();
  formData.append("nama_mitra", dataMitra.value.nama_mitra);
  formData.append("pemilik", dataMitra.value.pemilik);
  formData.append("email", dataMitra.value.email);
  formData.append("no_hp", dataMitra.value.no_hp ?? "");
  formData.append("alamat", dataMitra.value.alamat ?? "");
  formData.append("status", dataMitra.value.status ?? "aktif");

  if (dataMitra.value.password) {
    formData.append("password", dataMitra.value.password);
  }

  if (props.selected) {
    formData.append("_method", "PUT");
  }

  block(document.getElementById("form-mitra"));
  axios({
    method: "post",
    url: props.selected ? `/mitra/store/${props.selected}` : "/mitra/store",
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
      unblock(document.getElementById("form-mitra"));
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
    id="form-mitra"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Mitra</h2>
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
        <!-- Nama Mitra -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Nama Mitra</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            name="nama_mitra"
            v-model="dataMitra.nama_mitra"
            placeholder="Masukkan nama mitra"
          />
          <ErrorMessage name="nama_mitra" class="fv-help-block text-danger" />
        </div>

        <!-- Pemilik -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Pemilik</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            name="pemilik"
            v-model="dataMitra.pemilik"
            placeholder="Masukkan nama pemilik"
          />
          <ErrorMessage name="pemilik" class="fv-help-block text-danger" />
        </div>

        <!-- Email -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Email</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            name="email"
            v-model="dataMitra.email"
            placeholder="Masukkan email mitra"
          />
          <ErrorMessage name="email" class="fv-help-block text-danger" />
        </div>

        <!-- Nomor HP -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6">Nomor HP</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            name="no_hp"
            v-model="dataMitra.no_hp"
            placeholder="Masukkan nomor HP (opsional)"
          />
          <ErrorMessage name="no_hp" class="fv-help-block text-danger" />
        </div>

        <!-- Alamat -->
        <div class="col-md-12 mb-7">
          <label class="form-label fw-bold fs-6">Alamat</label>
          <Field
            as="textarea"
            class="form-control form-control-lg form-control-solid"
            name="alamat"
            v-model="dataMitra.alamat"
            placeholder="Masukkan alamat mitra"
          />
          <ErrorMessage name="alamat" class="fv-help-block text-danger" />
        </div>

        <!-- Password -->
        <div class="col-md-6 mb-7" v-if="!selected">
          <label class="form-label fw-bold fs-6 required">Password</label>
          <Field
            type="password"
            class="form-control form-control-lg form-control-solid"
            name="password"
            v-model="dataMitra.password"
            placeholder="Masukkan password"
          />
          <ErrorMessage name="password" class="fv-help-block text-danger" />
        </div>

        <!-- Status -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Status</label>
          <Field
            as="select"
            class="form-select form-select-lg form-select-solid"
            name="status"
            v-model="dataMitra.status"
          >
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </Field>
          <ErrorMessage name="status" class="fv-help-block text-danger" />
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
