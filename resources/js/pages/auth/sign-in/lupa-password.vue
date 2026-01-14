<template>
  <VForm class="form w-100" @submit="submit" :validation-schema="schema">
    <!--begin::Heading-->
    <div class="text-center mb-10">
      <router-link to="/">
        <img :src="setting?.logo" :alt="setting?.app" class="w-200px mb-8" />
      </router-link>
      <!--begin::Title-->
      <h1 class="mb-3">
        <h1 class="text-dark mb-3">Lupa Password</h1>
      </h1>
      <!--end::Title-->
    </div>
    <!--begin::Heading-->
    <!-- Heading -->
    <div class="text-center mb-10">
      <!-- <h1 class="text-dark mb-3">Lupa Password</h1> -->
      <div class="text-gray-500 fw-semibold fs-6">
        Masukkan email Anda untuk mereset password
      </div>
    </div>

    <!-- Input Email -->
    <div class="fv-row mb-10 ">
      <div class="position-relative">
        <img src="/storage/photo/mail.png" alt="email icon"
          class="position-absolute top-50 start-0 translate-middle-y ms-3" style="width: 18px; height: 18px;" />

        <Field type="email" name="email" v-model="form.email"
          class="form-control form-control-lg form-control-solid ps-12" placeholder="Masukkan email"
          autocomplete="off" />
      </div>
      <!-- Icon -->

      <!-- Error -->
      <div class="fv-plugins-message-container">
        <div class="fv-help-block">
          <ErrorMessage name="email" />
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="d-flex flex-wrap justify-content-center pb-lg-0">
      <button type="submit" ref="submitButton" class="btn btn-primary w-100 mb-5">
        <span class="indicator-label">Kirim Link Reset</span>
        <span class="indicator-progress">
          <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
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
import { Field, ErrorMessage, Form as VForm } from "vee-validate";
import * as Yup from "yup";
import axios from "@/libs/axios";
import Swal from "sweetalert2";
import { useSetting } from "@/services";

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
      text: res.data.message || "Link reset password telah dikirim ke email Anda",
      confirmButtonText: "OK",
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