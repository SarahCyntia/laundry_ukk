<template>
  <div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-bold">Transaksi Layanan</h5>
      <button class="btn btn-success btn-sm">
        + Tambah
      </button>
    </div>

    <!-- Search -->
    <div class="d-flex justify-content-between mb-2">
      <div>
        <label>
          <select v-model="entriesPerPage" class="form-select form-select-sm d-inline-block w-auto">
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
          </select>
          entries per page
        </label>
      </div>
      <div>
        <label>
          Search:
          <input v-model="search" type="text" class="form-control form-control-sm d-inline-block w-auto" />
        </label>
      </div>
    </div>

    <!-- Table -->
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-primary">
        <tr>
          <th>WAKTU</th>
          <th>LAYANAN PRIORITAS</th>
          <th>TOTAL BAYAR</th>
          <th>PELANGGAN</th>
          <th>PEGAWAI</th>
          <th>AKSI</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(trx, i) in filteredTransactions" :key="i">
          <td>
            <div>{{ trx.waktu }}</div>
            <div class="fw-bold text-primary">{{ trx.garment }}</div>
            <div>
              <span class="badge bg-success" v-if="trx.status === 'Selesai'">Selesai</span>
              <span class="badge bg-warning text-dark" v-else-if="trx.status === 'Proses'">Proses</span>
              <span class="badge bg-danger" v-else>Pending</span>
            </div>
          </td>
          <td>{{ trx.layanan }}</td>
          <td>{{ trx.total }}</td>
          <td>{{ trx.pelanggan }}</td>
          <td>{{ trx.pegawai }}</td>
          <td>
            <button class="btn btn-info btn-sm me-1">
              <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-warning btn-sm me-1">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-danger btn-sm me-1">
              <i class="bi bi-trash"></i>
            </button>
            <button class="btn btn-secondary btn-sm">
              <i class="bi bi-printer"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";

const entriesPerPage = ref(10);
const search = ref("");

const transactions = ref([
  {
    waktu: "20 July 2024 18:35:17",
    layanan: "Reguler",
    total: "Rp138.000,00",
    pelanggan: "Umar Thamrin S.Sos",
    pegawai: "Pegawai Laundry 4",
    garment: "Gamis 3",
    status: "Selesai",
  },
  {
    waktu: "20 July 2024 10:36:41",
    layanan: "Reguler",
    total: "Rp138.000,00",
    pelanggan: "Manah Pardi Iswahyudi",
    pegawai: "Pegawai Laundry 4",
    garment: "",
    status: "Proses",
  },
  {
    waktu: "16 July 2024 05:21:12",
    layanan: "Reguler",
    total: "Rp158.000,00",
    pelanggan: "Mutia Cinthia Prastuti",
    pegawai: "Pegawai Laundry 1",
    garment: "",
    status: "Pending",
  },
]);

const filteredTransactions = computed(() => {
  return transactions.value.filter(
    (trx) =>
      trx.pelanggan.toLowerCase().includes(search.value.toLowerCase()) ||
      trx.pegawai.toLowerCase().includes(search.value.toLowerCase()) ||
      trx.layanan.toLowerCase().includes(search.value.toLowerCase())
  );
});
</script>

<style scoped>
.card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
.table thead {
  background: #f0f8ff;
}
</style>
