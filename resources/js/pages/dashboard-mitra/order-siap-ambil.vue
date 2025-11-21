<template>
  <div class="order-ready">
    <h2>Order Siap Diambil</h2>

    <!-- Jika tidak ada data -->
    <div v-if="order.length === 0" class="empty">
      Tidak ada order siap diambil.
    </div>

    <!-- List order -->
    <div class="card" v-for="o in order" :key="o.id">
      <div class="row">
        <div>
          <h3>{{ o.kode_order }}</h3>
          <p>Customer: {{ o.customer_name }}</p>
          <p>Berat: {{ o.berat_estimasi }} kg</p>
          <p>Status: <strong>Siap Diambil</strong></p>
        </div>

        <div class="actions">
          <button 
            class="btn-complete"
            @click="selesaiOrder(o.id)"
          >
            Sudah Diambil ✔
          </button>
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

// Load data
const loadOrders = async () => {
  const res = await axios.get("/order/siap-diambil");
  order.value = res.data;
};

// Tandai order selesai
const selesaiOrder = async (id) => {
  try {
    await axios.post(`/order/${id}/selesai`);
    toast.success("Order ditandai selesai!");
    loadOrders();
  } catch (e) {
    toast.error("Gagal menyelesaikan order.");
  }
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
  align-items: center;
}

.actions {
  display: flex;
  align-items: center;
}

.btn-complete {
  background: #2ecc71;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
}

.empty {
  padding: 20px;
  text-align: center;
  color: #777;
}
</style>
