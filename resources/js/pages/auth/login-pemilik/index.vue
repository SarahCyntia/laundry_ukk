<template>
  <div class="auth-wrapper">
    <transition name="slide" mode="out-in">
      <div v-if="mode === 'login'" key="login" class="auth-container">
        <!-- Left branding -->
        <div class="auth-brand">
          <div class="content">
            <div class="logo">
              <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <path d="M12 6v6l4 2" />
                </svg>
              </div>
              <h2>SLaundry</h2>
            </div>
            <h3>Kelola Bisnis Laundry Anda dengan Mudah</h3>
            <p>Sistem manajemen laundry yang membantu Anda mengelola pesanan, pelanggan, dan keuangan secara efisien.</p>
            <ul class="features">
              <li>✓ Manajemen Pesanan Real-time</li>
              <li>✓ Laporan Keuangan Lengkap</li>
              <li>✓ Notifikasi Otomatis</li>
            </ul>
          </div>
        </div>

        <!-- Right login form -->
        <div class="auth-form">
          <div class="form-box">
            <h1>Selamat Datang Kembali</h1>
            <p>Masuk ke akun pemilik Anda</p>

            <WithEmail />

            <div class="divider"><span>atau</span></div>

            <p class="switch-text">
              Belum punya akun?
              <button class="switch-btn" @click="toggleMode('register')">Daftar Sekarang →</button>
            </p>
          </div>
        </div>
      </div>

      <!-- REGISTER SECTION -->
      <div v-else key="register" class="auth-container">
        <div class="auth-form full">
          <div class="form-box">
            <h1>Buat Akun Pemilik</h1>
            <p>Isi data berikut untuk mendaftar</p>

            <DaftarPemilik />

            <div class="divider"><span>atau</span></div>

            <p class="switch-text">
              Sudah punya akun?
              <button class="switch-btn" @click="toggleMode('login')">← Masuk Sekarang</button>
            </p>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import WithEmail from "./tabs/WithEmail.vue";
import DaftarPemilik from "./tabs/daftar-pemilik.vue";

const mode = ref<"login" | "register">("login");
const toggleMode = (target: "login" | "register") => {
  mode.value = target;
};
</script>

<style scoped>
/* ---------- LAYOUT ---------- */
.auth-wrapper {
  position: fixed;
  inset: 0;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.auth-container {
  display: flex;
  width: 100%;
  max-width: 1100px;
  min-height: 580px;
  background: #ffffff;
  border-radius: 25px;
  overflow: hidden;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
  transition: 0.3s ease;
}

/* ---------- LEFT SIDE ---------- */
.auth-brand {
  flex: 1;
  background: rgba(255, 255, 255, 0.15);
  color: #fff;
  padding: 60px 50px;
  backdrop-filter: blur(12px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-brand .content {
  max-width: 440px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 25px;
}

.logo .icon {
  width: 65px;
  height: 65px;
  background: #fff;
  border-radius: 18px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #667eea;
  font-size: 28px;
}

.logo h2 {
  font-size: 34px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.features {
  list-style: none;
  margin-top: 35px;
  padding: 0;
}

.features li {
  margin: 10px 0;
  font-size: 15px;
  line-height: 1.6;
}

/* ---------- RIGHT SIDE ---------- */
.auth-form {
  flex: 1;
  background: #fafafa;
  padding: 60px 60px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-form.full {
  flex: 1;
  border-radius: 25px;
}

.form-box {
  width: 100%;
  max-width: 420px;
  background: #fff;
  padding: 40px 35px;
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
}

.form-box h1 {
  font-size: 30px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 12px;
  text-align: center;
}

.form-box p {
  color: #6b7280;
  margin-bottom: 28px;
  text-align: center;
}

.divider {
  position: relative;
  text-align: center;
  margin: 30px 0;
}

.divider::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 1px;
  top: 50%;
  left: 0;
  background: #e5e7eb;
}

.divider span {
  background: #fff;
  padding: 0 12px;
  color: #9ca3af;
  font-size: 14px;
}

/* ---------- SWITCH BUTTON ---------- */
.switch-text {
  text-align: center;
  font-size: 15px;
}

.switch-btn {
  background: none;
  border: none;
  color: #667eea;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.switch-btn:hover {
  text-decoration: underline;
}

/* ---------- ANIMATION ---------- */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.6s ease;
}

.slide-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

/* ---------- RESPONSIVE ---------- */
@media (max-width: 968px) {
  .auth-brand {
    display: none;
  }

  .auth-container {
    border-radius: 20px;
    max-width: 480px;
  }

  .auth-form {
    background: #fff;
    padding: 40px 30px;
  }

  .form-box {
    box-shadow: none;
    padding: 20px 10px;
  }
}
</style>
