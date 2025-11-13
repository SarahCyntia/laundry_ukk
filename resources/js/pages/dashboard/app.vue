<template>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow fixed-top custom-navbar">
    <div class="container">
      <!-- Logo -->
      <a class="navbar-brand fw-bold text-info d-flex align-items-center" href="#">
        <span class="fs-4 me-2 ms-20"></span> SLaundry
      </a>

      <!-- Mobile Toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <!-- ganti mx-auto -> ms-3 -->
        <ul class="navbar-nav ms-10 mb-2 mb-lg-0 fw-medium">
          <li class="nav-item ">
            <a class="nav-link active text-info fw-semibold" aria-current="page" href="#beranda">
              Beranda
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#tentang">Tentang Kami</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#layanan">Layanan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Lokasi</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-info fw-semibold d-flex align-items-center" href="#"
              id="aksesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              🔑 Akses Sistem
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="aksesDropdown">
              <li>
                <router-link class="dropdown-item d-flex align-items-start gap-2" to="/pelanggan">
                  <!-- <a class="dropdown-item d-flex align-items-start gap-2" href="/pelanggan/index.vue"> -->
                  <span class="text-info fs-5">👤</span>
                  <div>
                    <div class="fw-semibold">Akses Pelanggan</div>
                    <small class="text-muted">Pesan & lacak laundry Anda</small>
                  </div>
                </router-link>
                <!-- </a> -->
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li v-if="!store.isAuthenticated">
                <router-link class="dropdown-item d-flex align-items-start gap-2" to="/sign-in">
                  <span class="text-warning fs-5">🔒</span>
                  <div>
                    <div class="fw-semibold">Sign in</div>
                    <small class="text-muted">
                      Login
                    </small>
                  </div>

                </router-link>
              </li>

              <li v-else>
                <a @click="signOut()" class="dropdown-item d-flex align-items-start gap-2">
                  <span class="text-danger fs-5">🚪</span>
                  <div>
                    <div class="fw-semibold">Sign Out</div>
                    <small class="text-muted">Keluar dari sistem</small>
                  </div>
                </a>
              </li>
             
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-info fw-semibold d-flex align-items-center" href="#"
              id="aksesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              🔑 Masuk
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="aksesDropdown">
              <li>
                <router-link class="dropdown-item d-flex align-items-start gap-2" to="/sign-in">
                  <span class="text-info fs-5">👤</span>
                  <div>
                    <div class="fw-semibold">Login</div>
                    <!-- <small class="text-muted">Pesan & lacak laundry Anda</small> -->
                  </div>
                </router-link>
                <!-- </a> -->
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <!-- <li>
                <a class="dropdown-item d-flex align-items-start gap-2" href="/sign-in">
                  <span class="text-warning fs-5">🔒</span>
                  <div>
                    <div class="fw-semibold">Pelanggan</div>
                    <small class="text-muted">Daftar sebagai pelanggan</small>
                  </div>
                </a>
              </li> -->
             
             
            </ul>
          </li>
        
        </ul>
     
          <div>
    <!-- Tombol untuk membuka modal -->
    <!-- <button class="btn btn-info text-white fw-semibold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#loginModal">
      🔑 Masuk
    </button> -->

    <!-- Modal -->
    <div
      class="modal fade"
      id="loginModal"
      tabindex="-1"
      aria-labelledby="loginModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold text-dark" id="loginModalLabel">Masuk ke SLaundry</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-4">Saya ingin masuk sebagai</p>

            <!-- Pilihan: Pelanggan -->
            <div
              class="card border-0 shadow-sm mb-3 pilih-akses"
              @click="goTo('pelanggan')"
            >
              <div class="card-body d-flex align-items-center p-3">
                <img
                  src="/images/pelanggan.png"
                  alt="Pelanggan"
                  width="60"
                  height="60"
                  class="me-3"
                />
                <h6 class="fw-semibold mb-0">Pelanggan Laundry</h6>
              </div>
            </div>

            <!-- Pilihan: Mitra -->
            <div
              class="card border-0 shadow-sm pilih-akses"
              @click="goTo('mitra')"
            >
              <div class="card-body d-flex align-items-center p-3">
                <img
                  src="/images/mitra.png"
                  alt="Mitra"
                  width="60"
                  height="60"
                  class="me-3"
                />
                <h6 class="fw-semibold mb-0">Mitra Laundry</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

      </div>
    </div>


  </nav>


  <!-- <main class="min-vh-100">
    <router-view />
  </main> -->
  <router-view />



</template>



<script setup lang="ts">
import { RouterView } from "vue-router";
import { RouterLink } from "vue-router";
import Swal from "sweetalert2";
// import router from "@/router";

import { useRouter } from "vue-router";

const router = useRouter();

const goTo = (role: string) => {
  // Tutup modal
  const modal = document.getElementById("loginModal");
  const modalInstance = bootstrap.Modal.getInstance(modal);
  modalInstance?.hide();

  // Arahkan ke halaman login sesuai role
  if (role === "pelanggan") router.push({ name: "pelanggan-login" });
  else router.push({ name: "mitra-login" });
};
// const router = useRouter();

// const goTo = (role: string) => {
//   if (role === "pelanggan") {
//     router.push({ name: "pelanggan-login" });
//   } else if (role === "mitra") {
//     router.push({ name: "mitra-login" });
//   }
// };

import { useAuthStore } from "@/stores/auth";

const store = useAuthStore();
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
html {
  scroll-behavior: smooth;
}

/* Biar dropdown muncul saat hover, bukan klik */
.nav-item.dropdown:hover .dropdown-menu {
  display: block;
  margin-top: 0;
  /* biar rapet ke navbar */
}

.custom-navbar {
  width: 100%;
  height: 70px;
  /* Sesuaikan dengan tinggi navbar Anda */
  left: 0;
  right: 0;

}
.card:hover {
  transform: translateY(-3px);
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
.modal-content {
  max-width: 420px;
  margin: 0 auto;
  border-radius: 16px;
}

.pilih-akses {
  transition: all 0.3s ease;
  cursor: pointer;
  border-radius: 12px;
}

.pilih-akses:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
}
</style>

