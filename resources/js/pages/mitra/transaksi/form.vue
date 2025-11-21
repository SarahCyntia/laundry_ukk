<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-5">Detail Transaksi</h1>

    <div v-if="loading">Memuat data...</div>

    <div v-else>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">No Transaksi</label>
          <input class="form-control" v-model="form.kode_transaksi" disabled />
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Pelanggan</label>
          <input class="form-control" v-model="form.pelanggan?.name" disabled />
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Layanan</label>
          <input
            class="form-control"
            v-model="form.jenis_layanan?.nama_layanan"
            disabled
          />
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Status</label>
          <select v-model="form.status" class="form-control">
            <option value="pending">Pending</option>
            <option value="diproses">Diproses</option>
            <option value="selesai">Selesai</option>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Total Harga</label>
          <input
            class="form-control"
            type="number"
            v-model="form.total_harga"
            placeholder="Masukkan total harga"
          />
        </div>

        <div class="col-md-12 mb-3">
          <label class="form-label fw-bold">Catatan (Opsional)</label>
          <textarea
            class="form-control"
            rows="3"
            v-model="form.catatan"
            placeholder="Tambahkan catatan untuk pelanggan..."
          ></textarea>
        </div>
      </div>

      <div class="mt-4">
        <button class="btn btn-primary me-2" @click="simpan">
          Simpan Perubahan
        </button>
        <button class="btn btn-secondary" @click="kembali">Kembali</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "@/libs/axios";
import Swal from "sweetalert2";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const form = ref<any>({});

const getData = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`/mitra/transaksi/${route.params.id}`);
    form.value = res.data.data;
  } catch (err) {
    console.error(err);
  }
  loading.value = false;
};

const simpan = async () => {
  try {
    await axios.put(`/mitra/transaksi/${route.params.id}`, form.value);

    Swal.fire("Berhasil", "Transaksi berhasil diperbarui!", "success");

    router.push("/mitra/transaksi");
  } catch (err) {
    Swal.fire("Error", "Gagal memperbarui transaksi", "error");
  }
};

const kembali = () => {
  router.back();
};

onMounted(() => getData());
</script>

<style>
.row {
  display: flex;
  flex-wrap: wrap;
}
.col-md-6 {
  width: 50%;
  padding-right: 10px;
}
.col-md-12 {
  width: 100%;
}
</style>
