<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow fixed-top custom-navbar">
    <div class="container">
      <a class="navbar-brand fw-bold text-info d-flex align-items-center" href="#">
        <span class="fs-4 me-2 ms-20"></span> SLaundry
      </a>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-10 mb-2 mb-lg-0 fw-medium">
          <li class="nav-item">
            <a class="nav-link active text-info fw-semibold" href="#beranda">Beranda</a>
          </li>
          <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Kami</a></li>
          <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Lokasi</a></li>

          <!-- Jika BELUM login -->
          <li v-if="!isLoggedIn" class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle text-info fw-semibold d-flex align-items-center"
              href="#"
              id="aksesDropdown"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              🔑 Masuk
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="aksesDropdown">
              <li>
                <router-link class="dropdown-item d-flex align-items-start gap-2" to="/sign-in">
                  <span class="text-info fs-5">👤</span>
                  <div>
                    <div class="fw-semibold">Login</div>
                  </div>
                </router-link>
              </li>
            </ul>
          </li>

          <!-- Jika SUDAH login -->
          <li v-else class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle text-info fw-semibold d-flex align-items-center"
              href="#"
              id="userDropdown"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              👤 {{ userName || "Pengguna" }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
              <li>
                <a @click="signOut" class="dropdown-item d-flex align-items-start gap-2" href="#">
                  <span class="text-danger fs-5">🚪</span>
                  <div>
                    <div class="fw-semibold">Logout</div>
                    <small class="text-muted">Keluar dari sistem</small>
                  </div>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <router-view />
</template>
<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const store = useAuthStore();

// reactive login state
const isLoggedIn = ref(false);
const userName = ref("");

// cek login dari store atau localStorage
onMounted(() => {
  const storedUser = localStorage.getItem("user");
  if (store.isAuthenticated || storedUser) {
    isLoggedIn.value = true;
    userName.value = store.user?.name || JSON.parse(storedUser)?.name || "";
  }
});

const signOut = () => {
  Swal.fire({
    icon: "warning",
    text: "Apakah Anda yakin ingin keluar?",
    showCancelButton: true,
    confirmButtonText: "Ya, Keluar",
    cancelButtonText: "Batal",
    reverseButtons: true,
    buttonsStyling: false,
    customClass: {
      confirmButton: "btn fw-semibold btn-light-primary",
      cancelButton: "btn fw-semibold btn-light-danger",
    },
  }).then(async (result) => {
    if (result.isConfirmed) {
      await store.logout();
      localStorage.removeItem("user");
      localStorage.removeItem("mitra_id");
      isLoggedIn.value = false;

      Swal.fire({
        icon: "success",
        text: "Berhasil keluar",
      }).then(() => {
        router.push({ name: "sign-in" });
      });
    }
  });
};
</script>
<style scoped>
.custom-navbar {
  width: 100%;
  height: 70px;
  /* Sesuaikan dengan tinggi navbar Anda */
  left: 0;
  right: 0;

}
</style>