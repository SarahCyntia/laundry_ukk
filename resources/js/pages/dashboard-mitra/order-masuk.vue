<template>
  <div class="order-masuk">
    <h2>Order Masuk</h2>

    <!-- Jika tidak ada order -->
    <div v-if="order.length === 0" class="empty">
      Tidak ada order masuk.
    </div>

    <!-- List order -->
    <div class="card" v-for="o in order" :key="o.id">
      <div class="row">
        <div>
          <h3>{{ o.kode_order }}</h3>
          <p>Pelanggan: {{ o.user.pelanggan.nama }}</p>
          <p>Berat Estimasi: {{ o.berat_estimasi }} kg</p>
          <p>Catatan: {{ o.catatan || '-' }}</p>
        </div>

        <div class="actions">
          <button class="btn-accept" @click="terimaOrder(o.id)">
            Terima
          </button>

          <button class="btn-reject" @click="openReject(o.id)">
            Tolak
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Penolakan -->
    <div class="modal" v-if="showRejectModal">
      <div class="modal-content">
        <h3>Alasan Penolakan</h3>

        <textarea v-model="alasan" placeholder="Tulis alasan penolakan..."></textarea>

        <div class="modal-actions">
          <button @click="showRejectModal = false">Batal</button>
          <button class="btn-reject" @click="submitTolak">Kirim</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import ApiService from "@/core/services/ApiService";
import { toast } from "vue3-toastify";
import axios from "@/libs/axios";

// Data
const order = ref([]);
const showRejectModal = ref(false);
const alasan = ref("");
const selectedOrderId = ref(null);

// Fetch order masuk
const loadOrders = async () => {
  const res = await axios.post("/order-masuk");
  order.value = res.data;
};

// Terima order
const terimaOrder = async (id) => {
  try {
    await axios.post(`/order/${id}/accept`);
    toast.success("Order diterima!");
    loadOrders();
  } catch (e) {
    toast.error("Gagal menerima order!");
  }
};

// Buka modal tolak
const openReject = (id) => {
  selectedOrderId.value = id;
  showRejectModal.value = true;
};

// Submit penolakan
const submitTolak = async () => {
  if (!alasan.value) {
    toast.warning("Alasan wajib diisi.");
    return;
  }

  try {
    await axios.post(`/order/${selectedOrderId.value}/reject`, {
      alasan: alasan.value,
    });

    toast.success("Order ditolak.");
    showRejectModal.value = false;
    alasan.value = "";
    loadOrders();
  } catch (e) {
    toast.error("Gagal menolak order.");
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
  flex-direction: column;
  gap: 8px;
}

.btn-accept {
  background: #2ecc71;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
}

.btn-reject {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
}

/* Modal */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  background: white;
  width: 330px;
  padding: 20px;
  border-radius: 10px;
}

textarea {
  width: 100%;
  height: 80px;
  padding: 8px;
  margin-top: 10px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 10px;
  gap: 10px;
}

.empty {
  padding: 20px;
  text-align: center;
  color: #777;
}
</style>
