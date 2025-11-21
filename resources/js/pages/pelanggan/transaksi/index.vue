<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "@/libs/axios";
import Swal from "sweetalert2";

// Data transaksi
const transaksi = ref([]);
const loading = ref(false);

// Ambil data transaksi pelanggan
function loadTransaksi() {
  loading.value = true;
  axios.get("/pelanggan/transaksi")
    .then(res => transaksi.value = res.data)
    .finally(() => loading.value = false);
}

// Print resi
function printResi(id: number) {
  window.open(`/pelanggan/transaksi/${id}/print-resi`, "_blank");
}

onMounted(() => loadTransaksi());
</script>

<template>
  <div class="card">
    <div class="card-header">
      <h3>Daftar Transaksi Saya</h3>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>No</th>
            <th>Mitra Laundry</th>
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
            <td>{{ t.mitra.nama_laundry }}</td>
            <td>{{ t.layanan.nama_layanan }}</td>
            <td>{{ t.berat }}</td>
            <td>Rp {{ t.total_harga }}</td>
            <td>{{ t.status }}</td>
            <td>
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
