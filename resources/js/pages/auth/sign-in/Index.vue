<template>
  <div class="login-wrapper">
    <div class="login-card">
      <!-- Form -->
      <div class="w-100">
        <!-- Header -->
        <div class="text-center mb-10">
          <router-link to="/">
            <img src="/storage/image/pelanggan.png" class="orang" />
          </router-link>
          <h1 class="abc mb-3">
            Masuk ke <span class="text-primary">SLaundry</span>
          </h1>
        </div>

        <!-- Tab -->
        <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
          <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#with-email">Email</a>
          </li>
        </ul>

        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="with-email" role="tabpanel">
            <WithEmail />
          </div>
        </div>

        <div class="border-bottom border-gray-300 w-100 mt-5 mb-10"></div>

        <div class="text-center daftar">
          Belum punya akun?
          <router-link to="/sign-up" class="link-daftar">Daftar</router-link>
        </div>
        <div class="text-center daftar mt-5 ">
          Daftar sebagai mitra?
          <router-link to="/register-mitra" class="link-daftar">Daftar Mitra</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useRouter } from "vue-router";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { blockBtn, unblockBtn } from "@/libs/utils";
import { useAuthStore } from "@/stores/auth";
import WithEmail from "./tabs/WithEmail.vue";
import * as Yup from "yup";

export default defineComponent({
  name: "sign-in",
  components: { WithEmail },

  setup() {
    const store = useAuthStore();
    const router = useRouter();
    const submitButton = ref(null);

    const loginSchema = Yup.object().shape({
      identifier: Yup.string().required("Masukkan email atau nomor telepon"),
      password: Yup.string().min(8, "Password minimal 8 karakter").required("Masukkan password"),
    });

    return { store, router, submitButton, loginSchema };
  },

  data() {
    return {
      data: {
        identifier: "",
        password: "",
      },
      check: {
        type: "",
        error: "",
      },
    };
  },

  methods: {
    async submit() {
      try {
        blockBtn(this.submitButton);

        // tentukan jenis login (email / phone)
        this.checkInput(this.data.identifier);

        const res = await axios.post("/auth/login", {
          ...this.data,
          type: this.check.type,
        });

        const { user, token } = res.data;

        // simpan user + token di store
        this.store.setAuth(user, token);

        // 🔹 Redirect sesuai role
        switch (user.role) {
          case "mitra":
            this.router.push("/mitra/beranda");
            break;
          case "admin":
            this.router.push("/admin/dashboard");
            break;
          default:
            this.router.push("/pengguna/beranda");
        }

        toast.success("Login berhasil!");
      } catch (error) {
        const msg = error.response?.data?.message || "Login gagal, periksa kembali data Anda.";
        toast.error(msg);
      } finally {
        unblockBtn(this.submitButton);
      }
    },

    checkInput(value) {
      this.check.type = "";
      if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value)) {
        this.check.type = "email";
      } else if (!isNaN(value)) {
        this.check.type = "phone";
      } else {
        this.check.type = "invalid";
      }
    },
  },
});
</script>

<style scoped>
.login-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #466ee7;
}

.login-card {
  width: 500px;
  background: #ffffff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.login-card img.orang {
  width: 150px;
  margin-bottom: 15px;
}

.abc {
  font-weight: 600;
  font-size: 28px;
  color: black;
}

.nav-link {
  font-weight: 500;
  font-size: 16px;
  color: black;
}
</style>
