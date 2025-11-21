<template>
  <div class="detail-container">
    <button class="back-btn" @click="$router.back()">← Kembali</button>

    <div v-if="loading" class="loading">Memuat...</div>

    <div v-else class="detail-card">
      <h2>{{ laundry.nama_laundry }}</h2>

      <p>📍 {{ laundry.alamat_laundry }}</p>
      <p>⭐ Rating: {{ laundry.rating ?? '4.8' }}</p>

      <div class="price-box">
        <p>Harga per kilo</p>
        <h3>Rp {{ laundry.harga_per_kilo ?? '5000' }}</h3>
      </div>

      <hr />

      <!-- Form Order -->
      <h3>Masukkan Pesanan</h3>

      <div class="form-group">
        <label>Berat Estimasi (kg)</label>
        <input
          type="number"
          v-model="form.berat_estimasi"
          placeholder="contoh: 3"
        />
      </div>

      <div class="form-group">
        <label>Catatan (opsional)</label>
        <textarea
          v-model="form.catatan"
          placeholder="contoh: tolong pisahkan baju putih"
        ></textarea>
      </div>

      <button class="btn-order" @click="buatOrder" :disabled="loadingSubmit">
        <span v-if="loadingSubmit">Mengirim...</span>
        <span v-else>Buat Pesanan</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import { toast } from "vue3-toastify";

const route = useRoute();
const router = useRouter();

const id = route.params.id; // ID laundry / mitra

const laundry = ref({});
const loading = ref(true);
const loadingSubmit = ref(false);

const form = ref({
  berat_estimasi: "",
  catatan: "",
});

// Ambil detail laundry
const loadDetail = async () => {
  try {
    const res = await axios.get(`/api/pelanggan/laundry/${id}`);
    laundry.value = res.data;
  } catch (err) {
    toast.error("Gagal memuat detail laundry");
  } finally {
    loading.value = false;
  }
};

// Buat order baru
const buatOrder = async () => {
  if (!form.value.berat_estimasi) {
    return toast.error("Berat estimasi wajib diisi!");
  }

  try {
    loadingSubmit.value = true;

    await axios.post("/api/pelanggan/order", {
      mitra_id: id,
      berat_estimasi: form.value.berat_estimasi,
      catatan: form.value.catatan,
    });

    toast.success("Order berhasil dibuat!");
    router.push("/pelanggan/order-saya");
  } catch (err) {
    toast.error("Gagal membuat order!");
  } finally {
    loadingSubmit.value = false;
  }
};

onMounted(() => {
  loadDetail();
});
</script>

<style scoped>
.detail-container {
  padding: 20px;
}

.back-btn {
  background: none;
  border: none;
  color: #3498db;
  font-size: 16px;
  cursor: pointer;
  margin-bottom: 10px;
}

.detail-card {
  background: white;
  padding: 18px;
  border-radius: 12px;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
}

.price-box {
  background: #f3f9ff;
  padding: 12px;
  border-radius: 8px;
  margin: 12px 0;
}

.form-group {
  margin-bottom: 15px;
}

input,
textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.btn-order {
  width: 100%;
  background: #3498db;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
</style>
