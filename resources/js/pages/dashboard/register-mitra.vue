<template>
  <div class="register-container">
    <h2>Pendaftaran Mitra Laundry</h2>

    <form @submit.prevent="registerMitra" class="register-form" enctype="multipart/form-data">
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input v-model="form.name" type="text" placeholder="Masukkan nama lengkap" required />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input v-model="form.email" type="email" placeholder="Masukkan email" required />
      </div>

      <div class="form-group">
        <label>No. HP</label>
        <input v-model="form.phone" type="text" placeholder="Masukkan nomor HP" required />
      </div>

      <div class="form-group">
        <label>Nama Laundry</label>
        <input v-model="form.nama_laundry" type="text" placeholder="Masukkan nama laundry" required />
      </div>

      <div class="form-group">
        <label>Alamat Laundry</label>
        <textarea v-model="form.alamat_laundry" rows="2" placeholder="Masukkan alamat laundry" required></textarea>
      </div>

      <!-- 🔹 Upload Foto KTP -->
      <div class="form-group">
        <label>Foto KTP</label>
        <input type="file" accept="image/*" @change="onFileChange" required />
        <small class="text-muted">Format: JPG, PNG. Maks 2MB.</small>
      </div>

      <div v-if="previewKTP" class="preview">
        <img :src="previewKTP" alt="Preview KTP" />
      </div>

      <div class="form-group">
        <label>Password</label>
        <input v-model="form.password" type="password" placeholder="Masukkan password" required />
      </div>

      <div class="form-group">
        <label>Konfirmasi Password</label>
        <input v-model="form.password_confirmation" type="password" placeholder="Ulangi password" required />
      </div>

      <button type="submit" class="btn-primary" :disabled="loading">
        {{ loading ? 'Mengirim...' : 'Daftar Mitra' }}
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const form = ref({
  name: "",
  email: "",
  phone: "",
  nama_laundry: "",
  alamat_laundry: "",
  password: "",
  password_confirmation: "",
  foto_ktp: null as File | null,
});

const previewKTP = ref<string | null>(null);
const loading = ref(false);

// 📸 handle perubahan file KTP
const onFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    const file = target.files[0];
    form.value.foto_ktp = file;
    previewKTP.value = URL.createObjectURL(file); // tampilkan preview
  }
};

// 📩 fungsi kirim data pendaftaran
const registerMitra = async () => {
  loading.value = true;

  try {
    // Pakai FormData biar file bisa terkirim
    const payload = new FormData();
    payload.append("name", form.value.name);
    payload.append("email", form.value.email);
    payload.append("phone", form.value.phone);
    payload.append("nama_laundry", form.value.nama_laundry);
    payload.append("alamat_laundry", form.value.alamat_laundry);
    payload.append("password", form.value.password);
    payload.append("password_confirmation", form.value.password_confirmation);
    payload.append("role", "mitra");
    payload.append("status_validasi", "menunggu");

    if (form.value.foto_ktp) {
      payload.append("foto_ktp", form.value.foto_ktp);
    }

    await axios.post("/register-mitra", payload, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    Swal.fire({
      title: "Pendaftaran Dikirim!",
      text: "Data kamu sedang diperiksa oleh admin. Harap menunggu konfirmasi.",
      icon: "info",
      confirmButtonText: "OK",
    });

    // reset form dan preview
    Object.keys(form.value).forEach(
      (key) => (form.value[key as keyof typeof form.value] = "")
    );
    previewKTP.value = null;
  } catch (error: any) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat();
      errors.forEach((msg: any) => Swal.fire("Gagal", msg, "error"));
    } else {
      Swal.fire("Error", "Terjadi kesalahan saat pendaftaran.", "error");
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.register-container {
  max-width: 480px;
  margin: 50px auto;
  background: #fff;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.register-container h2 {
  text-align: center;
  margin-bottom: 1rem;
  color: #333;
}
.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}
.form-group label {
  font-weight: 600;
  margin-bottom: 0.3rem;
}
.form-group input,
.form-group textarea {
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 0.6rem;
  font-size: 0.95rem;
}
.btn-primary {
  background: #4f46e5;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.8rem;
  width: 100%;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-primary:hover {
  background: #4338ca;
}
.preview {
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
}
.preview img {
  width: 120px;
  height: auto;
  border-radius: 8px;
  border: 2px solid #ccc;
}
.text-muted {
  color: #777;
  font-size: 0.85rem;
}
</style>
