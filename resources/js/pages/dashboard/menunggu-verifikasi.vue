<template>
  <div class="flex flex-col items-center justify-center h-screen text-center pt-20">
    <h1 class="text-2xl font-bold mb-4">Menunggu Verifikasi</h1>
    <p class="text-gray-600 mb-6">
      Akun kamu sedang dalam proses verifikasi oleh admin.<br />
      Kamu akan diarahkan ke halaman login setelah akun diterima.
    </p>
    <p class="text-sm text-gray-400">
      Halaman ini akan otomatis memeriksa status akun kamu...
    </p>
  </div>
</template>



<script setup lang="ts">
import axios from "axios";
import { onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

const router = useRouter();

// Ambil ID mitra yang baru daftar dari localStorage
const userId = localStorage.getItem("mitra_id");

// Simpan ID interval di scope luar supaya bisa diakses di onUnmounted
let intervalId: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
  if (!userId) return; // pastikan ada ID

  intervalId = setInterval(async () => {
    try {
      const res = await axios.get(`/cek-status-mitra/${userId}`);
      const status = res.data.status_validasi;

      if (status === "diterima") {
        clearInterval(intervalId!);
        Swal.fire({
          icon: "success",
          title: "Akun Diterima!",
          text: "Akun kamu sudah diverifikasi, silakan login.",
          confirmButtonText: "OK",
        }).then(() => {
          localStorage.removeItem("mitra_id");
          router.push({ name: "sign-in" });
        });
      } else if (status === "ditolak") {
        clearInterval(intervalId!);
        Swal.fire({
          icon: "error",
          title: "Akun Ditolak",
          text: "Akun kamu tidak memenuhi syarat pendaftaran mitra. Silakan daftar ulang.",
          confirmButtonText: "Daftar Ulang",
        }).then(() => {
          localStorage.removeItem("mitra_id");
          router.push({ name: "sign-up" });
        });
      }
    } catch (err) {
      console.error("Gagal cek status mitra:", err);
    }
  }, 5000); // cek setiap 5 detik
});

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>



<!-- <script setup lang="ts">
import axios from "axios";
import { onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

// asumsi user_id disimpan di localStorage setelah register
const userId = localStorage.getItem("pending_mitra_id");

onMounted(() => {
  const interval = setInterval(async () => {
    try {
      const res = await axios.get(`/mitra/status/${userId}`);
      if (res.data.status_validasi === "diterima") {
        clearInterval(interval);
        alert("Akun kamu sudah diverifikasi! Silakan login.");
        router.push({ name: "sign-in" });
      }
    } catch (err) {
      console.error(err);
    }
  }, 5000); // cek setiap 5 detik
});
</script> -->




<!-- <template>
  <div class="waiting-page">
    <h2>⏳ Pendaftaran Sedang Diperiksa</h2>
    <p>Mohon tunggu, admin sedang memverifikasi akun mitra Anda...</p>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";

const router = useRouter();
const email = localStorage.getItem("email_mitra"); // simpan email waktu daftar

const checkStatus = async () => {
  try {
    const { data } = await axios.get(`/cek-status-mitra/${email}`);

    if (data.status_validasi === "diterima") {
      Swal.fire({
        title: "Selamat!",
        text: "Status validasi Anda telah diterima oleh admin.",
        icon: "success",
        confirmButtonText: "Login Sekarang",
      }).then(() => router.push("/sign-in"));
    } else if (data.status_validasi === "ditolak") {
      Swal.fire({
        title: "Pendaftaran Ditolak",
        text: "Maaf, pendaftaran Anda ditolak. Silakan daftar ulang.",
        icon: "error",
        confirmButtonText: "Daftar Ulang",
      }).then(() => router.push("/register-mitra"));
    }

  } catch (err) {
    console.error(err);
  }
};

// cek status tiap 10 detik
onMounted(() => {
  checkStatus();
  setInterval(checkStatus, 10000);
});
</script> -->
