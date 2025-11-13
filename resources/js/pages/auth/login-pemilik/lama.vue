<template>
  <div class="login-wrapper">
    <div class="login-container">
      <!-- Left Side - Branding -->
      <div class="login-branding">
        <div class="branding-content">
          <div class="logo-section">
            <div class="logo-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
              </svg>
            </div>
            <h2 class="brand-name">SLaundry</h2>
          </div>
          <h3 class="brand-tagline">Kelola Bisnis Laundry Anda dengan Mudah</h3>
          <p class="brand-description">
            Sistem manajemen laundry yang membantu Anda mengelola pesanan, 
            pelanggan, dan keuangan secara efisien.
          </p>
          <div class="features">
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <span>Manajemen Pesanan Real-time</span>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <span>Laporan Keuangan Lengkap</span>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <span>Notifikasi Otomatis</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - Login Form -->
      <div class="login-form-section">
        <div class="form-wrapper">
          <div class="form-header">
            <router-link to="/" class="back-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
              </svg>
            </router-link>
            <h1 class="form-title">Selamat Datang Kembali</h1>
            <p class="form-subtitle">Masuk ke akun pemilik Anda</p>
          </div>

          <ul class="nav-tabs">
            <li class="nav-item">
              <a class="nav-link active" data-bs-toggle="tab" href="#with-email">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
                Login dengan Email
              </a>
            </li>
          </ul>

          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="with-email" role="tabpanel">
              <WithEmail />
            </div>
          </div>

          <div class="divider">
            <span>atau</span>
          </div>

          <div class="signup-prompt">
            <p>Belum punya akun?</p>
            <router-link to="/daftar-pemilik" class="signup-link">
              Daftar Sekarang
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
              </svg>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { blockBtn, unblockBtn } from "@/libs/utils";

import WithEmail from "./tabs/WithEmail.vue";
import WithPhone from "./tabs/WithPhone.vue";
import { useSetting } from "@/services";

export default defineComponent({
  name: "login-pemilik",
  components: {
    WithEmail,
    WithPhone,
  },
  setup() {
    const store = useAuthStore();
    const router = useRouter();
    const { data: setting = {} } = useSetting();
    const submitButton = ref(null);

    const login = Yup.object().shape({
      identifier: Yup.string()
        .email("Email/No. Telepon tidak valid")
        .required("Harap masukkan Email/No. Telepon")
        .label("Email"),
      password: Yup.string()
        .min(8, "Password minimal terdiri dari 8 karakter")
        .required("Harap masukkan password")
        .label("Password"),
    });

    return {
      login,
      submitButton,
      getAssetPath,
      store,
      router,
      setting,
    };
  },
  data() {
    return {
      data: {
        identifier: null,
        password: null,
      },
      check: {
        type: "",
        error: "",
      },
    };
  },
  methods: {
    submit() {
      blockBtn(this.submitButton);
      axios
        .post("/auth/login", { ...this.data, type: this.check.type })
        .then((res) => {
          this.store.setAuth(res.data.user, res.data.token);
          this.router.push("/dashboard");
        })
        .catch((error) => {
          toast.error(error.response.data.message);
        })
        .finally(() => {
          unblockBtn(this.submitButton);
        });
    },
    checkInput(value) {
      this.check.type = "";
      if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value)) {
        this.check.type = "email";
      } else {
        this.check.type = "phone";
        if (isNaN(this.data.identifier)) {
          this.check.type = "Masukkan Email / No. Telepon Yang Valid!";
        }
      }
    },
  },
});
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.login-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  overflow: auto;
  padding: 20px;
}

.login-container {
  max-width: 1200px;
  margin: 0 auto;
  min-height: 100%;
  display: flex;
  align-items: center;
  gap: 0;
}

/* Left Side - Branding */
.login-branding {
  flex: 1;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 60px 50px;
  border-radius: 20px 0 0 20px;
  color: white;
  display: flex;
  align-items: center;
  min-height: 600px;
}

.branding-content {
  width: 100%;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 30px;
}

.logo-icon {
  width: 60px;
  height: 60px;
  background: white;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #667eea;
}

.logo-icon svg {
  width: 35px;
  height: 35px;
}

.brand-name {
  font-size: 36px;
  font-weight: 700;
  margin: 0;
}

.brand-tagline {
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 15px;
  line-height: 1.3;
}

.brand-description {
  font-size: 16px;
  line-height: 1.6;
  opacity: 0.9;
  margin-bottom: 40px;
}

.features {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 15px;
  font-size: 15px;
}

.feature-icon {
  width: 28px;
  height: 28px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  flex-shrink: 0;
}

/* Right Side - Form */
.login-form-section {
  flex: 1;
  background: white;
  padding: 60px 50px;
  border-radius: 0 20px 20px 0;
  min-height: 600px;
  display: flex;
  align-items: center;
}

.form-wrapper {
  width: 100%;
  max-width: 450px;
  margin: 0 auto;
}

.form-header {
  margin-bottom: 40px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: #f3f4f6;
  border-radius: 10px;
  color: #6b7280;
  text-decoration: none;
  margin-bottom: 20px;
  transition: all 0.3s ease;
}

.back-link:hover {
  background: #e5e7eb;
  color: #374151;
}

.form-title {
  font-size: 32px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 8px;
}

.form-subtitle {
  font-size: 16px;
  color: #6b7280;
  margin: 0;
}

/* Tabs */
.nav-tabs {
  list-style: none;
  padding: 0;
  margin-bottom: 30px;
  border-bottom: 2px solid #f3f4f6;
}

.nav-item {
  display: inline-block;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 15px 20px;
  font-size: 15px;
  font-weight: 500;
  color: #6b7280;
  text-decoration: none;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: #667eea;
}

.nav-link.active {
  color: #667eea;
  border-bottom-color: #667eea;
}

.tab-content {
  margin-bottom: 30px;
}

.tab-pane {
  display: none;
}

.tab-pane.show.active {
  display: block;
}

/* Divider */
.divider {
  position: relative;
  text-align: center;
  margin: 30px 0;
}

.divider::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  width: 100%;
  height: 1px;
  background: #e5e7eb;
}

.divider span {
  position: relative;
  background: white;
  padding: 0 15px;
  color: #9ca3af;
  font-size: 14px;
}

/* Signup Prompt */
.signup-prompt {
  text-align: center;
}

.signup-prompt p {
  color: #6b7280;
  margin-bottom: 12px;
  font-size: 15px;
}

.signup-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  font-size: 15px;
  padding: 12px 24px;
  border: 2px solid #667eea;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.signup-link:hover {
  background: #667eea;
  color: white;
}

/* Responsive */
@media (max-width: 968px) {
  .login-branding {
    display: none;
  }

  .login-form-section {
    border-radius: 20px;
    flex: 1;
  }

  .login-container {
    min-height: auto;
  }
}

@media (max-width: 640px) {
  .login-wrapper {
    padding: 15px;
  }

  .login-form-section {
    padding: 40px 30px;
  }

  .form-title {
    font-size: 26px;
  }

  .form-subtitle {
    font-size: 14px;
  }

  .nav-link {
    padding: 12px 15px;
    font-size: 14px;
  }
}
</style>