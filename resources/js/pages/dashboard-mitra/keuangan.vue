<template>
  <div class="keuangan">
    <h2>Keuangan Mitra</h2>

    <div class="summary">
      <div class="box">
        <p>Total Pendapatan</p>
        <h3>Rp {{ format(pendapatan) }}</h3>
      </div>
      <div class="box">
        <p>Total Pencairan</p>
        <h3>Rp {{ format(pencairan) }}</h3>
      </div>
      <div class="box saldo">
        <p>Saldo Saat Ini</p>
        <h3>Rp {{ format(saldo) }}</h3>
      </div>
    </div>

    <div class="withdraw">
      <input v-model="jumlah" type="number" placeholder="Masukkan jumlah pencairan" />
      <button @click="ajukanPencairan">Ajukan Pencairan</button>
    </div>

    <h3>Riwayat Pencairan</h3>

    <div v-if="riwayat.length === 0" class="empty">Belum ada pencairan.</div>

    <table v-else>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Jumlah</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in riwayat" :key="r.id">
          <td>{{ r.created_at }}</td>
          <td>Rp {{ format(r.jumlah) }}</td>
          <td>{{ r.status }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ApiService from "@/core/services/ApiService";
import { toast } from "vue3-toastify";
import axios from "axios";

const pendapatan = ref(0);
const pencairan = ref(0);
const saldo = ref(0);

const riwayat = ref([]);
const jumlah = ref("");

const loadData = async () => {
  const r = await axios.get("/mitra/keuangan");
  pendapatan.value = r.data.total_pendapatan;
  pencairan.value = r.data.total_pencairan;
  saldo.value = r.data.saldo;

  const rw = await axios.get("/mitra/keuangan/riwayat");
  riwayat.value = rw.data;
};

const ajukanPencairan = async () => {
  if (!jumlah.value) {
    toast.error("Masukkan jumlah dulu");
    return;
  }

  try {
    await axios.post("/mitra/keuangan/ajukan", {
      jumlah: jumlah.value,
    });

    toast.success("Pencairan diajukan!");
    jumlah.value = "";
    loadData();
  } catch (e) {
    toast.error(e.response.data.message);
  }
};

const format = (num) => new Intl.NumberFormat("id-ID").format(num);

onMounted(() => loadData());
</script>

<style scoped>
.summary {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}
.box {
  flex: 1;
  background: #fff;
  padding: 15px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
.saldo {
  background: #d6f5d6;
}
.withdraw {
  display: flex;
  gap: 10px;
  margin-bottom: 25px;
}
.withdraw input {
  flex: 1;
  padding: 8px;
}
.withdraw button {
  background: #3498db;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 6px;
  cursor: pointer;
}
.empty {
  color: #777;
  padding: 10px 0;
}
table {
  width: 100%;
  border-collapse: collapse;
}
table th, table td {
  padding: 10px;
  border-bottom: 1px solid #eee;
}
</style>
