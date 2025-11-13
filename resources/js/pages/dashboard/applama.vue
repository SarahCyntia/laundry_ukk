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
              <li>
                <a class="dropdown-item d-flex align-items-start gap-2" href="/sign-in">
                  <span class="text-warning fs-5">🔒</span>
                  <div>
                    <div class="fw-semibold">Akses Admin</div>
                    <small class="text-muted">Kelola pesanan & pelanggan</small>
                  </div>
                </a>
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
              <!-- <li> -->
                 <!-- <div class="menu-item px-5">
            <a @click="signOut()" class="menu-link px-5"> Sign Out </a>
        </div>
               <!-- <router-link class="btn btn-light btn-sm rounded-pill fw-semibold text-info" to="/sign-in">Logout</router-link> -
               </li> -->
            </ul>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link text-info fw-semibold d-flex align-items-center" href="#">
              🔑 Akses Sistem
            </a>
          </li> -->
        </ul>
      </div>
    </div>


  </nav>


  <!-- <nav class="navbar navbar-expand-lg navbar-light bg-white shadow sticky-top">
      <div class="container">
        <RouterLink class="navbar-brand fw-bold text-info d-flex align-items-center" to="/">
          <span class="fs-4 me-2"></span> SLaundry
        </RouterLink>

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
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-medium">
            <li class="nav-item">
              <RouterLink class="nav-link" :to="{ path: '/beranda', hash: '#' }">Beranda</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" :to="{ path: '/beranda', hash: '#tentang' }">Tentang Kami</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" :to="{ path: '/', hash: '#layanan' }">Layanan</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" :to="{ path: '/', hash: '#lokasi' }">Lokasi</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link text-info fw-semibold d-flex align-items-center" to="/login">
                🔑 Akses Sistem
              </RouterLink>
            </li>
          </ul>
        </div>
      </div>
    </nav> -->

  <!-- <main class="min-vh-100">
    <router-view />
  </main> -->
  <router-view />

  

</template>



<script setup lang="ts">
import { RouterView } from "vue-router";
import { RouterLink } from "vue-router";
import Swal from "sweetalert2";
import router from "@/router";

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
    }).then(async(result) => {
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
</style>

<!-- Info Bar -->
<!-- <div class="bg-info text-white py-2">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex gap-4 small">
            <span>🏪 <strong>Status Toko:</strong> BUKA</span>
            <span>⏰ <strong>Jam Operasional:</strong> 06.30 - 21.00 WIB</span>
        </div>
        <a href="#" class="btn btn-light btn-sm rounded-pill fw-semibold text-info">
            Akses Pelanggan →
        </a>
    </div>
</div> -->

<!-- 
<footer className="bg-gray-900 text-white py-12 px-6">
        <div className="container mx-auto">
          <div className="grid md:grid-cols-4 gap-8">
            <div className="md:col-span-2">
              <div className="flex items-center space-x-2 mb-6">
                <div className="w-10 h-10 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg flex items-center justify-center">
                  <span className="text-white font-bold text-xl">Z</span>
                </div>
                <span className="text-2xl font-bold">Zeea Laundry</span>
              </div>
              <p className="text-gray-400 max-w-md mb-6">
                Penyedia jasa laundry profesional dengan komitmen memberikan layanan terbaik untuk semua kebutuhan laundry Anda.
              </p>
              <div className="flex space-x-4">
                <a href="#" className="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center hover:bg-cyan-700 transition-colors">
                  <span className="text-sm font-bold">f</span>
                </a>
                <a href="#" className="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center hover:bg-cyan-700 transition-colors">
                  <span className="text-sm font-bold">t</span>
                </a>
                <a href="#" className="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center hover:bg-cyan-700 transition-colors">
                  <span className="text-sm font-bold">i</span>
                </a>
              </div>
            </div>
            
            <div>
              <h4 className="text-lg font-bold mb-4">Layanan</h4>
              <ul className="space-y-2 text-gray-400">
                <li><a href="#" className="hover:text-cyan-400 transition-colors">Cuci Kering</a></li>
                <li><a href="#" className="hover:text-cyan-400 transition-colors">Cuci Setrika</a></li>
                <li><a href="#" className="hover:text-cyan-400 transition-colors">Dry Clean</a></li>
                <li><a href="#" className="hover:text-cyan-400 transition-colors">Antar Jemput</a></li>
              </ul>
            </div>
            
            <div>
              <h4 className="text-lg font-bold mb-4">Kontak</h4>
              <ul className="space-y-2 text-gray-400">
                <li>Surabaya, Jawa Timur</li>
                <li>+62 812-3456-7890</li>
                <li>info@zeealaundry.com</li>
                <li>www.zeealaundry.com</li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 Zeea Laundry. Semua hak cipta dilindungi.</p>
          </div>
        </div>
      </footer> -->