<template>
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
</script>
