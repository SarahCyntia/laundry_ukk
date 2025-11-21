<template>
  <div class="order-history">
    <h2>Riwayat Order Selesai</h2>

    <!-- Jika tidak ada riwayat -->
    <div v-if="order.length === 0" class="empty">
      Belum ada order selesai.
    </div>

    <!-- List order -->
    <div class="card" v-for="o in order" :key="o.id">
      <div class="row">
        <div>
          <h3>{{ o.kode_order }}</h3>
          <p>Customer: {{ o.customer_name }}</p>
          <p>Berat: {{ o.berat_estimasi }} kg</p>
          <p>Total: Rp {{ formatRupiah(o.total_harga) }}</p>
          <p>Selesai: {{ o.tanggal_selesai }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ApiService from "@/core/services/ApiService";
import { toast } from "vue3-toastify";
import axios from "axios";

const order = ref([]);

// Load riwayat order selesai
const loadOrders = async () => {
  const res = await axios.get("/order/selesai");
  order.value = res.data;
};

// Format angka ke Rupiah
const formatRupiah = (value) => {
  if (!value) return "0";
  return new Intl.NumberFormat("id-ID").format(value);
};

onMounted(() => {
  loadOrders();
});
</script>

<style scoped>
.card {
  background: white;
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 12px;
  box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.1);
}

.row {
  display: flex;
  justify-content: space-between;
}

.empty {
  padding: 20px;
  text-align: center;
  color: #777;
}
</style>
