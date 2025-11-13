<template>
  <VForm class="form w-100" @submit="submit" :validation-schema="login">
    <!--begin::Input group-->
    <div class="fv-row mb-10">
      <!--begin::Label-->
      <label class="form-label fs-6 fw-bold">Email</label>
      <!--end::Label-->

      <!--begin::Input-->
      <Field tabindex="1" class="form-control form-control-lg form-control-solid" type="text" name="email"
        autocomplete="off" v-model="data.email" />
      <!--end::Input-->
      <div class="fv-plugins-message-container">
        <div class="fv-help-block">
          <ErrorMessage name="email" />
        </div>
      </div>
    </div>
    <!--end::Input group-->

    <!--begin::Input group-->
    <div class="fv-row mb-5">
      <!--begin::Wrapper-->
      <div class="d-flex flex-stack mb-2">
        <!--begin::Label-->
        <label class="form-label fw-bold fs-6 mb-0">Password</label>
        <!--end::Label-->

        <!--begin::Link-->
        <!-- <router-link to="/password-reset" class="link-primary fs-6 fw-bold">
              Forgot Password ?
            </router-link> -->
        <!--end::Link-->
      </div>
      <!--end::Wrapper-->

      <!--begin::Input-->
      <div class="position-relative mb-3">
        <!--begin::Input-->
        <Field tabindex="2" class="form-control form-control-lg form-control-solid" type="password" name="password"
          v-model="data.password" autocomplete="off" />
        <!--end::Input-->

        <!--begin::Visibility toggle-->
        <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
          <i class="bi bi-eye-slash fs-2" @click="togglePassword"></i>
        </span>
        <!--end::Visibility toggle-->
      </div>
      <!--end::Input-->
      <div class="fv-plugins-message-container">
        <div class="fv-help-block">
          <ErrorMessage name="password" />
        </div>
      </div>
    </div>
    <!--end::Input group-->

    <!-- <div class="form-check mb-10">
            <Field tabindex="3" class="form-check-input" type="checkbox" id="remember_me" name="remember_me" value="1"
                v-model="data.remember_me" />
            <label class="form-check-label" for="remember_me">
                {{ $t('login.remember') }}
            </label>
        </div> -->

    <!--begin::Actions-->
    <div class="text-center">
      <!--begin::Submit button-->
      <button tabindex="3" type="submit" ref="submitButton" class="btn btn-lg btn-primary w-100 mb-5">
        <span class="indicator-label">Login</span>

        <span class="indicator-progress">
          <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
      </button>
      <!--end::Submit button-->
    </div>
    <!--end::Actions-->
  </VForm>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { blockBtn, unblockBtn } from "@/libs/utils";
import Swal from "sweetalert2";

export default defineComponent({
  setup() {
    const store = useAuthStore();
    const router = useRouter();
    const submitButton = ref(null);

    const login = Yup.object().shape({
      email: Yup.string()
        .email("Email tidak valid")
        .required("Harap masukkan Email")
        .label("Email"),
      password: Yup.string()
        .min(8, "Password minimal terdiri dari 8 karakter")
        .required("Harap masukkan password")
        .label("Password"),
    });

    return { login, submitButton, getAssetPath, store, router };
  },

  data() {
    return {
      data: {
        email: "",
        password: "",
      },
    };
  },

  methods: {
    async submit() {
      console.log("Submit");
      blockBtn(this.submitButton);
      try {
        const res = await axios.post("/auth/login", {
          ...this.data,
        });

        // simpan user + token
        this.store.setAuth(res.data.user, res.data.token);

        // normalisasi role
        const roleName =
          typeof res.data.user.role === "string"
            ? res.data.user.role
            : res.data.user.role?.name;

        console.log("Role:", roleName);
        console.log("Role Type:", typeof roleName);

        if (roleName == "pelangan") {
          // ✅ Popup untuk pelanggan
          await Swal.fire({
            title: "Selamat Datang 👋",
            text: `Selamat datang di SLaundry, ${res.data.user.name || ""}!`,
            icon: "success",
            confirmButtonText: "OK",
            allowOutsideClick: false,
          });

          this.router.push("/beranda");
        } else if (roleName == "admin") {
          // ✅ Popup untuk admin
          await Swal.fire({
            title: "Halo Admin 👋",
            text: `Selamat datang kembali, ${res.data.user.name || ""}!`,
            icon: "info", // bisa diganti 'success' sesuai selera
            confirmButtonText: "OK",
            allowOutsideClick: false,
          });

          this.router.push("/dashboard");
        } else {
          // ✅ Role lain langsung ke dashboard tanpa popup
          this.router.push("/dashboard");
        }

        // if (roleName == "pelangan") {
        //     console.log("Pop")
        //   // ✅ tampilkan popup dan tunggu OK
        //   await Swal.fire({
        //     title: "Selamat Datang 👋",
        //     text: `Selamat datang di SLaundry, ${res.data.user.name || ""}!`,
        //     icon: "success",
        //     confirmButtonText: "OK",
        //     allowOutsideClick: false,
        //     didOpen: () => console.log("SweetAlert sudah terbuka"),
        //   });

        //   // pindah setelah popup ditutup
        //   this.router.push("/beranda");
        // } else {
        //   this.router.push("/dashboard");
        // }
      } catch (error: any) {
        toast.error(error.response?.data?.message || "Login gagal");
      } finally {
        unblockBtn(this.submitButton);
      }
    },

    togglePassword(ev: any) {
      const input = document.querySelector<HTMLInputElement>(
        "[name=password]"
      );
      if (!input) return;

      if (input.type === "password") {
        input.type = "text";
        ev.target.classList.add("bi-eye");
        ev.target.classList.remove("bi-eye-slash");
      } else {
        input.type = "password";
        ev.target.classList.remove("bi-eye");
        ev.target.classList.add("bi-eye-slash");
      }
    },
  },
});
</script>
