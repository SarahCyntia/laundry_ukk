<script setup lang="ts">
import { ref, onMounted } from "vue";
import { Field, Form as VForm, ErrorMessage } from "vee-validate";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { block, unblock } from "@/libs/utils";
import ApiService from "@/core/services/ApiService";
import type { transaksilayanan } from "@/types/transaksilayanan";

const emit = defineEmits(["close", "refresh"]);
const formRef = ref();
const dataHarga = ref<transaksilayanan>({} as transaksilayanan);

// ✅ Data utama form
const formData = ref({
  pelanggan: "",
  layanan_prioritas: "",
  layanan_tambahan: [],
  jenis_pembayaran: "",
  bayar: 0,
  kembalian: 0,
  total_prioritas: 0,
  total_tambahan: 0,
  total_layanan: 0,
  total_bayar: 0,
  layanan: [], // list dinamis pakaian + layanan
});

// ✅ List data dari database
const listPelanggan = ref<any[]>([]);
const listPrioritas = ref<any[]>([]);
const listTambahan = ref<any[]>([]);
const listItem = ref<any[]>([]);
const listJenisLayanan = ref<any[]>([]);

// ✅ Validasi Yup (sudah disesuaikan dengan field yang ada)
const formSchema = Yup.object().shape({
  pelanggan: Yup.string().required("Pelanggan wajib dipilih"),
  jenis_pembayaran: Yup.string().required("Pilih jenis pembayaran"),
  bayar: Yup.number()
    .min(0, "Nominal tidak boleh negatif")
    .required("Masukkan nominal pembayaran"),
});

// ✅ Fungsi memuat data awal dari API
async function getDataAwal() {
  try {
    const el = document.getElementById("form-transaksi_layanan");
    if (el) block(el);

    const [pelangganRes, prioritasRes, tambahanRes, itemRes, layananRes] =
      await Promise.all([
        ApiService.get("/datapelangga/all"),
        ApiService.get("/layanan_prioritas/all"),
        ApiService.get("/layanan_tambahan/all"),
        ApiService.get("/jenis_ite/all"), // sesuai API kamu
        ApiService.get("/jenis_layana/all"), // sesuai API kamu (tanpa n)
      ]);

    listPelanggan.value = pelangganRes.data;
    listPrioritas.value = prioritasRes.data;
    listTambahan.value = tambahanRes.data;
    listItem.value = itemRes.data;
    listJenisLayanan.value = layananRes.data;

  } catch (err: any) {
    toast.error("Gagal memuat data awal");
  } finally {
    const el = document.getElementById("form-transaksi_layanan");
    if (el) unblock(el);
  }
}

// ✅ Tambah Layanan
function tambahLayanan() {
  formData.value.layanan.push({
    jenis_item: "",
    jenis_layanan: [],
    harga: 0,
    total_item: 1,
  });
}

// ✅ Hapus layanan
function hapusLayanan(index: number) {
  formData.value.layanan.splice(index, 1);
  hitungTotal();
}

function hapusSemuaLayanan() {
  formData.value.layanan = [];
  hitungTotal();
}

// ✅ Update harga berdasarkan jenis layanan yang dipilih
function updateHarga(index: number) {
  const layanan = formData.value.layanan[index];
  let totalHarga = 0;

  layanan.jenis_layanan.forEach((id: number) => {
    const jenis = listJenisLayanan.value.find((x) => x.id === id);
    if (jenis) totalHarga += Number(jenis.harga || 0);
  });

  layanan.harga = totalHarga;
  hitungTotal();
}

// ✅ Hitung total keseluruhan biaya
function hitungTotal() {
  let total = 0;
  formData.value.layanan.forEach((l) => {
    total += (l.harga || 0) * (l.total_item || 0);
  });
  formData.value.total_layanan = total;
  formData.value.total_bayar =
    total +
    (formData.value.total_prioritas || 0) +
    (formData.value.total_tambahan || 0);
}

// ✅ Hitung kembalian otomatis
function hitungKembalian() {
  const bayar = Number(formData.value.bayar || 0);
  const total = Number(formData.value.total_bayar || 0);
  formData.value.kembalian = bayar > total ? bayar - total : 0;
}

// ✅ Update total prioritas + tambahan
function updateTotal() {
  // hitung prioritas
  const prioritas = listPrioritas.value.find(
    (x) => x.id === formData.value.layanan_prioritas
  );
  formData.value.total_prioritas = prioritas ? prioritas.harga : 0;

  // hitung tambahan
  let totalTambahan = 0;
  formData.value.layanan_tambahan.forEach((id: number) => {
    const tambahan = listTambahan.value.find((x) => x.id === id);
    if (tambahan) totalTambahan += tambahan.harga || 0;
  });
  formData.value.total_tambahan = totalTambahan;

  hitungTotal();
}

// ✅ Format angka ke Rupiah
function formatRupiah(value: number) {
  if (!value) return "Rp0";
  return "Rp" + value.toLocaleString("id-ID");
}

// ✅ Submit form
function submit() {
  console.log("Submit jalan...");
  const payload = { ...formData.value };

  const el = document.getElementById("form-transaksi_layanan");
  if (el) block(el);

  axios
    .post("/transaksi_layanan/store", payload)
    .then(() => {
      toast.success("Transaksi berhasil ditambahkan");
      emit("refresh");
      emit("close");
      formRef.value.resetForm();
    })
    .catch((err: any) => {
      toast.error(err.response?.data?.message || "Gagal menyimpan transaksi");
    })
    .finally(() => {
      if (el) unblock(el);
    });
}

onMounted(() => {
  getDataAwal();
});
</script>

<template>
  <VForm
    class="form card mb-10"
    @submit="submit"
    :validation-schema="formSchema"
    id="form-transaksi_layanan"
    ref="formRef"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">Tambah Transaksi</h2>
      <button
        type="button"
        class="btn btn-sm btn-light-danger ms-auto"
        @click="emit('close')"
      >
        Kembali <i class="la la-times-circle p-0"></i>
      </button>
    </div>

    <div class="card-body">
      <!-- Pelanggan -->
      <div class="col-md-6">
        <label class="form-label fw-bold required">Pelanggan</label>
        <Field
          as="select"
          name="pelanggan"
          v-model="formData.pelanggan"
          class="form-select form-select-solid"
        >
          <option value="">Pilih Pelanggan!</option>
          <option v-for="item in listPelanggan" :key="item.id" :value="item.id">
            {{ item.nama }}
          </option>
        </Field>
        <ErrorMessage name="pelanggan" class="text-danger" />
        <small class="text-muted mt-2 d-block">
          Belum membuat daftar pelanggan?
          <RouterLink to="/admin/tambah-pelanggan" class="text-primary fw-semibold">
            Klik di sini
          </RouterLink>
        </small>
      </div>

      <hr />

      <!-- Detail Transaksi -->
      <h5 class="mb-4">Detail Transaksi</h5>

      <!-- Aksi Layanan -->
      <div class="mb-7">
        <label class="form-label fw-bold">Aksi Layanan</label>
        <div class="mb-3">
          <button
            type="button"
            class="btn btn-warning btn-sm me-2"
            @click="tambahLayanan"
            title="Tambah Layanan"
          >
            <i class="la la-plus"></i>
          </button>
          <button
            type="button"
            class="btn btn-danger btn-sm me-2"
            @click="hapusSemuaLayanan"
            title="Hapus Semua"
          >
            <i class="la la-trash"></i>
          </button>
          <button
            type="button"
            class="btn btn-info btn-sm"
            @click="hitungTotal"
            title="Hitung Ulang Total"
          >
            <i class="la la-calculator"></i>
          </button>
        </div>

        <!-- Daftar Layanan Dinamis -->
        <div
          v-for="(transaksilayanan, index) in formData.layanan"
          :key="index"
          class="row align-items-center mb-3"
        >
          <div class="col-md-3">
            <label class="form-label">Jenis Item</label>
            <select
              v-model="transaksilayanan.jenis_item"
              class="form-select form-select-solid"
              @change="updateHarga(index)"
            >
              <option value="">Pilih Item!</option>
              <option v-for="p in listItem" :key="p.id" :value="p.id">{{ p.nama }}</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Jenis Layanan</label>
            <select
              v-model="transaksilayanan.jenis_layanan"
              class="form-select form-select-solid"
              multiple
              @change="updateHarga(index)"
            >
              <option v-for="l in listJenisLayanan" :key="l.id" :value="l.id">
                {{ l.nama }}
              </option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Harga Jenis Layanan</label>
            <input
              type="text"
              class="form-control form-control-solid"
              :value="formatRupiah(transaksilayanan.harga)"
              readonly
            />
          </div>

          <div class="col-md-2">
            <label class="form-label">Total Item</label>
            <div class="input-group">
              <input
                type="number"
                min="1"
                class="form-control form-control-solid"
                v-model.number="transaksilayanan.total_item"
                @input="hitungTotal"
              />
              <span class="input-group-text">Kg</span>
            </div>
          </div>

          <div class="col-md-1 d-flex align-items-end">
            <button
              type="button"
              class="btn btn-danger btn-sm"
              @click="hapusLayanan(index)"
              title="Hapus Layanan"
            >
              <i class="la la-times"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Layanan Prioritas -->
      <div class="row mb-6">
        <div class="col-md-6">
          <label class="form-label fw-bold required">Layanan Prioritas</label>
          <Field
            as="select"
            name="layanan_prioritas"
            v-model="formData.layanan_prioritas"
            class="form-select form-select-solid"
            @change="updateTotal"
          >
            <option value="">Pilih Layanan Prioritas</option>
            <option v-for="item in listPrioritas" :key="item.id" :value="item.id">
              {{ item.nama }} (Rp{{ item.harga }}/kg)
            </option>
          </Field>
          <ErrorMessage name="layanan_prioritas" class="text-danger" />
        </div>

        <div class="col-md-6">
          <label class="form-label fw-bold">Total Biaya Prioritas</label>
          <input
            type="text"
            class="form-control form-control-solid"
            :value="formatRupiah(formData.total_prioritas)"
            readonly
          />
        </div>
      </div>

      <!-- Layanan Tambahan -->
      <div class="row mb-6">
        <div class="col-md-6">
          <label class="form-label fw-bold">Layanan Tambahan</label>
          <select
            multiple
            v-model="formData.layanan_tambahan"
            class="form-select form-select-solid"
            @change="updateTotal"
          >
            <option v-for="item in listTambahan" :key="item.id" :value="item.id">
              {{ item.nama }}
            </option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-bold">Total Biaya Layanan Tambahan</label>
          <input
            type="text"
            class="form-control form-control-solid"
            :value="formatRupiah(formData.total_tambahan)"
            readonly
          />
        </div>
      </div>

      <hr />

      <!-- Total Biaya -->
      <div class="row mb-6">
        <div class="col-md-6">
          <label class="form-label fw-bold required">Total Biaya Layanan</label>
          <input
            type="text"
            class="form-control form-control-solid"
            :value="formatRupiah(formData.total_layanan)"
            readonly
          />
        </div>

        <div class="col-md-6">
          <label class="form-label fw-bold required">Total Bayar</label>
          <input
            type="text"
            class="form-control form-control-solid"
            :value="formatRupiah(formData.total_bayar)"
            readonly
          />
        </div>
      </div>

      <!-- Jenis Pembayaran -->
      <div class="row mb-6">
        <div class="col-md-6">
          <label class="form-label fw-bold required">Jenis Pembayaran</label>
          <Field
            as="select"
            name="jenis_pembayaran"
            v-model="formData.jenis_pembayaran"
            class="form-select form-select-solid"
          >
            <option value="">Pilih Jenis Pembayaran!</option>
            <option value="cash">Cash</option>
            <option value="transfer">Transfer</option>
          </Field>
          <ErrorMessage name="jenis_pembayaran" class="text-danger" />
        </div>

        <div class="col-md-3">
          <label class="form-label fw-bold required">Bayar</label>
          <Field
            type="number"
            name="bayar"
            v-model="formData.bayar"
            class="form-control form-control-solid"
            @input="hitungKembalian"
          />
          <ErrorMessage name="bayar" class="text-danger" />
        </div>

        <div class="col-md-3">
          <label class="form-label fw-bold required">Kembalian</label>
          <input
            type="text"
            class="form-control form-control-solid"
            :value="formatRupiah(formData.kembalian)"
            readonly
          />
        </div>
      </div>
    </div>

    <div class="card-footer d-flex">
      <button type="submit" class="btn btn-success btn-sm ms-auto">
        Tambah Transaksi
      </button>
    </div>
  </VForm>
</template>
