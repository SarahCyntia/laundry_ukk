<template>
  <form class="register-form" @submit.prevent="handleRegister">
    <!-- NAMA -->
    <div class="form-group">
      <label for="nama">Nama Lengkap</label>
      <input
        id="nama"
        v-model="form.nama"
        type="text"
        placeholder="Masukkan nama lengkap"
        required
      />
    </div>

    <!-- EMAIL -->
    <div class="form-group">
      <label for="email">Email</label>
      <input
        id="email"
        v-model="form.email"
        type="email"
        placeholder="Masukkan email aktif"
        required
      />
    </div>

    <!-- PASSWORD -->
    <div class="form-group">
      <label for="password">Kata Sandi</label>
      <input
        id="password"
        v-model="form.password"
        type="password"
        placeholder="Minimal 6 karakter"
        required
      />
    </div>

    <!-- KONFIRMASI PASSWORD -->
    <div class="form-group">
      <label for="password_confirmation">Konfirmasi Kata Sandi</label>
      <input
        id="password_confirmation"
        v-model="form.password_confirmation"
        type="password"
        placeholder="Ulangi kata sandi"
        required
      />
    </div>

    <!-- SUBMIT -->
    <button type="submit" class="btn-submit" :disabled="loading">
      <span v-if="loading" class="spinner"></span>
      <span v-else>Daftar</span>
    </button>
  </form>
</template>

<script setup lang="ts">
import { ref } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const loading = ref(false);

const form = ref({
  nama: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const handleRegister = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    Swal.fire("Error", "Kata sandi tidak cocok!", "error");
    return;
  }

  try {
    loading.value = true;
    const res = await axios.post("/api/register", form.value);
    Swal.fire("Berhasil!", "Akun Anda berhasil dibuat!", "success");
    console.log(res.data);
  } catch (err: any) {
    Swal.fire("Gagal", err.response?.data?.message || "Terjadi kesalahan", "error");
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.register-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}

input {
  padding: 10px 14px;
  border: 1px solid #d1d5db;
  border-radius: 10px;
  font-size: 15px;
  outline: none;
  transition: all 0.2s ease;
}

input:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
}

.btn-submit {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  font-weight: 600;
  border: none;
  padding: 12px;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-submit:hover {
  opacity: 0.9;
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinner {
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  animation: spin 1s linear infinite;
  display: inline-block;
  vertical-align: middle;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
