<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import ApiService from "@/core/services/ApiService";
import { toast } from "vue3-toastify";
import * as Yup from "yup";
import { Field, ErrorMessage, Form as VForm } from "vee-validate";

// Ambil router & query
const route = useRoute();
const router = useRouter();

// Data form transaksi
const dataItem = ref({
  pelanggan_id: null,
  mitra_id: null,
  layanan_id: null,
  berat: 1,
  total_harga: 0,
  alamat_jemput: "",
  alamat_antar: "",
  status: "menunggu-jemput",
});

// Dropdown data
const layanan = ref([]);

// Form ref
const formRef = ref();

// Validasi form
const formSchema = Yup.object().shape({
  pelanggan_id: Yup.number().required("Pelanggan wajib dipilih"),
  layanan_id: Yup.number().required("Pilih layanan"),
  berat: Yup.number().min(1, "Minimal 1 Kg").required(),
  alamat_jemput: Yup.string().required("Alamat jemput wajib diisi"),
  alamat_antar: Yup.string().required("Alamat antar wajib diisi"),
});

// Load layanan dari Mitra
const loadLayanan = (mitraId: number) => {
  ApiService.get(`/mitra/${mitraId}`).then(res => {
    layanan.value = res.data.layanan;
    dataItem.value.mitra_id = res.data.id;
  });
};

// Hitung total otomatis
watch(
  () => [dataItem.value.berat, dataItem.value.layanan_id],
  () => {
    const selected = layanan.value.find((l: any) => l.id == dataItem.value.layanan_id);
    if (selected) {
      dataItem.value.total_harga = selected.harga_per_kg * dataItem.value.berat;
    }
  }
);

// Submit form
function submit() {
  const formData = new FormData();
  Object.keys(dataItem.value).forEach(key => formData.append(key, dataItem.value[key]));

  ApiService.post("/transaksi/store", formData)
    .then(() => {
      toast.success("Transaksi berhasil dibuat!");
      router.push("/"); // arahkan ke homepage atau halaman riwayat
    })
    .catch(err => {
      toast.error(err.response?.data?.message || "Terjadi kesalahan");
      formRef.value.setErrors(err.response?.data?.errors || {});
    });
}

// On mounted
onMounted(() => {
  const mitraId = Number(route.query.mitra_id);
  if (mitraId) loadLayanan(mitraId);

  // ambil id pelanggan dari user login (misal API atau store)
  dataItem.value.pelanggan_id = Number(route.query.pelanggan_id || null);
});
</script>

<template>
  <VForm @submit="submit" :validation-schema="formSchema" ref="formRef" class="card p-4">
    <h2>Form Transaksi Laundry</h2>

    <!-- Layanan -->
    <div class="mb-3">
      <label class="form-label">Pilih Layanan</label>
      <Field as="select" v-model="dataItem.layanan_id" class="form-control">
        <option value="">-- Pilih Layanan --</option>
        <option v-for="l in layanan" :key="l.id" :value="l.id">
          {{ l.nama_layanan }} (Rp {{ l.harga_per_kg }}/kg)
        </option>
      </Field>
      <ErrorMessage name="layanan_id" class="text-danger"/>
    </div>

    <!-- Berat -->
    <div class="mb-3">
      <label class="form-label">Berat (Kg)</label>
      <Field type="number" v-model="dataItem.berat" class="form-control" min="1"/>
      <ErrorMessage name="berat" class="text-danger"/>
    </div>

    <!-- Total -->
    <div class="mb-3">
      <label class="form-label">Total Harga</label>
      <input type="text" class="form-control" :value="dataItem.total_harga" readonly/>
    </div>

    <!-- Alamat Jemput -->
    <div class="mb-3">
      <label class="form-label">Alamat Jemput</label>
      <Field v-model="dataItem.alamat_jemput" class="form-control"/>
      <ErrorMessage name="alamat_jemput" class="text-danger"/>
    </div>

    <!-- Alamat Antar -->
    <div class="mb-3">
      <label class="form-label">Alamat Antar</label>
      <Field v-model="dataItem.alamat_antar" class="form-control"/>
      <ErrorMessage name="alamat_antar" class="text-danger"/>
    </div>

    <button type="submit" class="btn btn-primary">Buat Transaksi</button>
  </VForm>
</template>

<style scoped>
.card { max-width: 600px; margin: 20px auto; }
</style>
