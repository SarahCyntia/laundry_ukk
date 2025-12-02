<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { ref, watch, onMounted } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const props = defineProps({
  selected: {
    type: [String, Number],
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const formRef = ref();

const layanan = ref({
  nama_layanan: "",
  deskripsi: "",
  satuan: "kiloan", // default
  harga: 0,
});

// ===================== VALIDATION =====================
const formSchema = Yup.object().shape({
  nama_layanan: Yup.string().required("Nama layanan wajib diisi"),
  deskripsi: Yup.string().nullable(),
  satuan: Yup.string().required("Satuan wajib dipilih"),
  harga: Yup.number()
    .required("Harga wajib diisi")
    .min(0, "Harga tidak boleh minus"),
});

// ===================== GET DATA EDIT =====================
function getEdit() {
  if (!props.selected) return;

  block(document.getElementById("form-layanan"));

  axios
    .get(`/jenis-layanan/${props.selected}`)
    .then(({ data }) => {
      layanan.value = {
        nama_layanan: data.nama_layanan,
        deskripsi: data.deskripsi,
        satuan: data.satuan,
        harga: data.harga,
      };
    })
    .catch((err) => {
      toast.error(err.response?.data?.message || "Gagal mengambil data");
    })
    .finally(() => unblock(document.getElementById("form-layanan")));
}

// ===================== SUBMIT =====================
function submit() {
  const payload = { ...layanan.value };

  block(document.getElementById("form-layanan"));

  axios({
    method: props.selected ? "put" : "post",
    url: props.selected
      ? `/jenis-layanan/${props.selected}`
      : "/jenis-layanan/store",
    data: payload,
  })
    .then(() => {
      toast.success("Layanan berhasil disimpan");
      emit("refresh");
      emit("close");
      formRef.value.resetForm();
    })
    .catch((err) => {
      formRef.value.setErrors(err.response?.data?.errors || {});
      toast.error(err.response?.data?.message || "Terjadi kesalahan");
    })
    .finally(() => unblock(document.getElementById("form-layanan")));
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
    id="form-layanan"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">
        {{ selected ? "Edit" : "Tambah" }} Jenis Layanan
      </h2>

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

      <!-- Nama Layanan -->
      <div class="fv-row mb-7">
        <label class="form-label fw-bold fs-6 required">Nama Layanan</label>
        <Field
          class="form-control form-control-lg form-control-solid"
          type="text"
          name="nama_layanan"
          v-model="layanan.nama_layanan"
          placeholder="Contoh: Cuci Kiloan, Cuci Sepatu"
        />
        <ErrorMessage name="nama_layanan" class="fv-help-block" />
      </div>

      <!-- Deskripsi -->
      <div class="fv-row mb-7">
        <label class="form-label fw-bold fs-6">Deskripsi</label>
        <Field
          as="textarea"
          name="deskripsi"
          class="form-control form-control-lg form-control-solid"
          v-model="layanan.deskripsi"
          placeholder="Contoh: Pengerjaan 1–2 hari"
        />
      </div>

      <!-- Satuan -->
      <div class="fv-row mb-7">
        <label class="form-label fw-bold fs-6 required">Satuan</label>
        <Field
          as="select"
          name="satuan"
          class="form-control form-control-lg form-control-solid"
          v-model="layanan.satuan"
        >
          <option value="kiloan">Kiloan</option>
          <option value="satuan">Satuan</option>
        </Field>
        <ErrorMessage name="satuan" class="fv-help-block" />
      </div>

      <!-- Harga -->
      <div class="fv-row mb-7">
        <label class="form-label fw-bold fs-6 required">Harga</label>
        <Field
          type="number"
          name="harga"
          class="form-control form-control-lg form-control-solid"
          v-model.number="layanan.harga"
          placeholder="Contoh: 8000"
        />
        <ErrorMessage name="harga" class="fv-help-block" />
      </div>
    </div>

    <div class="card-footer d-flex">
      <button type="submit" class="btn btn-primary btn-sm ms-auto">
        Simpan
      </button>
    </div>
  </VForm>
</template>
