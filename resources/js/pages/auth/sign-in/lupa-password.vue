<template>
  <VForm class="form w-100 max-w-400px mx-auto" @submit="submit" :validation-schema="schema">
    <!-- Logo -->
    <div class="text-center mb-10">
      <router-link to="/">
        <img :src="setting?.logo" :alt="setting?.app" class="h-70px mb-5" />
      </router-link>

      <h1 class="text-dark fw-bold mb-2">Lupa Password?</h1>
      <div class="text-gray-500 fs-6">
        Kami akan mengirimkan link reset ke email Anda
      </div>
    </div>

    <!-- Email -->
    <div class="fv-row mb-10">
      <div class="position-relative">
        <i
          class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-400"
        ></i>

        <Field
          type="email"
          name="email"
          v-model="form.email"
          class="form-control form-control-lg form-control-solid ps-12"
          placeholder="Masukkan email"
          autocomplete="off"
        />
      </div>

      <ErrorMessage name="email" class="text-danger fs-7 mt-1 d-block" />
    </div>

    <!-- Submit -->
    <button type="submit" class="btn btn-primary w-100 mb-5">
      Kirim Link Reset
    </button>

    <!-- Back -->
    <div class="text-center">
      <router-link to="/sign-in" class="link-primary fw-semibold">
        ← Kembali ke Login
      </router-link>
    </div>
  </VForm>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { Field, ErrorMessage, Form as VForm } from "vee-validate";
import * as Yup from "yup";
import axios from "@/libs/axios";
import Swal from "sweetalert2";
import { useSetting } from "@/services";

const setting = useSetting();

const form = ref({
  email: "",
});

const schema = Yup.object().shape({
  email: Yup.string()
    .email("Email tidak valid")
    .required("Email wajib diisi"),
});

const submit = async () => {
  try {
    const res = await axios.post("/forgot-password", {
      email: form.value.email,
    });

    await Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: res.data.message || "Link reset password telah dikirim",
    });

    form.value.email = "";
  } catch (err: any) {
    Swal.fire({
      icon: "error",
      title: "Gagal!",
      text: err.response?.data?.message || "Terjadi kesalahan",
    });
  }
};
</script>
