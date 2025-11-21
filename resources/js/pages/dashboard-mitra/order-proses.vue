<template>
  <div class="order-proses">
    <h2>Order Sedang Diproses</h2>

    <!-- Jika tidak ada order -->
    <div v-if="order.length === 0" class="empty">
      Tidak ada order dalam proses.
    </div>

    <!-- List order -->
    <div class="card" v-for="o in order" :key="o.id">
      <div class="row">
        <div>
          <h3>{{ o.kode_order }}</h3>
          <p>Customer: {{ o.customer_name }}</p>
          <p>Status: <strong>{{ o.status }}</strong></p>
          <p>Berat Estimasi: {{ o.berat_estimasi }} kg</p>
        </div>

        <div class="actions">
          <button 
            class="btn-update" 
            :disabled="o.status === 'siap_diambil'"
            @click="updateStatus(o.id)"
          >
            Update Status →
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

// Fetch order dalam proses
const loadOrders = async () => {
  const res = await axios.get("/order/proses");
  order.value = res.data;
};

// Update status 1 level
const updateStatus = async (id) => {
  try {
    await axios.post(`/mitra/order/${id}/update-status`);
    toast.success("Status berhasil diperbarui!");
    loadOrders();
  } catch (e) {
    toast.error("Gagal update status!");
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

.btn-update {
  background: #3498db;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
}

.btn-update:disabled {
  background: #7f8c8d;
  cursor: not-allowed;
}

.empty {
  padding: 20px;
  text-align: center;
  color: #777;
}
</style>
