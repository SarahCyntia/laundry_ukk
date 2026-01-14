<template>
  <VForm class="form w-100" @submit="submit" :validation-schema="schema">
    <!-- Heading -->
    <div class="text-center mb-10">
      <h1 class="text-dark mb-3">Reset Password</h1>
      <div class="text-gray-500 fw-semibold fs-6">
        Masukkan password baru untuk akun Anda
      </div>
    </div>

    <!-- Password Baru -->
    <div class="fv-row mb-10 position-relative">
      <Field
        :type="showPassword ? 'text' : 'password'"
        name="password"
        v-model="form.password"
        class="form-control form-control-lg form-control-solid pe-10"
        placeholder="Password baru"
      />
      <!-- tombol toggle -->
      <span
        class="btn btn-sm btn-icon position-absolute top-50 end-0 translate-middle-y me-3"
        style="cursor: pointer"
        @click="showPassword = !showPassword"
      >
        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
      </span>

      <div class="fv-plugins-message-container">
        <div class="fv-help-block">
          <ErrorMessage name="password" />
        </div>
      </div>
    </div>

    <!-- Konfirmasi Password -->
    <div class="fv-row mb-10 position-relative">
      <Field
        :type="showPasswordConfirm ? 'text' : 'password'"
        name="password_confirmation"
        v-model="form.password_confirmation"
        class="form-control form-control-lg form-control-solid pe-10"
        placeholder="Konfirmasi password"
      />
      <!-- tombol toggle -->
      <span
        class="btn btn-sm btn-icon position-absolute top-50 end-0 translate-middle-y me-3"
        style="cursor: pointer"
        @click="showPasswordConfirm = !showPasswordConfirm"
      >
        <i :class="showPasswordConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
      </span>

      <div class="fv-plugins-message-container">
        <div class="fv-help-block">
          <ErrorMessage name="password_confirmation" />
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="d-flex flex-wrap justify-content-center pb-lg-0">
      <button
        type="submit"
        ref="submitButton"
        class="btn btn-primary w-100 mb-5"
        :disabled="loading"
      >
        <span v-if="!loading" class="indicator-label">Reset Password</span>
        <span v-else class="indicator-progress">
          <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
          Proses...
        </span>
      </button>
    </div>

    <!-- Back to Login -->
    <div class="text-center">
      <router-link to="/sign-in" class="link-primary fw-bold">
        Kembali ke Login
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
    .min(8, "Minimal 8 karakter")
    .matches(/^[A-Z]/, "Password harus diawali huruf besar")
    .matches(/[0-9]/, "Password harus mengandung angka")
    .matches(/[!@#$%^&*(),.?\":{}|<>]/, "Password harus mengandung simbol"),
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