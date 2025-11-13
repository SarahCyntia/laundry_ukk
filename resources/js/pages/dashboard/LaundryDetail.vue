<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <button
      class="mb-4 text-blue-600 hover:underline"
      @click="router.back()"
    >
      ← Kembali
    </button>

    <div v-if="loading" class="text-center text-gray-500">Memuat data...</div>

    <div v-else>
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-6">
          <img
            :src="laundry.foto || '/default-laundry.jpg'"
            alt="Laundry"
            class="w-full md:w-60 h-40 object-cover rounded-lg"
          />

          <div>
            <h1 class="text-2xl font-bold">{{ laundry.nama_laundry }}</h1>
            <p class="text-gray-600 mt-1">{{ laundry.alamat }}</p>
            <p class="text-gray-500 mt-1">☎️ {{ laundry.no_hp }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">🧺 Daftar Layanan</h2>

        <div v-if="layanan.length === 0" class="text-gray-500">
          Belum ada layanan yang tersedia.
        </div>

        <div v-else class="grid sm:grid-cols-2 gap-4">
          <div
            v-for="item in layanan"
            :key="item.id"
            class="border rounded-lg p-4 hover:bg-gray-100 cursor-pointer transition"
          >
            <h3 class="font-semibold text-lg">{{ item.nama_layanan }}</h3>
            <p class="text-gray-600">{{ item.deskripsi }}</p>
            <p class="text-blue-600 font-semibold mt-2">
              Rp {{ formatHarga(item.harga) }} / {{ item.satuan }}
            </p>
          </div>
        </div>

        <div class="text-center mt-8">
          <button
            @click="buatPesanan"
            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
          >
            Pesan Sekarang
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const laundry = ref({});
const layanan = ref([]);
const loading = ref(true);

const getDetailLaundry = async () => {
  try {
    const id = route.params.id;
    const res = await axios.get(`/api/mitra/${id}`);
    laundry.value = res.data.laundry;
    layanan.value = res.data.layanan;
  } catch (err) {
    console.error("Gagal ambil detail laundry:", err);
  } finally {
    loading.value = false;
  }
};

const formatHarga = (harga) => {
  return new Intl.NumberFormat("id-ID").format(harga);
};

const buatPesanan = () => {
  router.push({ name: "order.form", params: { id: laundry.value.id } });
};

onMounted(() => {
  getDetailLaundry();
});
</script>
