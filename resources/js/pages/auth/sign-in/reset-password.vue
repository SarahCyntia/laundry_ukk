<template>
  <VForm class="form w-100 max-w-400px mx-auto" @submit="submit" :validation-schema="schema">
    <!-- Heading -->
    <div class="text-center mb-10">
      <h1 class="text-dark fw-bold mb-3">Reset Password</h1>
      <div class="text-gray-500 fs-6">
        Buat password baru untuk akun Anda
      </div>
    </div>

    <!-- Password Baru -->
    <div class="fv-row mb-7">
      <div class="position-relative">
        <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-400"></i>

        <Field
          :type="showPassword ? 'text' : 'password'"
          name="password"
          v-model="form.password"
          class="form-control form-control-lg form-control-solid ps-12 pe-12"
          placeholder="Password baru"
        />

        <span
          class="position-absolute top-50 end-0 translate-middle-y me-4 text-gray-500"
          style="cursor: pointer"
          @click="showPassword = !showPassword"
        >
          <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
        </span>
      </div>

      <ErrorMessage name="password" class="text-danger fs-7 mt-1 d-block" />
    </div>

    <!-- Konfirmasi Password -->
    <div class="fv-row mb-10">
      <div class="position-relative">
        <i class="bi bi-lock-fill position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-400"></i>

        <Field
          :type="showPasswordConfirm ? 'text' : 'password'"
          name="password_confirmation"
          v-model="form.password_confirmation"
          class="form-control form-control-lg form-control-solid ps-12 pe-12"
          placeholder="Konfirmasi password"
        />

        <span
          class="position-absolute top-50 end-0 translate-middle-y me-4 text-gray-500"
          style="cursor: pointer"
          @click="showPasswordConfirm = !showPasswordConfirm"
        >
          <i :class="showPasswordConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
        </span>
      </div>

      <ErrorMessage name="password_confirmation" class="text-danger fs-7 mt-1 d-block" />
    </div>

    <!-- Submit -->
    <button
      type="submit"
      class="btn btn-primary w-100"
      :disabled="loading"
    >
      <span v-if="!loading">Reset Password</span>
      <span v-else>
        <span class="spinner-border spinner-border-sm me-2"></span>
        Proses...
      </span>
    </button>

    <!-- Back -->
    <div class="text-center mt-7">
      <router-link to="/sign-in" class="link-primary fw-semibold">
        ← Kembali ke Login
      </router-link>
    </div>
  </VForm>
</template>


<script setup lang="ts">
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { Field, ErrorMessage, Form as VForm } from "vee-validate";
import * as Yup from "yup";
import axios from "@/libs/axios";
import Swal from "sweetalert2";

const router = useRouter();
const route = useRoute();

const loading = ref(false);

// toggle show/hide password
const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const form = ref({
  password: "",
  password_confirmation: "",
});

// Ambil token & email dari query
const token = (route.query.token as string) || "";
const email = (route.query.email as string) || "";

const schema = Yup.object().shape({
  password: Yup.string()
    .required("Password wajib diisi")
    .min(8, "Minimal 8 karakter"),
    // .matches(/^[A-Z]/, "Password harus diawali huruf besar")
    // .matches(/[0-9]/, "Password harus mengandung angka")
    // .matches(/[!@#$%^&*(),.?\":{}|<>]/, "Password harus mengandung simbol"),
  password_confirmation: Yup.string()
    .oneOf([Yup.ref("password")], "Konfirmasi password tidak sama")
    .required("Konfirmasi password wajib diisi"),
});

const submit = async () => {
  loading.value = true;
  try {
    const res = await axios.post("/reset-password", {
      token,
      email,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
    });

    await Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: res.data.message || "Password berhasil direset",
      confirmButtonText: "Login",
    });

    router.push("/sign-in");
  } catch (err: any) {
    Swal.fire({
      icon: "error",
      title: "Gagal!",
      text: err.response?.data?.message || "Terjadi kesalahan saat reset password",
    });
  } finally {
    loading.value = false;
  }
};
</script>