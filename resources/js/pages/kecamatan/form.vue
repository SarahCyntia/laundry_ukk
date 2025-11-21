<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import ApiService from "@/core/services/ApiService";
import { toast } from "vue3-toastify";

const props = defineProps({
  selected: { type: String, default: null },
});

const emit = defineEmits(["close", "refresh"]);

const dataKecamatan = ref({
  nama: "",
  wilayah_id: "",
});

const wilayahList = ref([]);

const formRef = ref();

// Validation
const schema = Yup.object().shape({
  nama: Yup.string().required("Nama kecamatan harus diisi"),
  wilayah_id: Yup.string().required("Wilayah harus dipilih"),
});

// Load dropdown wilayah
function getWilayah() {
  ApiService.get("/wilayah/all").then(({ data }) => {
    wilayahList.value = data;
  });
}

// Ambil data untuk edit
function getEdit() {
  block(document.getElementById("form-kecamatan"));
  ApiService.get(`/kecamatan/${props.selected}`)
    .then(({ data }) => (dataKecamatan.value = data))
    .finally(() => unblock(document.getElementById("form-kecamatan")));
}

// Submit
function submit() {
  const formData = new FormData();
  formData.append("nama", dataKecamatan.value.nama);
  formData.append("wilayah_id", dataKecamatan.value.wilayah_id);

  if (props.selected) formData.append("_method", "PUT");

  block(document.getElementById("form-kecamatan"));

  ApiService.post(
    props.selected ? `/kecamatan/${props.selected}` : "/kecamatan",
    formData
  )
    .then(() => {
      toast.success("Data berhasil disimpan");
      emit("refresh");
      emit("close");
    })
    .catch((err) => {
      formRef.value.setErrors(err.response?.data?.errors || {});
      toast.error("Terjadi kesalahan");
    })
    .finally(() => unblock(document.getElementById("form-kecamatan")));
}

onMounted(() => {
  getWilayah();

  if (props.selected) getEdit();
});

watch(
  () => props.selected,
  () => props.selected && getEdit()
);
</script>

<template>
  <VForm
    id="form-kecamatan"
    ref="formRef"
    @submit="submit"
    :validation-schema="schema"
    class="form card mb-10"
  >
    <div class="card-header">
      <h2>{{ props.selected ? "Edit" : "Tambah" }} Kecamatan</h2>
      <button class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
        Batal
      </button>
    </div>

    <div class="card-body">
      <div class="mb-7">
        <label class="form-label fw-bold fs-6 required">Nama Kecamatan</label>
        <Field
          name="nama"
          v-model="dataKecamatan.nama"
          class="form-control form-control-lg form-control-solid"
          placeholder="Masukkan nama kecamatan"
        />
        <ErrorMessage name="nama" class="text-danger" />
      </div>

      <div class="mb-7">
        <label class="form-label fw-bold fs-6 required">Wilayah</label>
        <Field
          as="select"
          name="wilayah_id"
          v-model="dataKecamatan.wilayah_id"
          class="form-control form-control-lg form-control-solid"
        >
          <option value="">-- pilih wilayah --</option>
          <option
            v-for="w in wilayahList"
            :key="w.id"
            :value="w.id"
          >
            {{ w.nama }}
          </option>
        </Field>
        <ErrorMessage name="wilayah_id" class="text-danger" />
      </div>
    </div>

    <div class="card-footer text-end">
      <button class="btn btn-primary btn-sm">Simpan</button>
    </div>
  </VForm>
</template>
