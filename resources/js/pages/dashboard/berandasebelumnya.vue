<template>
  <div class="bg-info text-white py-2" style="margin-top: 70px;">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="d-flex gap-4 small">
        <span>🏪 <strong>Status Toko:</strong> BUKA</span>
        <span>⏰ <strong>Jam Operasional:</strong> 06.30 - 21.00 WIB</span>
      </div>
      <router-link class="btn btn-light btn-sm rounded-pill fw-semibold text-info" to="/pelanggan">Akses Pelanggan
        →</router-link>
      <!-- <a href="#" class="btn btn-light btn-sm rounded-pill fw-semibold text-info">
        Akses Pelanggan →
      </a> -->
    </div>
  </div>
  <div class="min-h-screen bg-gradient">
    <div class="container">
      <!-- Hero Section -->
      <section class="hero-section">
        <!-- Text Content -->
        <div class="content-wrapper">
          <span class="tagline">Rapi, Bersih, Wangi!</span>

          <h1 class="hero-title">
            <span class="text-highlight">Slaundry</span> Solusi<br>
            Terpercaya untuk<br>
            Pakaian Anda
          </h1>

          <p class="hero-description">
            Slaundry menyediakan layanan cuci dan setrika profesional
            dengan hasil rapi, bersih, dan wangi. Kami menggunakan deterjen
            berkualitas dan teknologi modern untuk merawat pakaian Anda.
          </p>

          <!-- Action Buttons -->
          <div class="button-group">
            <button class="btn btn-info" @click="lihatLayanan">
              Lihat Layanan
              <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            <button class="btn btn-secondary" @click="aksesSystem">
              <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 4a2 2 0 104 0m-4 0a2 2 0 114 0m-4 0v2a2 2 0 002 2h2a2 2 0 002-2v-2">
                </path>
              </svg>
              Akses Sistem
            </button>
          </div>

          <!-- Feature Cards -->
          <div class="features-grid">
            <div class="feature-card">
              <div class="feature-icon icon-blue">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
              </div>
              <span class="feature-text">Layanan Antar Jemput</span>
            </div>

            <div class="feature-card">
              <div class="feature-icon icon-green">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <span class="feature-text">Lacak Pesanan Online</span>
            </div>

            <div class="feature-card feature-card-wide">
              <div class="feature-icon icon-purple">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
              </div>
              <span class="feature-text">Riwayat Transaksi</span>
            </div>
          </div>
        </div>

        <!-- Illustration Section -->
        <div class="illustration-wrapper">
          <img src="/storage/image/hero.png" class="map1" />

        </div>
      </section>

      <div class="customer-benefits">
        <!-- Header Section -->
        <div class="header-section">
          <div class="badge">
            <svg class="star-icon" fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.519-4.674z">
              </path>
            </svg>
            Keuntungan Pelanggan
          </div>

          <h2 class="main-title">
            Anda Seorang <span class="text-highlight"> Pelanggan?</span>
          </h2>

          <p class="description">
            Semua kebutuhan pelanggan dapat diakses melalui halaman
            <span class="accent-text">akses sistem</span>
            pelanggan dibawah
          </p>
        </div>

        <!-- Cards Section -->
        <div class="cards-container">
          <!-- Antar Jemput Card -->
          <div class="service-card">
            <div class="card-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
              </svg>
            </div>

            <h3 class="card-title">Antar Jemput</h3>

            <p class="card-text">
              Pesan layanan antar jemput langsung dari akun pelanggan Anda.
              Kami akan menjemput dan mengantar pakaian Anda tepat waktu.
            </p>

            <button class="card-button" @click="pesanSekarang">
              <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                </path>
              </svg>
              Pesan Sekarang
            </button>
          </div>

          <!-- Lacak Pesanan Card -->
          <div class="service-card">
            <div class="card-icon">
              icon
            </div>

            <h3 class="card-title">Lacak Pesanan</h3>

            <p class="card-text">
              Pantau status pesanan laundry Anda secara real-time. Anda dapat
              melihat apakah pakaian sedang dicuci, disetrika, atau siap
              untuk diambil kapan saja.
            </p>

            <button class="card-button" @click="lacakSekarang">
              <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                </path>
              </svg>
              Lacak Sekarang
            </button>
          </div>

          <!-- Riwayat Transaksi Card -->
          <div class="service-card">
            <div class="card-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>

            <h3 class="card-title">Riwayat Transaksi</h3>

            <p class="card-text">
              Akses riwayat transaksi laundry Anda dengan mudah. Lihat detail
              pesanan sebelumnya dan pesan kembali layanan yang sama dengan
              cepat.
            </p>

            <button class="card-button" @click="lihatRiwayat">
              <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                </path>
              </svg>
              Lihat Riwayat
            </button>
          </div>
        </div>
      </div>

      <div class="system-access">
        <!-- Header Section -->
        <div class="header-section">
          <div class="back-badge" @click="backToHome">
            <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12">
              </path>
            </svg>
            Masuk ke Sistem
          </div>

          <h1 class="main-title">
            <span class="title-highlight">Akses Sistem</span> Slaundry
          </h1>

          <p class="subtitle">
            Silahkan pilih akses sesuai dengan kebutuhan Anda
          </p>
        </div>

        <!-- Access Cards -->
        <div class="access-cards">
          <!-- Customer Card -->
          <div class="access-card customer-card">
            <div class="card-illustration">
              <div class="orang1">
                <img src="/storage/image/gambarpelanggan.png" class="orang" />
              </div>
            </div>

            <div class="card-content">
              <h2 class="card-title customer-title">Pelanggan</h2>
              <p class="card-description">Pesan layanan dan cek status</p>
              <router-link class="access-btn customer-btn" to="/pelanggan">Masuk</router-link>
              <!-- <button class="access-btn customer-btn" @click="loginCustomer">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 0a4 4 0 01-4 4H6a4 4 0 01-4-4V7a4 4 0 014-4h7a4 4 0 014 4"></path>
                </svg>
                Masuk
              </button> -->
            </div>
          </div>

          <!-- Admin Card -->
          <div class="access-card admin-card">
            <div class="card-illustration">
              <div class="orang1">
                <img src="/storage/image/adminlogin.png" class="orang" />
              </div>
            </div>

            <div class="card-content">
              <h2 class="card-title admin-title">Admin</h2>
              <p class="card-description">Kelola pesanan pelanggan</p>
              <router-link class="access-btn admin-btn" to="/sign-in">Masuk</router-link>
              <!-- <button class="access-btn admin-btn" @click="loginAdmin">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 0a4 4 0 01-4 4H6a4 4 0 01-4-4V7a4 4 0 014-4h7a4 4 0 014 4"></path>
                </svg>
                Masuk
              </button> -->
            </div>
          </div>
        </div>
      </div>




      <div class="customer-benefits" id="tentang">
        <!-- Header Section -->
        <div class="header-section">
          <div class="badge">
            <i class="bi bi-exclamation-lg"></i>Tentang Kami
          </div>

          <h2 class="main-title">
            Anda Seorang <span class="text-highlight"> Pelanggan?</span>
          </h2>

          <p class="description">
            Semua kebutuhan pelanggan dapat diakses melalui halaman
            <span class="accent-text">akses sistem</span>
            pelanggan dibawah
          </p>
        </div>

        <!-- Cards Section -->
        <div class="cards-containerrr">
          <!-- Antar Jemput Card -->
          <div class="service-card">
            <div class="card-iconn">
              <i class="fas fa-tshirt"></i>
            </div>
            <h4 class="card-titlee">Kualitas</h4>

            <p class="card-text">
              Kami menggunakan deterjen berkualitas tinggi dan teknologi modern untuk memastikan pakaian Anda bersih,
              wangi, dan terawat dengan baik.
            </p>
          </div>

          <!-- Tepat Waktu-->
          <div class="service-card">
            <div class="card-iconn">
              <i class="bi bi-clock-fill"></i>
            </div>

            <h4 class="card-titlee">Tepat Waktu</h4>

            <p class="card-text">
              Kami menghargai waktu Anda. Layanan kami menjamin pengerjaan dan pengiriman yang tepat waktu sesuai dengan
              jadwal yang dijanjikan.
            </p>
          </div>


          <div class="service-card">
            <div class="card-iconn">
              <i class="fas fa-hand-holding-usd"></i>
            </div>

            <h4 class="card-titlee">Harga Terjangkau</h4>

            <p class="card-text">
              Akses riwayat transaksi laundry Anda dengan mudah. Lihat detail
              pesanan sebelumnya dan pesan kembali layanan yang sama dengan
              cepat.
            </p>
          </div>
          <div class="service-card">
            <div class="card-iconn">
              <i class="fas fa-truck"></i>
            </div>

            <h4 class="card-titlee">Riwayat Transaksi</h4>

            <p class="card-text">
              Akses riwayat transaksi laundry Anda dengan mudah. Lihat detail
              pesanan sebelumnya dan pesan kembali layanan yang sama dengan
              cepat.
            </p>
          </div>
          <div class="service-card">
            <div class="card-iconn">
              <i class="fas fa-shield-alt"></i>
            </div>

            <h4 class="card-titlee">Riwayat Transaksi</h4>

            <p class="card-text">
              Akses riwayat transaksi laundry Anda dengan mudah. Lihat detail
              pesanan sebelumnya dan pesan kembali layanan yang sama dengan
              cepat.
            </p>
          </div>
        </div>
      </div>






      <div class="laundry-servicess" id="layanan">
        <!-- Header Section -->
        <div class="header-sectionn">
          <div class="badge">
            <i class="bi bi-journal-text"></i>
            Layanan Kami
          </div>

          <h2 class="main-title">
            Paket <span class="text-highlight">Layanan</span> Kami
          </h2>

          <p class="description">
            Kami menyediakan berbagai layanan laundry untuk memenuhi kebutuhan
            Anda. Berikut adalah paket layanan yang kami tawarkan.
          </p>
        </div>

        <div class="services-grid">
          <div v-for="(item, i) in services" :key="i" class="service-simple-card" @click="pesanSekarang(item.slug)">
            <img :src="item.icon" class="service-icon" />
            <h3 class="service-name">{{ item.name }}</h3>
          </div>
        </div>
      </div>





    </div>
  </div>

  <section class="cta-section text-center text-light py-10">
    <div class="container py-10">
      <h2 class="fw-bold mb-2">Siap Untuk Mencoba Layanan Kami?</h2>
      <p class="mb-4">
        Hubungi kami sekarang untuk mendapatkan layanan laundry terbaik. Kami siap melayani
        kebutuhan laundry Anda dengan profesional.
      </p>
      <a href="https://wa.me/681231365656" target="_blank" rel="noopener"
        class="btn btn-light text-primary fw-bold px-4">
        <i class="bi bi-whatsapp me-1"></i> Hubungi Kami Sekarang
      </a>
    </div>
  </section>
  <footer class="footer-dark text-light pt-5 pb-4">
    <div class="container">
      <div class="row gy-4">
        <!-- Brand -->
        <div class="col-md-4">
          <h5 class="ligo fw-bold mb-2">
            <!-- <img src="/storage/image/logo.png" alt="Logo" class="logo me-1" /> -->
            SLaundry
          </h5>
          <p class="small mb-0">
            SLaundry adalah penyedia jasa laundry profesional yang berkomitmen memberikan layanan
            terbaik untuk semua kebutuhan laundry Anda.
          </p>
        </div>

        <!-- Tautan -->
        <div class="col-md-2">
          <h6 class="fw-bold mb-3 section-title">Tautan</h6>
          <ul class="list-unstyled small">
            <li><router-link to="beranda" class="footer-link">Beranda</router-link></li>
            <li><router-link to="tentang" class="footer-link">Tentang Kami</router-link></li>
            <li><router-link to="/layanan" class="footer-link">Layanan</router-link></li>
            <li><router-link to="/lokasi" class="footer-link">Lokasi</router-link></li>
            <li><router-link to="/login" class="footer-link">Akses Sistem</router-link></li>
          </ul>
        </div>

        <!-- Layanan -->
        <div class="col-md-3">
          <h6 class="fw-bold mb-3 section-title">Layanan</h6>
          <ul class="list-unstyled small">
            <li>Cuci Kering</li>
            <li>Cuci Setrika</li>
            <li>Setrika Saja</li>
            <li>Cuci Custom</li>
          </ul>
        </div>

        <!-- Kontak -->
        <div class="col-md-3">
          <h6 class="fw-bold mb-3 section-title">Kontak Kami</h6>
          <ul class="list-unstyled small mb-0">
            <li class="mb-2 d-flex">
              <i class="bi bi-geo-alt-fill text-info me-2"></i>
              Surabaya, Indonesia
            </li>
            <li class="d-flex">
              <i class="bi bi-telephone-fill text-info me-2"></i>
              +62 812-3456-789
            </li>
          </ul>
        </div>
      </div>

      <hr class="divider my-4" />
      <div class="text-center small text-secondary">
        © {{ new Date().getFullYear() }} SLaundry. All Rights Reserved.
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">

import { ref } from "vue";
const services = ref([
  { name: "Daily Kiloan", slug: "daily-kiloan", icon: "/storage/image/kiloan.png" },
  { name: "Cuci & Setrika", slug: "cuci-setrika", icon: "/storage/image/cuci_setrika.png" },
  { name: "Laundry Sepatu", slug: "laundry-sepatu", icon: "/storage/image/sepatu.png" },
  { name: "Laundry Tas", slug: "laundry-tas", icon: "/storage/image/tas.png" },
  { name: "Laundry Sarung Bantal", slug: "laundry-karpet", icon: "/storage/image/karpet.png" },
  { name: "Laundry Bed Cover ", slug: "laundry-gorden", icon: "/storage/image/gorden.png" },
  { name: "Laundry Sprei", slug: "laundry-stroller", icon: "/storage/image/stroller.png" },
  { name: "Laundry Boneka", slug: "laundry-boneka", icon: "/storage/image/boneka.png" },
]);

const pesanSekarang = (slug: string) => {
  console.log("Pesan layanan:", slug);
  // arahkan ke halaman pemesanan / kirim event sesuai kebutuhan
};
// import TentangAdmin from '..tre'
// const lihatLayanan = (): void => {
//   console.log('Navigating to services...')
//   // Add navigation logic here
// }

const aksesSystem = (): void => {
  console.log('Accessing system...')
  // Add system access logic here
}

// const pesanSekarang = (): void => {
//   console.log('Navigating to booking page...')
//   // Add your routing logic here
//   // Example: router.push('/pesan-antar-jemput')
// }

const lacakSekarang = (): void => {
  console.log('Navigating to tracking page...')
  // Add your routing logic here
  // Example: router.push('/lacak-pesanan')
}

const lihatRiwayat = (): void => {
  console.log('Navigating to history page...')
  // Add your routing logic here
  // Example: router.push('/riwayat-transaksi')
}

const backToHome = (): void => {
  console.log('Navigating back to home...')
  // Add navigation logic here
  // Example: router.push('/')
}

const loginCustomer = (): void => {
  console.log('Navigating to customer login...')
  // Add customer login logic here
  // Example: router.push('/login/customer')
}

const loginAdmin = (): void => {
  console.log('Navigating to admin login...')
  // Add admin login logic here
  // Example: router.push('/login/admin')
}

// export default {
//   name: 'LaundryServices',
//   methods: {
//     pesanSekarang(serviceType) {
//       // Handle order logic here
//       console.log('Pesan layanan:', serviceType);
//       // You can emit an event or navigate to order page
//       this.$emit('order-service', serviceType);
//     }
//   }
// }
</script>

<style scoped>
.services-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* 4 kolom */
  gap: 1rem; /* jarak antar card */
}

.service-simple-card {
  background: #fff;
  border-radius: 16px;
  padding: 1.5rem 1rem;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: all 0.25s ease;
  cursor: pointer;
}

.service-simple-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.12);
}

.service-icon {
  width: 90px;
  height: 90px;
  object-fit: contain;
  margin-bottom: 1rem;
}

.service-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: #008b8b; /* warna teal seperti contoh */
}


.footer-dark {
  background-color: #2e2e2e;
}

/* .logo {
  height: 28px;
  color: #ffffff;
} */
.ligo {
  height: 28px;
  color: #ffffff;
}

.section-title {
  border-bottom: 2px solid #0dcaf0;
  padding-bottom: 4px;
  color: #e5d9d9;
}

.footer-link {
  color: #ccc;
  text-decoration: none;
  display: inline-block;
  margin-bottom: 6px;
}

.footer-link:hover {
  color: #0dcaf0;
}

.divider {
  border-color: rgba(255, 255, 255, 0.1);
}

.cta-section {
  background: linear-gradient(135deg, #3434f6 0%, #28d7d7 100%);
  padding: 60px 0;
  /* tinggi area */
  margin-top: 60px;
  /* jarak ke section atas */
}







.orang {
  width: 150px;
  height: 200px;
  object-fit: contain;
  border-radius: 20px;
}

.orang1 {
  width: 200px;
  height: 180px;
  margin: 0 auto 24px;
  margin-top: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #98b3ef;
  border-radius: 20px;
}

.map1 {
  width: 500px;
  height: 500px;
  object-fit: contain;
  border-radius: 20px;
}

.map-image {
  width: 70px;
  height: 70px;
  object-fit: contain;
}

.card-icon-image {
  width: 100px;
  height: 100px;
  margin: 0 auto 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(79, 209, 197, 0.1) 0%, rgba(54, 209, 220, 0.1) 100%);
  border-radius: 20px;
}

.laundry-servicess {
  max-width: 1200px;
  margin: 0 auto;
  /* padding: 40px 40px; */
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}


.header-sectionn {
  text-align: center;
  margin-bottom: 60px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 0 20px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(79, 209, 197, 0.1);
  color: #4fd1c5;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 20px;
  border: 1px solid rgba(79, 209, 197, 0.2);
  justify-self: center;
}

.badge i {
  font-size: 16px;
  color: #f3e8ff;
}

.star-icon {
  width: 16px;
  height: 16px;
}

.main-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #2d3748;
  margin-bottom: 16px;
  line-height: 1.2;
}

.text-highlight {
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.description {
  font-size: 1.1rem;
  color: #718096;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
  text-align: center;
}

/* Services Container */
.services-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  margin-top: 40px;
  /* width: 90%; */
  justify-items: center;
}

/* Service Card */
.service-card {
  background: white;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(226, 232, 240, 0.8);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.service-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.service-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
}

/* Card Badge */
.card-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  padding: 6px 12px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.card-badge.popular {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.card-badge.terlaris {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.card-badge.praktis {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
}

/* Card Icon */
.card-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(79, 209, 197, 0.1) 0%, rgba(54, 209, 220, 0.1) 100%);
  border-radius: 20px;
}


.service-image {
  width: 50px;
  height: 50px;
  object-fit: contain;
}

/* Card Title */
.card-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d3748;
  /* color: #2d3748; */
  text-align: center;
  margin-bottom: 16px;
}

/* Price Section */
.price-section {
  text-align: center;
  margin-bottom: 20px;
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 4px;
}

.price {
  font-size: 2rem;
  font-weight: 800;
  color: #2d3748;
}

.price-unit {
  font-size: 1.1rem;
  color: #718096;
  font-weight: 500;
}

.price-type {
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
  color: white;
  padding: 4px 8px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  margin-left: 8px;
}

/* Card Description */
.card-description {
  color: #718096;
  line-height: 1.6;
  margin-bottom: 24px;
  text-align: center;
}

/* Features List */
.features-list {
  list-style: none;
  margin-bottom: 30px;
}

.features-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
  color: #4a5568;
  font-size: 14px;
}

.check-icon::before {
  content: '✓';
  width: 20px;
  height: 20px;
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
  flex-shrink: 0;
}

/* Order Button */
.order-button {
  width: 100%;
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
  color: white;
  border: none;
  padding: 14px 20px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.order-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(79, 209, 197, 0.3);
}

.order-button:active {
  transform: translateY(0);
}

.button-icon {
  width: 20px;
  height: 20px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .laundry-services {
    padding: 20px 15px;
  }

  .main-title {
    font-size: 2rem;
  }

  .services-container {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .service-card {
    padding: 24px;
  }

  .price {
    font-size: 1.8rem;
  }
}












html {
  scroll-behavior: smooth;
}

.system-access {
  min-height: 100vh;
  /* background: linear-gradient(135deg, #e8f5f3 0%, #f0f9ff 50%, #ffffff 100%); */
  padding: 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* ===== HEADER SECTION ===== */
/* .header-section {
  text-align: center;
  margin-bottom: 4rem;
  max-width: 600px;
} */

.back-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #7042e4, #8579ff);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 2rem;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(77, 208, 225, 0.3);
}

.back-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(77, 208, 225, 0.4);
}

.back-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.main-title {
  font-size: 3rem;
  font-weight: 700;
  color: #37474f;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.title-highlight {
  color: #7042e4;
  /* color: #4dd0e1; */
}

.subtitle {
  color: #78909c;
  font-size: 1.1rem;
  margin: 0;
}

/* ===== ACCESS CARDS ===== */
.access-cards {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 3rem;
  max-width: 800px;
  width: 800%;
}

.access-card {
  background: white;
  border-radius: 20px;
  padding: 1rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  text-align: center;
  position: relative;
  overflow: hidden;
}

.access-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  transform: scaleX(0);
  transition: transform 0.4s ease;
  transform-origin: left;
}

.customer-card::before {
  background: linear-gradient(135deg, #4dd0e1, #26c6da);
}

.admin-card::before {
  background: linear-gradient(135deg, #ffa726, #ff9800);
}

.access-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.access-card:hover::before {
  transform: scaleX(1);
}

/* ===== CARD ILLUSTRATIONS ===== */
.card-illustration {
  height: 200px;
  margin-bottom: 2rem;
  position: relative;
  border-radius: 16px;
  overflow: hidden;
}



.customer-title {
  color: #4dd0e1;
}


.card-description {
  color: #78909c;
  margin-bottom: 2rem;
  font-size: 0.95rem;
}

/* ===== ACCESS BUTTONS ===== */
.access-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem 2rem;
  border-radius: 25px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  color: white;
  min-width: 120px;
  justify-content: center;
}

.customer-btn {
  background: linear-gradient(135deg, #4dd0e1, #26c6da);
  box-shadow: 0 4px 15px rgba(77, 208, 225, 0.3);
}

.customer-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(77, 208, 225, 0.4);
}

.admin-btn {
  background: linear-gradient(135deg, #ffa726, #ff9800);
  box-shadow: 0 4px 15px rgba(255, 167, 38, 0.3);
}

.admin-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 167, 38, 0.4);
}

.btn-icon {
  width: 1rem;
  height: 1rem;
}

/* ===== ANIMATIONS ===== */
@keyframes steam-rise {
  0% {
    transform: translateY(0) scale(0.8);
    opacity: 0.6;
  }

  50% {
    transform: translateY(-15px) scale(1);
    opacity: 0.3;
  }

  100% {
    transform: translateY(-30px) scale(1.2);
    opacity: 0;
  }
}

@keyframes float-element {

  0%,
  100% {
    transform: translateY(0) rotate(0deg);
    opacity: 0.3;
  }

  50% {
    transform: translateY(-10px) rotate(180deg);
    opacity: 0.6;
  }
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .system-access {
    padding: 1.5rem;
  }

  .main-title {
    font-size: 2.5rem;
  }

  .access-cards {
    grid-template-columns: 1fr;
    gap: 2rem;
    max-width: 400px;
  }

  .access-card {
    padding: 2rem;
  }

  .card-illustration {
    height: 180px;
  }
}

@media (max-width: 480px) {
  .system-access {
    padding: 1rem;
  }

  .main-title {
    font-size: 2rem;
  }

  .subtitle {
    font-size: 1rem;
  }

  .access-card {
    padding: 1.5rem;
  }

  .card-illustration {
    height: 160px;
  }

  .access-btn {
    width: 100%;
    padding: 1rem;
  }
}




.customer-benefits {
  /* background: linear-gradient(180deg, #e0f7fa 0%, #f5f5f5 100%); */
  padding: 4rem 2rem;
  min-height: 50vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* ===== HEADER SECTION ===== */
.header-section {
  text-align: center;
  margin-bottom: 4rem;
  max-width: 600px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #7c4de1, #26c6da);
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 12px rgba(77, 208, 225, 0.3);
}

.star-icon {
  width: 1rem;
  height: 1rem;
}

.main-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #37474f;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.highlight {
  color: #4dd0e1;
}

.description {
  color: #78909c;
  font-size: 1rem;
  line-height: 1.6;
  margin: 0;
}

.accent-text {
  color: #ffa726;
  font-weight: 600;
}

/* ===== CARDS CONTAINER ===== */
.cards-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  width: 100%;
}

/* .cards-containerrr {
  display: grid;
  margin-right: 80%;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 2rem;
  /* max-width: 200px; *  
  width: 10%;
  height: 50%;
  
} */

/* ===== SERVICE CARDS ===== */

.cards-containerrr {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  /* jadi 5 kolom */
  gap: 1.5rem;
  /* jarak antar card */
  padding: 1rem;
}

.service-card {
  background: white;
  border-radius: 16px;
  padding: 1rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.04);
  position: relative;
  overflow: hidden;
}

/* .service-card {
  background: white;
  border-radius: 16px;
  padding: 1rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.04);
  position: relative;
  overflow: hidden;
} */

.service-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #ffa726, #ffb74d);
  transform: scaleX(0);
  transition: transform 0.3s ease;
  transform-origin: left;
}

.service-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.service-card:hover::before {
  transform: scaleX(1);
}

/* ===== CARD CONTENT ===== */
.card-icon {
  width: 4rem;
  height: 4rem;
  background: linear-gradient(135deg, #ffa726, #ffb74d);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 16px rgba(255, 167, 38, 0.3);
  transition: all 0.3s ease;
}



.card-iconn {
  width: 3rem;
  height: 3rem;
  background: linear-gradient(135deg, #ffa726, #ffb74d);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(255, 167, 38, 0.3);
  transition: all 0.3s ease;
  color: #f0f9ff;
}



.service-card:hover .card-icon {
  transform: scale(1.1);
  box-shadow: 0 8px 24px rgba(255, 167, 38, 0.4);
}


.card-text {
  color: #78909c;
  line-height: 1.6;
  margin-bottom: 2rem;
  font-size: 0.95rem;
}

/* ===== CARD BUTTONS ===== */
.card-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #ffa726;
  font-weight: 600;
  font-size: 0.9rem;
  background: none;
  border: 2px solid #ffa726;
  border-radius: 25px;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
}

.card-button:hover {
  background: #ffa726;
  color: white;
  transform: translateX(4px);
  box-shadow: 0 4px 16px rgba(255, 167, 38, 0.3);
}

.button-icon {
  width: 1rem;
  height: 1rem;
  transition: transform 0.3s ease;
}

.card-button:hover .button-icon {
  transform: translateX(2px);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .customer-benefits {
    padding: 3rem 1rem;
  }

  .main-title {
    font-size: 2rem;
  }

  .cards-container {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .service-card {
    padding: 1.5rem;
  }

  .card-icon {
    width: 3.5rem;
    height: 3.5rem;
  }

  .card-icon svg {
    width: 1.25rem;
    height: 1.25rem;
  }

  .card-title {
    font-size: 1.125rem;
  }

  .card-titlee {
    font-size: 1rem;
  }

  .card-text {
    font-size: 0.9rem;
  }
}

@media (max-width: 480px) {
  .customer-benefits {
    padding: 2rem 1rem;
  }

  .main-title {
    font-size: 1.75rem;
  }

  .description {
    font-size: 0.9rem;
  }

  .service-card {
    padding: 1.25rem;
  }

  .card-button {
    width: 100%;
    justify-content: center;
    padding: 1rem 1.5rem;
  }
}

/* ===== ANIMATIONS ===== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.service-card {
  animation: fadeInUp 0.6s ease-out;
}

.service-card:nth-child(1) {
  animation-delay: 0.1s;
}

.service-card:nth-child(2) {
  animation-delay: 0.2s;
}

.service-card:nth-child(3) {
  animation-delay: 0.3s;
}



/* ===== RESET & BASE ===== */
* {
  box-sizing: border-box;
}

/* ===== LAYOUT ===== */
.bg-gradient {
  background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 50%, #ffffff 100%);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.hero-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 100vh;
  padding: 2rem 0;
}

/* ===== CONTENT SECTION ===== */
.content-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  max-width: 550px;
}

.tagline {
  background: linear-gradient(135deg, #7722ee, #9257f7);
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 25px;
  font-size: 0.875rem;
  font-weight: 600;
  display: inline-block;
  align-self: flex-start;
  box-shadow: 0 4px 15px rgba(32, 178, 200, 0.3);
  margin-bottom: 1.5rem;
  animation: slideInUp 0.8s ease-out;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1.1;
  color: #1e293b;
  margin-bottom: 1.5rem;
  animation: slideInUp 0.8s ease-out 0.2s both;
}

.text-highlight {
  background: linear-gradient(135deg, #7722ee, #6927db);
  /* background: linear-gradient(135deg, #22d3ee, #0891b2); */
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-description {
  color: #64748b;
  line-height: 1.7;
  font-size: 1.1rem;
  margin-bottom: 2.5rem;
  animation: slideInUp 0.8s ease-out 0.4s both;
}

/* ===== BUTTONS ===== */
.button-group {
  display: flex;
  gap: 1rem;
  margin-bottom: 3rem;
  animation: slideInUp 0.8s ease-out 0.6s both;
}

.btn {
  display: inline-flex;
  align-items: center;
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
}

.btn-info {
  background: linear-gradient(135deg, #7722ee, #6927db);
  color: white;
  box-shadow: 0 10px 25px rgba(34, 211, 238, 0.3);
}

.btn-info:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(34, 211, 238, 0.4);
}

.btn-secondary {
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  color: #92400e;
  box-shadow: 0 10px 25px rgba(251, 191, 36, 0.3);
}

.btn-secondary:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(251, 191, 36, 0.4);
}

.btn-icon {
  width: 1rem;
  height: 1rem;
  margin-left: 0.5rem;
}

/* ===== FEATURES GRID ===== */
.features-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  animation: slideInUp 0.8s ease-out 0.8s both;
}

.feature-card {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  padding: 1.25rem;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
  transition: all 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 40px rgba(31, 38, 135, 0.15);
}

.feature-card-wide {
  grid-column: 1 / -1;
}

.feature-icon {
  padding: 0.75rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-blue {
  background: #dbeafe;
  color: #2563eb;
}

.icon-green {
  background: #dcfce7;
  color: #16a34a;
}

.icon-purple {
  background: #f3e8ff;
  color: #9333ea;
}

.icon {
  width: 1.25rem;
  height: 1.25rem;
}

.feature-text {
  font-weight: 500;
  color: #374151;
}



/* ===== ANIMATIONS ===== */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {

  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
    opacity: 0.6;
  }

  50% {
    transform: translateY(-15px) rotate(180deg);
    opacity: 0.8;
  }
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1024px) {
  .hero-section {
    gap: 3rem;
  }

  .hero-title {
    font-size: 3rem;
  }

}

@media (max-width: 768px) {
  .hero-section {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
    padding: 1rem 0;
  }

  .content-wrapper {
    max-width: 100%;
    align-items: center;
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .button-group {
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
  }

  .btn {
    width: 100%;
    max-width: 280px;
    justify-content: center;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }


  /* Adjust scene elements for smaller container */
  .character {
    bottom: 80px;
  }




  .folded-section {
    bottom: 60px;
    right: 60px;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 1rem;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-description {
    font-size: 1rem;
  }

}
</style>








<template>
  <div class="bg-info text-white py-2" style="margin-top: 70px;">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="d-flex gap-4 small">
        <span>🏪 <strong>Status Toko:</strong> BUKA</span>
        <span>⏰ <strong>Jam Operasional:</strong> 06.30 - 21.00 WIB</span>
      </div>
      <router-link class="btn btn-light btn-sm rounded-pill fw-semibold text-info" to="/pelanggan">Akses Pelanggan
        →</router-link>
      <!-- <a href="#" class="btn btn-light btn-sm rounded-pill fw-semibold text-info">
        Akses Pelanggan →
      </a> -->
    </div>
  </div>
  <div class="min-h-screen bg-gradient">
    <div class="container">
      <!-- Hero Section -->
      <section class="hero-section">
        <!-- Text Content -->
        <div class="content-wrapper">
          <span class="tagline">Rapi, Bersih, Wangi!</span>

          <h1 class="hero-title">
            <span class="text-highlight">Slaundry</span> Solusi<br>
            Terpercaya untuk<br>
            Pakaian Anda
          </h1>

          <p class="hero-description">
            Slaundry menyediakan layanan cuci dan setrika profesional
            dengan hasil rapi, bersih, dan wangi. Kami menggunakan deterjen
            berkualitas dan teknologi modern untuk merawat pakaian Anda.
          </p>

          <!-- Action Buttons -->
          <div class="button-group">
            <button class="btn btn-info" @click="lihatLayanan">
              Lihat Layanan
              <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            <button class="btn btn-secondary" @click="aksesSystem">
              <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 4a2 2 0 104 0m-4 0a2 2 0 114 0m-4 0v2a2 2 0 002 2h2a2 2 0 002-2v-2">
                </path>
              </svg>
              Akses Sistem
            </button>
          </div>

          <!-- Feature Cards -->
          <div class="features-grid">
            <div class="feature-card">
              <div class="feature-icon icon-blue">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
              </div>
              <span class="feature-text">Layanan Antar Jemput</span>
            </div>

            <div class="feature-card">
              <div class="feature-icon icon-green">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <span class="feature-text">Lacak Pesanan Online</span>
            </div>

            <div class="feature-card feature-card-wide">
              <div class="feature-icon icon-purple">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
              </div>
              <span class="feature-text">Riwayat Transaksi</span>
            </div>
          </div>
        </div>

        <!-- Illustration Section -->
        <div class="illustration-wrapper">
          <img src="/storage/image/hero.png" class="map1" />

        </div>
      </section>

      <div class="customer-benefits">
        <!-- Header Section -->
        <div class="header-section">
          <div class="badge">
            <svg class="star-icon" fill="currentColor" viewBox="0 0 20 20">
              <path
                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.519-4.674z">
              </path>
            </svg>
            Keuntungan Pelanggan
          </div>

          <h2 class="main-title">
            Anda Seorang <span class="text-highlight"> Pelanggan?</span>
          </h2>

          <p class="description">
            Semua kebutuhan pelanggan dapat diakses melalui halaman
            <span class="accent-text">akses sistem</span>
            pelanggan dibawah
          </p>
        </div>

        <!-- Cards Section -->
        <div class="cards-container">
          <!-- Antar Jemput Card -->
          <div class="service-card">
            <div class="card-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
              </svg>
            </div>

            <h3 class="card-title">Antar Jemput</h3>

            <p class="card-text">
              Pesan layanan antar jemput langsung dari akun pelanggan Anda.
              Kami akan menjemput dan mengantar pakaian Anda tepat waktu.
            </p>

            <button class="card-button" @click="pesanSekarang">
              <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                </path>
              </svg>
              Pesan Sekarang
            </button>
          </div>

          <!-- Lacak Pesanan Card -->
          <div class="service-card">
            <div class="card-icon">
              icon
            </div>

            <h3 class="card-title">Lacak Pesanan</h3>

            <p class="card-text">
              Pantau status pesanan laundry Anda secara real-time. Anda dapat
              melihat apakah pakaian sedang dicuci, disetrika, atau siap
              untuk diambil kapan saja.
            </p>

            <button class="card-button" @click="lacakSekarang">
              <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                </path>
              </svg>
              Lacak Sekarang
            </button>
          </div>

          <!-- Riwayat Transaksi Card -->
          <div class="service-card">
            <div class="card-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>

            <h3 class="card-title">Riwayat Transaksi</h3>

            <p class="card-text">
              Akses riwayat transaksi laundry Anda dengan mudah. Lihat detail
              pesanan sebelumnya dan pesan kembali layanan yang sama dengan
              cepat.
            </p>

            <button class="card-button" @click="lihatRiwayat">
              <svg class="button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                </path>
              </svg>
              Lihat Riwayat
            </button>
          </div>
        </div>
      </div>

      <div class="system-access">
        <!-- Header Section -->
        <div class="header-section">
          <div class="back-badge" @click="backToHome">
            <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12">
              </path>
            </svg>
            Masuk ke Sistem
          </div>

          <h1 class="main-title">
            <span class="title-highlight">Akses Sistem</span> Slaundry
          </h1>

          <p class="subtitle">
            Silahkan pilih akses sesuai dengan kebutuhan Anda
          </p>
        </div>

        <!-- Access Cards -->
        <div class="access-cards">
          <!-- Customer Card -->
          <div class="access-card customer-card">
            <div class="card-illustration">
              <div class="orang1">
                <img src="/storage/image/gambarpelanggan.png" class="orang" />
              </div>
            </div>

            <div class="card-content">
              <h2 class="card-title customer-title">Pelanggan</h2>
              <p class="card-description">Pesan layanan dan cek status</p>
              <router-link class="access-btn customer-btn" to="/pelanggan">Masuk</router-link>
              <!-- <button class="access-btn customer-btn" @click="loginCustomer">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 0a4 4 0 01-4 4H6a4 4 0 01-4-4V7a4 4 0 014-4h7a4 4 0 014 4"></path>
                </svg>
                Masuk
              </button> -->
            </div>
          </div>

          <!-- Admin Card -->
          <div class="access-card admin-card">
            <div class="card-illustration">
              <div class="orang1">
                <img src="/storage/image/adminlogin.png" class="orang" />
              </div>
            </div>

            <div class="card-content">
              <h2 class="card-title admin-title">Admin</h2>
              <p class="card-description">Kelola pesanan pelanggan</p>
              <router-link class="access-btn admin-btn" to="/sign-in">Masuk</router-link>
              <!-- <button class="access-btn admin-btn" @click="loginAdmin">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 0a4 4 0 01-4 4H6a4 4 0 01-4-4V7a4 4 0 014-4h7a4 4 0 014 4"></path>
                </svg>
                Masuk
              </button> -->
            </div>
          </div>
        </div>
      </div>




  <div>
          <!-- 🔹 Pilih Laundry -->
          <section class="py-10 px-4 bg-gray-50">
            <h2 class="text-2xl font-semibold text-center mb-6">🧺 Pilih Laundry Terdekat</h2>

            <div v-if="loading" class="text-center text-gray-500">Memuat daftar laundry...</div>

            <div v-else class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6">
              <div v-for="item in mitraList" :key="item.id"
                class="bg-white shadow rounded-xl p-4 hover:shadow-lg transition cursor-pointer"
                @click="bukaDetail(item.id)">
                <img :src="item.foto || '/default-laundry.jpg'" alt="Laundry"
                  class="w-full h-40 object-cover rounded-lg mb-3" />
                <h3 class="text-lg font-bold text-gray-800">{{ item.nama_mitra }}</h3>
                <p class="text-sm text-gray-600">{{ item.alamat || 'Alamat tidak tersedia' }}</p>
                <p class="text-sm text-blue-600 mt-1">👤 {{ item.pemilik }}</p>
              </div>
            </div>
          </section>
        </div>



















      <div class="customer-benefits" id="tentang">
        <!-- Header Section -->
        <div class="header-section">
          <div class="badge">
            <i class="bi bi-exclamation-lg"></i>Tentang Kami
          </div>

          <h2 class="main-title">
            Anda Seorang <span class="text-highlight"> Pelanggan?</span>
          </h2>

          <p class="description">
            Semua kebutuhan pelanggan dapat diakses melalui halaman
            <span class="accent-text">akses sistem</span>
            pelanggan dibawah
          </p>
        </div>

        <!-- Cards Section -->
        <div class="cards-containerrr">
          <!-- Antar Jemput Card -->
          <div class="service-card">
            <div class="card-iconn">
              <i class="fas fa-tshirt"></i>
            </div>
            <h4 class="card-titlee">Kualitas</h4>

            <p class="card-text">
              Kami menggunakan deterjen berkualitas tinggi dan teknologi modern untuk memastikan pakaian Anda bersih,
              wangi, dan terawat dengan baik.
            </p>
          </div>

          <!-- Tepat Waktu-->
          <div class="service-card">
            <div class="card-iconn">
              <i class="bi bi-clock-fill"></i>
            </div>

            <h4 class="card-titlee">Tepat Waktu</h4>

            <p class="card-text">
              Kami menghargai waktu Anda. Layanan kami menjamin pengerjaan dan pengiriman yang tepat waktu sesuai dengan
              jadwal yang dijanjikan.
            </p>
          </div>


          <div class="service-card">
            <div class="card-iconn">
              <i class="fas fa-hand-holding-usd"></i>
            </div>

            <h4 class="card-titlee">Harga Terjangkau</h4>

            <p class="card-text">
              Akses riwayat transaksi laundry Anda dengan mudah. Lihat detail
              pesanan sebelumnya dan pesan kembali layanan yang sama dengan
              cepat.
            </p>
          </div>
          <div class="service-card">
            <div class="card-iconn">
              <i class="fas fa-truck"></i>
            </div>

            <h4 class="card-titlee">Riwayat Transaksi</h4>

            <p class="card-text">
              Akses riwayat transaksi laundry Anda dengan mudah. Lihat detail
              pesanan sebelumnya dan pesan kembali layanan yang sama dengan
              cepat.
            </p>
          </div>
          <div class="service-card">
            <div class="card-iconn">
              <i class="fas fa-shield-alt"></i>
            </div>

            <h4 class="card-titlee">Riwayat Transaksi</h4>

            <p class="card-text">
              Akses riwayat transaksi laundry Anda dengan mudah. Lihat detail
              pesanan sebelumnya dan pesan kembali layanan yang sama dengan
              cepat.
            </p>
          </div>
        </div>
      </div>






      <div class="laundry-servicess" id="layanan">
        <!-- Header Section -->
        <div class="header-sectionn">
          <div class="badge">
            <i class="bi bi-journal-text"></i>
            Layanan Kami
          </div>

          <h2 class="main-title">
            Paket <span class="text-highlight">Layanan</span> Kami
          </h2>

          <p class="description">
            Kami menyediakan berbagai layanan laundry untuk memenuhi kebutuhan
            Anda. Berikut adalah paket layanan yang kami tawarkan.
          </p>
        </div>

        <div class="services-grid">
          <div v-for="(item, i) in services" :key="i" class="service-simple-card" @click="pesanSekarang(item.slug)">
            <img :src="item.icon" class="service-icon" />
            <h3 class="service-name">{{ item.name }}</h3>
          </div>
        </div>
      </div>





    </div>
  </div>

  <section class="cta-section text-center text-light py-10">
    <div class="container py-10">
      <h2 class="fw-bold mb-2">Siap Untuk Mencoba Layanan Kami?</h2>
      <p class="mb-4">
        Hubungi kami sekarang untuk mendapatkan layanan laundry terbaik. Kami siap melayani
        kebutuhan laundry Anda dengan profesional.
      </p>
      <a href="https://wa.me/681231365656" target="_blank" rel="noopener"
        class="btn btn-light text-primary fw-bold px-4">
        <i class="bi bi-whatsapp me-1"></i> Hubungi Kami Sekarang
      </a>
    </div>
  </section>
  <footer class="footer-dark text-light pt-5 pb-4">
    <div class="container">
      <div class="row gy-4">
        <!-- Brand -->
        <div class="col-md-4">
          <h5 class="ligo fw-bold mb-2">
            <!-- <img src="/storage/image/logo.png" alt="Logo" class="logo me-1" /> -->
            SLaundry
          </h5>
          <p class="small mb-0">
            SLaundry adalah penyedia jasa laundry profesional yang berkomitmen memberikan layanan
            terbaik untuk semua kebutuhan laundry Anda.
          </p>
        </div>

        <!-- Tautan -->
        <div class="col-md-2">
          <h6 class="fw-bold mb-3 section-title">Tautan</h6>
          <ul class="list-unstyled small">
            <li><router-link to="beranda" class="footer-link">Beranda</router-link></li>
            <li><router-link to="tentang" class="footer-link">Tentang Kami</router-link></li>
            <li><router-link to="/layanan" class="footer-link">Layanan</router-link></li>
            <li><router-link to="/lokasi" class="footer-link">Lokasi</router-link></li>
            <li><router-link to="/login" class="footer-link">Akses Sistem</router-link></li>
          </ul>
        </div>


    

        <!-- Layanan -->
        <div class="col-md-3">
          <h6 class="fw-bold mb-3 section-title">Layanan</h6>
          <ul class="list-unstyled small">
            <li>Cuci Kering</li>
            <li>Cuci Setrika</li>
            <li>Setrika Saja</li>
            <li>Cuci Custom</li>
          </ul>
        </div>

        <!-- Kontak -->
        <div class="col-md-3">
          <h6 class="fw-bold mb-3 section-title">Kontak Kami</h6>
          <ul class="list-unstyled small mb-0">
            <li class="mb-2 d-flex">
              <i class="bi bi-geo-alt-fill text-info me-2"></i>
              Surabaya, Indonesia
            </li>
            <li class="d-flex">
              <i class="bi bi-telephone-fill text-info me-2"></i>
              +62 812-3456-789
            </li>
          </ul>
        </div>
      </div>

      <hr class="divider my-4" />
      <div class="text-center small text-secondary">
        © {{ new Date().getFullYear() }} SLaundry. All Rights Reserved.
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">

// import { ref } from "vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";



const mitraList = ref([]);
const loading = ref(true);
const router = useRouter();

const fetchMitra = async () => {
  try {
    const res = await axios.get("/mitra/all");
    mitraList.value = res.data;
  } catch (error) {
    console.error("Gagal mengambil data mitra:", error);
  } finally {
    loading.value = false;
  }
};

const bukaDetail = (id) => {
  router.push(`/laundry/${id}`);
};

onMounted(() => {
  fetchMitra();
});






const services = ref([
  { name: "Daily Kiloan", slug: "daily-kiloan", icon: "/storage/image/kiloan.png" },
  { name: "Cuci & Setrika", slug: "cuci-setrika", icon: "/storage/image/cuci_setrika.png" },
  { name: "Laundry Sepatu", slug: "laundry-sepatu", icon: "/storage/image/sepatu.png" },
  { name: "Laundry Tas", slug: "laundry-tas", icon: "/storage/image/tas.png" },
  { name: "Laundry Sarung Bantal", slug: "laundry-karpet", icon: "/storage/image/karpet.png" },
  { name: "Laundry Bed Cover ", slug: "laundry-gorden", icon: "/storage/image/gorden.png" },
  { name: "Laundry Sprei", slug: "laundry-stroller", icon: "/storage/image/stroller.png" },
  { name: "Laundry Boneka", slug: "laundry-boneka", icon: "/storage/image/boneka.png" },
]);

const pesanSekarang = (slug: string) => {
  console.log("Pesan layanan:", slug);
  // arahkan ke halaman pemesanan / kirim event sesuai kebutuhan
};
// import TentangAdmin from '..tre'
// const lihatLayanan = (): void => {
//   console.log('Navigating to services...')
//   // Add navigation logic here
// }

const aksesSystem = (): void => {
  console.log('Accessing system...')
  // Add system access logic here
}

// const pesanSekarang = (): void => {
//   console.log('Navigating to booking page...')
//   // Add your routing logic here
//   // Example: router.push('/pesan-antar-jemput')
// }

const lacakSekarang = (): void => {
  console.log('Navigating to tracking page...')
  // Add your routing logic here
  // Example: router.push('/lacak-pesanan')
}

const lihatRiwayat = (): void => {
  console.log('Navigating to history page...')
  // Add your routing logic here
  // Example: router.push('/riwayat-transaksi')
}

const backToHome = (): void => {
  console.log('Navigating back to home...')
  // Add navigation logic here
  // Example: router.push('/')
}

const loginCustomer = (): void => {
  console.log('Navigating to customer login...')
  // Add customer login logic here
  // Example: router.push('/login/customer')
}

const loginAdmin = (): void => {
  console.log('Navigating to admin login...')
  // Add admin login logic here
  // Example: router.push('/login/admin')
}

// export default {
//   name: 'LaundryServices',
//   methods: {
//     pesanSekarang(serviceType) {
//       // Handle order logic here
//       console.log('Pesan layanan:', serviceType);
//       // You can emit an event or navigate to order page
//       this.$emit('order-service', serviceType);
//     }
//   }
// }
</script>

<style scoped>
.services-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  /* 4 kolom */
  gap: 1rem;
  /* jarak antar card */
}

.service-simple-card {
  background: #fff;
  border-radius: 16px;
  padding: 1.5rem 1rem;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.25s ease;
  cursor: pointer;
}

.service-simple-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.service-icon {
  width: 90px;
  height: 90px;
  object-fit: contain;
  margin-bottom: 1rem;
}

.service-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: #008b8b;
  /* warna teal seperti contoh */
}


.footer-dark {
  background-color: #2e2e2e;
}

/* .logo {
  height: 28px;
  color: #ffffff;
} */
.ligo {
  height: 28px;
  color: #ffffff;
}

.section-title {
  border-bottom: 2px solid #0dcaf0;
  padding-bottom: 4px;
  color: #e5d9d9;
}

.footer-link {
  color: #ccc;
  text-decoration: none;
  display: inline-block;
  margin-bottom: 6px;
}

.footer-link:hover {
  color: #0dcaf0;
}

.divider {
  border-color: rgba(255, 255, 255, 0.1);
}

.cta-section {
  background: linear-gradient(135deg, #3434f6 0%, #28d7d7 100%);
  padding: 60px 0;
  /* tinggi area */
  margin-top: 60px;
  /* jarak ke section atas */
}







.orang {
  width: 150px;
  height: 200px;
  object-fit: contain;
  border-radius: 20px;
}

.orang1 {
  width: 200px;
  height: 180px;
  margin: 0 auto 24px;
  margin-top: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #98b3ef;
  border-radius: 20px;
}

.map1 {
  width: 500px;
  height: 500px;
  object-fit: contain;
  border-radius: 20px;
}

.map-image {
  width: 70px;
  height: 70px;
  object-fit: contain;
}

.card-icon-image {
  width: 100px;
  height: 100px;
  margin: 0 auto 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(79, 209, 197, 0.1) 0%, rgba(54, 209, 220, 0.1) 100%);
  border-radius: 20px;
}

.laundry-servicess {
  max-width: 1200px;
  margin: 0 auto;
  /* padding: 40px 40px; */
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}


.header-sectionn {
  text-align: center;
  margin-bottom: 60px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 0 20px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(79, 209, 197, 0.1);
  color: #4fd1c5;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 20px;
  border: 1px solid rgba(79, 209, 197, 0.2);
  justify-self: center;
}

.badge i {
  font-size: 16px;
  color: #f3e8ff;
}

.star-icon {
  width: 16px;
  height: 16px;
}

.main-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #2d3748;
  margin-bottom: 16px;
  line-height: 1.2;
}

.text-highlight {
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.description {
  font-size: 1.1rem;
  color: #718096;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
  text-align: center;
}

/* Services Container */
.services-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  margin-top: 40px;
  /* width: 90%; */
  justify-items: center;
}

/* Service Card */
.service-card {
  background: white;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(226, 232, 240, 0.8);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.service-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.service-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
}

/* Card Badge */
.card-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  padding: 6px 12px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.card-badge.popular {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.card-badge.terlaris {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.card-badge.praktis {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
}

/* Card Icon */
.card-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(79, 209, 197, 0.1) 0%, rgba(54, 209, 220, 0.1) 100%);
  border-radius: 20px;
}


.service-image {
  width: 50px;
  height: 50px;
  object-fit: contain;
}

/* Card Title */
.card-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d3748;
  /* color: #2d3748; */
  text-align: center;
  margin-bottom: 16px;
}

/* Price Section */
.price-section {
  text-align: center;
  margin-bottom: 20px;
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 4px;
}

.price {
  font-size: 2rem;
  font-weight: 800;
  color: #2d3748;
}

.price-unit {
  font-size: 1.1rem;
  color: #718096;
  font-weight: 500;
}

.price-type {
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
  color: white;
  padding: 4px 8px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  margin-left: 8px;
}

/* Card Description */
.card-description {
  color: #718096;
  line-height: 1.6;
  margin-bottom: 24px;
  text-align: center;
}

/* Features List */
.features-list {
  list-style: none;
  margin-bottom: 30px;
}

.features-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
  color: #4a5568;
  font-size: 14px;
}

.check-icon::before {
  content: '✓';
  width: 20px;
  height: 20px;
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
  flex-shrink: 0;
}

/* Order Button */
.order-button {
  width: 100%;
  background: linear-gradient(135deg, #4fd1c5 0%, #36d1dc 100%);
  color: white;
  border: none;
  padding: 14px 20px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.order-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(79, 209, 197, 0.3);
}

.order-button:active {
  transform: translateY(0);
}

.button-icon {
  width: 20px;
  height: 20px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .laundry-services {
    padding: 20px 15px;
  }

  .main-title {
    font-size: 2rem;
  }

  .services-container {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .service-card {
    padding: 24px;
  }

  .price {
    font-size: 1.8rem;
  }
}












html {
  scroll-behavior: smooth;
}

.system-access {
  min-height: 100vh;
  /* background: linear-gradient(135deg, #e8f5f3 0%, #f0f9ff 50%, #ffffff 100%); */
  padding: 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* ===== HEADER SECTION ===== */
/* .header-section {
  text-align: center;
  margin-bottom: 4rem;
  max-width: 600px;
} */

.back-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #7042e4, #8579ff);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 2rem;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(77, 208, 225, 0.3);
}

.back-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(77, 208, 225, 0.4);
}

.back-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.main-title {
  font-size: 3rem;
  font-weight: 700;
  color: #37474f;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.title-highlight {
  color: #7042e4;
  /* color: #4dd0e1; */
}

.subtitle {
  color: #78909c;
  font-size: 1.1rem;
  margin: 0;
}

/* ===== ACCESS CARDS ===== */
.access-cards {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 3rem;
  max-width: 800px;
  width: 800%;
}

.access-card {
  background: white;
  border-radius: 20px;
  padding: 1rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  text-align: center;
  position: relative;
  overflow: hidden;
}

.access-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  transform: scaleX(0);
  transition: transform 0.4s ease;
  transform-origin: left;
}

.customer-card::before {
  background: linear-gradient(135deg, #4dd0e1, #26c6da);
}

.admin-card::before {
  background: linear-gradient(135deg, #ffa726, #ff9800);
}

.access-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.access-card:hover::before {
  transform: scaleX(1);
}

/* ===== CARD ILLUSTRATIONS ===== */
.card-illustration {
  height: 200px;
  margin-bottom: 2rem;
  position: relative;
  border-radius: 16px;
  overflow: hidden;
}



.customer-title {
  color: #4dd0e1;
}


.card-description {
  color: #78909c;
  margin-bottom: 2rem;
  font-size: 0.95rem;
}

/* ===== ACCESS BUTTONS ===== */
.access-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem 2rem;
  border-radius: 25px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  color: white;
  min-width: 120px;
  justify-content: center;
}

.customer-btn {
  background: linear-gradient(135deg, #4dd0e1, #26c6da);
  box-shadow: 0 4px 15px rgba(77, 208, 225, 0.3);
}

.customer-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(77, 208, 225, 0.4);
}

.admin-btn {
  background: linear-gradient(135deg, #ffa726, #ff9800);
  box-shadow: 0 4px 15px rgba(255, 167, 38, 0.3);
}

.admin-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 167, 38, 0.4);
}

.btn-icon {
  width: 1rem;
  height: 1rem;
}

/* ===== ANIMATIONS ===== */
@keyframes steam-rise {
  0% {
    transform: translateY(0) scale(0.8);
    opacity: 0.6;
  }

  50% {
    transform: translateY(-15px) scale(1);
    opacity: 0.3;
  }

  100% {
    transform: translateY(-30px) scale(1.2);
    opacity: 0;
  }
}

@keyframes float-element {

  0%,
  100% {
    transform: translateY(0) rotate(0deg);
    opacity: 0.3;
  }

  50% {
    transform: translateY(-10px) rotate(180deg);
    opacity: 0.6;
  }
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .system-access {
    padding: 1.5rem;
  }

  .main-title {
    font-size: 2.5rem;
  }

  .access-cards {
    grid-template-columns: 1fr;
    gap: 2rem;
    max-width: 400px;
  }

  .access-card {
    padding: 2rem;
  }

  .card-illustration {
    height: 180px;
  }
}

@media (max-width: 480px) {
  .system-access {
    padding: 1rem;
  }

  .main-title {
    font-size: 2rem;
  }

  .subtitle {
    font-size: 1rem;
  }

  .access-card {
    padding: 1.5rem;
  }

  .card-illustration {
    height: 160px;
  }

  .access-btn {
    width: 100%;
    padding: 1rem;
  }
}




.customer-benefits {
  /* background: linear-gradient(180deg, #e0f7fa 0%, #f5f5f5 100%); */
  padding: 4rem 2rem;
  min-height: 50vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* ===== HEADER SECTION ===== */
.header-section {
  text-align: center;
  margin-bottom: 4rem;
  max-width: 600px;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #7c4de1, #26c6da);
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 12px rgba(77, 208, 225, 0.3);
}

.star-icon {
  width: 1rem;
  height: 1rem;
}

.main-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #37474f;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.highlight {
  color: #4dd0e1;
}

.description {
  color: #78909c;
  font-size: 1rem;
  line-height: 1.6;
  margin: 0;
}

.accent-text {
  color: #ffa726;
  font-weight: 600;
}

/* ===== CARDS CONTAINER ===== */
.cards-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  width: 100%;
}

/* .cards-containerrr {
  display: grid;
  margin-right: 80%;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 2rem;
  /* max-width: 200px; *  
  width: 10%;
  height: 50%;
  
} */

/* ===== SERVICE CARDS ===== */

.cards-containerrr {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  /* jadi 5 kolom */
  gap: 1.5rem;
  /* jarak antar card */
  padding: 1rem;
}

.service-card {
  background: white;
  border-radius: 16px;
  padding: 1rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.04);
  position: relative;
  overflow: hidden;
}

/* .service-card {
  background: white;
  border-radius: 16px;
  padding: 1rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.04);
  position: relative;
  overflow: hidden;
} */

.service-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #ffa726, #ffb74d);
  transform: scaleX(0);
  transition: transform 0.3s ease;
  transform-origin: left;
}

.service-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.service-card:hover::before {
  transform: scaleX(1);
}

/* ===== CARD CONTENT ===== */
.card-icon {
  width: 4rem;
  height: 4rem;
  background: linear-gradient(135deg, #ffa726, #ffb74d);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 16px rgba(255, 167, 38, 0.3);
  transition: all 0.3s ease;
}



.card-iconn {
  width: 3rem;
  height: 3rem;
  background: linear-gradient(135deg, #ffa726, #ffb74d);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(255, 167, 38, 0.3);
  transition: all 0.3s ease;
  color: #f0f9ff;
}



.service-card:hover .card-icon {
  transform: scale(1.1);
  box-shadow: 0 8px 24px rgba(255, 167, 38, 0.4);
}


.card-text {
  color: #78909c;
  line-height: 1.6;
  margin-bottom: 2rem;
  font-size: 0.95rem;
}

/* ===== CARD BUTTONS ===== */
.card-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #ffa726;
  font-weight: 600;
  font-size: 0.9rem;
  background: none;
  border: 2px solid #ffa726;
  border-radius: 25px;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
}

.card-button:hover {
  background: #ffa726;
  color: white;
  transform: translateX(4px);
  box-shadow: 0 4px 16px rgba(255, 167, 38, 0.3);
}

.button-icon {
  width: 1rem;
  height: 1rem;
  transition: transform 0.3s ease;
}

.card-button:hover .button-icon {
  transform: translateX(2px);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .customer-benefits {
    padding: 3rem 1rem;
  }

  .main-title {
    font-size: 2rem;
  }

  .cards-container {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .service-card {
    padding: 1.5rem;
  }

  .card-icon {
    width: 3.5rem;
    height: 3.5rem;
  }

  .card-icon svg {
    width: 1.25rem;
    height: 1.25rem;
  }

  .card-title {
    font-size: 1.125rem;
  }

  .card-titlee {
    font-size: 1rem;
  }

  .card-text {
    font-size: 0.9rem;
  }
}

@media (max-width: 480px) {
  .customer-benefits {
    padding: 2rem 1rem;
  }

  .main-title {
    font-size: 1.75rem;
  }

  .description {
    font-size: 0.9rem;
  }

  .service-card {
    padding: 1.25rem;
  }

  .card-button {
    width: 100%;
    justify-content: center;
    padding: 1rem 1.5rem;
  }
}

/* ===== ANIMATIONS ===== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.service-card {
  animation: fadeInUp 0.6s ease-out;
}

.service-card:nth-child(1) {
  animation-delay: 0.1s;
}

.service-card:nth-child(2) {
  animation-delay: 0.2s;
}

.service-card:nth-child(3) {
  animation-delay: 0.3s;
}



/* ===== RESET & BASE ===== */
* {
  box-sizing: border-box;
}

/* ===== LAYOUT ===== */
.bg-gradient {
  background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 50%, #ffffff 100%);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.hero-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  min-height: 100vh;
  padding: 2rem 0;
}

/* ===== CONTENT SECTION ===== */
.content-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  max-width: 550px;
}

.tagline {
  background: linear-gradient(135deg, #7722ee, #9257f7);
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 25px;
  font-size: 0.875rem;
  font-weight: 600;
  display: inline-block;
  align-self: flex-start;
  box-shadow: 0 4px 15px rgba(32, 178, 200, 0.3);
  margin-bottom: 1.5rem;
  animation: slideInUp 0.8s ease-out;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1.1;
  color: #1e293b;
  margin-bottom: 1.5rem;
  animation: slideInUp 0.8s ease-out 0.2s both;
}

.text-highlight {
  background: linear-gradient(135deg, #7722ee, #6927db);
  /* background: linear-gradient(135deg, #22d3ee, #0891b2); */
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-description {
  color: #64748b;
  line-height: 1.7;
  font-size: 1.1rem;
  margin-bottom: 2.5rem;
  animation: slideInUp 0.8s ease-out 0.4s both;
}

/* ===== BUTTONS ===== */
.button-group {
  display: flex;
  gap: 1rem;
  margin-bottom: 3rem;
  animation: slideInUp 0.8s ease-out 0.6s both;
}

.btn {
  display: inline-flex;
  align-items: center;
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
}

.btn-info {
  background: linear-gradient(135deg, #7722ee, #6927db);
  color: white;
  box-shadow: 0 10px 25px rgba(34, 211, 238, 0.3);
}

.btn-info:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(34, 211, 238, 0.4);
}

.btn-secondary {
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  color: #92400e;
  box-shadow: 0 10px 25px rgba(251, 191, 36, 0.3);
}

.btn-secondary:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(251, 191, 36, 0.4);
}

.btn-icon {
  width: 1rem;
  height: 1rem;
  margin-left: 0.5rem;
}

/* ===== FEATURES GRID ===== */
.features-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  animation: slideInUp 0.8s ease-out 0.8s both;
}

.feature-card {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  padding: 1.25rem;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
  transition: all 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 40px rgba(31, 38, 135, 0.15);
}

.feature-card-wide {
  grid-column: 1 / -1;
}

.feature-icon {
  padding: 0.75rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-blue {
  background: #dbeafe;
  color: #2563eb;
}

.icon-green {
  background: #dcfce7;
  color: #16a34a;
}

.icon-purple {
  background: #f3e8ff;
  color: #9333ea;
}

.icon {
  width: 1.25rem;
  height: 1.25rem;
}

.feature-text {
  font-weight: 500;
  color: #374151;
}



/* ===== ANIMATIONS ===== */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {

  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
    opacity: 0.6;
  }

  50% {
    transform: translateY(-15px) rotate(180deg);
    opacity: 0.8;
  }
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1024px) {
  .hero-section {
    gap: 3rem;
  }

  .hero-title {
    font-size: 3rem;
  }

}

@media (max-width: 768px) {
  .hero-section {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
    padding: 1rem 0;
  }

  .content-wrapper {
    max-width: 100%;
    align-items: center;
  }

  .hero-title {
    font-size: 2.5rem;
  }

  .button-group {
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
  }

  .btn {
    width: 100%;
    max-width: 280px;
    justify-content: center;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }


  /* Adjust scene elements for smaller container */
  .character {
    bottom: 80px;
  }




  .folded-section {
    bottom: 60px;
    right: 60px;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 1rem;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-description {
    font-size: 1rem;
  }

}
</style>