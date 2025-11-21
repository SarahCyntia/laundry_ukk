<template>
  <div class="dashboard-mitra">
    <h2>Dashboard Mitra</h2>

    <div class="cards">
      <div class="card">
        <h3>Order Masuk</h3>
        <p>{{ summary.menunggu_konfirmasi }}</p>
      </div>

      <div class="card">
        <h3>Diproses</h3>
        <p>{{ summary.diproses }}</p>
      </div>

      <div class="card">
        <h3>Siap Diambil</h3>
        <p>{{ summary.siap_diambil }}</p>
      </div>

      <div class="card">
        <h3>Selesai Hari Ini</h3>
        <p>{{ summary.selesai }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const notif = ref(false);
const jumlahOrderBaru = ref(0);

// const checkNotif = async () => {
//   const res = await axios.get("/mitra/notif-order-count");
//   jumlahOrderBaru.value = res.data.count;
// };

const checkNotif = async () => {
  const res = await axios.get("/mitra/notif-order");
  if (res.data.new_order && !notif.value) {
    notif.value = true;
    toast.info("📢 Ada order baru masuk!", { autoClose: 3000 });
  }
};

onMounted(() => {
  setInterval(checkNotif, 20000); // 20 detik
});


const summary = ref({
  menunggu_konfirmasi: 0,
  diproses: 0,
  siap_diambil: 0,
  selesai: 0,
});

const loadSummary = async () => {
  try {
    const res = await axios.get("/mitra/summary");

    // supaya aman kalau backend tidak lengkap
    summary.value = {
      menunggu_konfirmasi: res.data.menunggu_konfirmasi ?? 0,
      diproses: res.data.diproses ?? 0,
      siap_diambil: res.data.siap_diambil ?? 0,
      selesai: res.data.selesai ?? 0,
    };
  } catch (e) {
    console.error("Gagal load summary", e);
  }
};






onMounted(() => {
  loadSummary();
});
</script>

<style scoped>
.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem;
  margin-top: 20px;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0px 3px 10px rgba(0,0,0,0.12);
  text-align: center;
}

.card h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.card p {
  margin-top: 8px;
  font-size: 28px;
  font-weight: bold;
}
</style>
