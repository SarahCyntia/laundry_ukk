<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "@/libs/axios";
import Swal from "sweetalert2";

const transaksi = ref([]);
const loading = ref(false);

// Ambil data transaksi Mitra
function loadTransaksi() {
  loading.value = true;
  axios.get("/transaksi")
    .then(res => transaksi.value = res.data)
    .finally(() => loading.value = false);
}

// Terima transaksi
function terimaTransaksi(id: number) {
  Swal.fire({
    title: "Terima transaksi ini?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya",
    cancelButtonText: "Batal"
  }).then(result => {
    if(result.isConfirmed){
      axios.put(`/transaksi/${id}/status`, { status: "diterima" })
        .then(() => {
          Swal.fire("Berhasil!", "Transaksi diterima", "success");
          loadTransaksi();
        })
        .catch(() => Swal.fire("Gagal", "Terjadi kesalahan", "error"));
    }
  });
}

// Update status proses Mitra (dicuci → selesai / ditolak)
function updateStatus(id: number, status: string) {
  Swal.fire({
    title: `Ubah status menjadi "${status}"?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya",
    cancelButtonText: "Batal"
  }).then(result => {
    if(result.isConfirmed){
      axios.put(`/transaksi/${id}/status`, { status })
        .then(() => {
          Swal.fire("Berhasil!", "Status transaksi diperbarui", "success");
          loadTransaksi();
        })
        .catch(() => Swal.fire("Gagal", "Terjadi kesalahan", "error"));
    }
  });
}

// Print resi
function printResi(id: number) {
  window.open(`/mitra/transaksi/${id}/print-resi`, "_blank");
}

onMounted(() => loadTransaksi());
</script>

<template>
  <div class="card">
    <div class="card-header d-flex align-items-center">
      <h3 class="mb-0">Transaksi Masuk</h3>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>No</th>
            <th>Pelanggan</th>
            <th>Layanan</th>
            <th>Berat (Kg)</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(t, index) in transaksi" :key="t.id">
            <td>{{ index + 1 }}</td>
            <td>{{ t.pelanggan.nama }}</td>
            <td>{{ t.layanan.nama_layanan }}</td>
            <td>{{ t.berat }}</td>
            <td>Rp {{ t.total }}</td>
            <td>{{ t.status }}</td>
            <td>
              <!-- Tombol Terima / Tolak untuk transaksi pending -->
              <template v-if="t.status === 'pending'">
                <button class="btn btn-sm btn-success me-1" @click="terimaTransaksi(t.id)">
                  Terima
                </button>
                <button class="btn btn-sm btn-danger me-1" @click="updateStatus(t.id, 'ditolak')">
                  Tolak
                </button>
              </template>

              <!-- Tombol update status Mitra -->
              <template v-else-if="t.status === 'diterima'">
                <button class="btn btn-sm btn-success me-1" @click="updateStatus(t.id, 'dicuci')">
                  Mulai Cuci
                </button>
              </template>

              <template v-else-if="t.status === 'dicuci'">
                <button class="btn btn-sm btn-success me-1" @click="updateStatus(t.id, 'selesai')">
                  Selesai
                </button>
              </template>

              <!-- Tombol Print Resi selalu ada -->
              <button class="btn btn-sm btn-primary" @click="printResi(t.id)">
                Print Resi
              </button>
            </td>
          </tr>

          <tr v-if="transaksi.length === 0">
            <td colspan="7" class="text-center">Belum ada transaksi</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
