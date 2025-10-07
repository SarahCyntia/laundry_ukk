<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import ApiService from "@/core/services/ApiService";
import type { hargajenislayanan } from "@/types/hargaJenisLayanan";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const dataHarga = ref<hargajenislayanan>({} as hargajenislayanan);
const formRef = ref();

// dropdown pilihan
const jenisLayananOptions = ref("");
const jenisItemOptions = ref("");
// const jenisLayananOptions = ref<{ id:number; nama_layanan:string }[]>([]);
// const jenisItemOptions = ref<{ id:number; nama:string }[]>([]);

// ✅ Validasi form
const formSchema = Yup.object().shape({
  harga: Yup.number().required("Harga harus diisi"),
  jenis_satuan: Yup.string().required("Jenis Satuan harus diisi"),
  jenis_layanan_id: Yup.string().required("Pilih Jenis Layanan"),
  jenis_item_id: Yup.string().required("Pilih Jenis Item"),
});

function loadDropdown() {
  // ambil data untuk select
  // ApiService.get("/jenis_layanan/all").then(({data})=>{
  //   jenisLayananOptions.value = data;
  // });
ApiService.get("/jenis_layana/all").then(({data})=>{
  console.log("jenis_layanan =>", data);   // 🔴 Cek di console browser
  jenisLayananOptions.value = data;
})
  ApiService.get("/jenis_ite/all").then(({data})=>{
    jenisItemOptions.value = data;
  });
}

function getEdit() {
  block(document.getElementById("form-harga_jenis_layanan"));
  ApiService.get(`/harga_jenis_layanan/${props.selected}`)
    .then(({ data }) => {
      dataHarga.value = data;
    })
    .catch((err:any) => {
      toast.error(err.response?.data?.message || "Gagal memuat data");
    })
    .finally(() => {
      unblock(document.getElementById("form-harga_jenis_layanan"));
    });
}

function submit() {
  const formData = new FormData();
  formData.append("harga", String(dataHarga.value.harga));
  formData.append("jenis_satuan", dataHarga.value.jenis_satuan);
  formData.append("jenis_layanan_id", String(dataHarga.value.jenis_layanan_id));
  formData.append("jenis_item_id", String(dataHarga.value.jenis_item_id));

  if (props.selected) {
    formData.append("_method", "PUT");
  }

  block(document.getElementById("form-harga_jenis_layanan"));
  axios({
    method: "post",
    url: props.selected
      ? `/harga_jenis_layanan/store/${props.selected}`
      : "/harga_jenis_layanan/store",
    data: formData,
  })
    .then(() => {
      emit("close");
      emit("refresh");
      toast.success("Data berhasil disimpan");
      formRef.value.resetForm();
    })
    .catch((err:any) => {
      formRef.value.setErrors(err.response?.data?.errors || {});
      toast.error(err.response?.data?.message || "Terjadi kesalahan");
    })
    .finally(() => {
      unblock(document.getElementById("form-harga_jenis_layanan"));
    });
}

onMounted(() => {
  loadDropdown();
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
    id="form-harga_jenis_layanan"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Harga Jenis Layanan</h2>
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
        <!-- Harga -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Harga</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            name="harga"
            v-model="dataHarga.harga"
            placeholder="Masukkan harga"
            type="number"
          />
          <ErrorMessage name="harga" class="fv-help-block text-danger" />
        </div>

        <!-- Jenis Satuan -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Jenis Satuan</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            name="jenis_satuan"
            v-model="dataHarga.jenis_satuan"
            placeholder="Contoh: kg / pcs"
          />
          <ErrorMessage name="jenis_satuan" class="fv-help-block text-danger" />
        </div>

        <!-- Jenis Layanan -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Jenis Layanan</label>
          <Field
            as="select"
            name="jenis_layanan_id"
            class="form-select form-select-lg form-select-solid"
            v-model="dataHarga.jenis_layanan_id"
          >
            <option value="">-- Pilih --</option>
            <option v-for="jl in jenisLayananOptions" :key="jl.id" :value="jl.id">
              {{ jl.nama_layanan }}
            </option>
          </Field>
          <ErrorMessage name="jenis_layanan_id" class="fv-help-block text-danger" />
        </div>

        <!-- Jenis item -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Jenis Item</label>
          <Field
            as="select"
            name="jenis_item_id"
            class="form-select form-select-lg form-select-solid"
            v-model="dataHarga.jenis_item_id"
          >
            <option value="">-- Pilih --</option>
            <option v-for="ji in jenisItemOptions" :key="ji.id" :value="ji.id">
              {{ ji.nama }}
            </option>
          </Field>
          <ErrorMessage name="jenis_item_id" class="fv-help-block text-danger" />
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
