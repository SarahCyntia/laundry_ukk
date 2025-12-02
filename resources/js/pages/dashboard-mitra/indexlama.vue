<!-- <template>
  <div class="dashboard-mitra">
    <h2>Dashboard Mitra</h2>

    <div class="cards">
      <div class="card">
        <h3>Order Masuk</h3>
        <p>{{ summary.menunggu_konfirmasi }}</p>
      </div>

      <div class="card">
        <h3>Diproses</h3>
        <p>{{ summary.diproses }}</p>
      </div>

      <div class="card">
        <h3>Siap Diambil</h3>
        <p>{{ summary.siap_diambil }}</p>
      </div>

      <div class="card">
        <h3>Selesai Hari Ini</h3>
        <p>{{ summary.selesai }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const notif = ref(false);
const jumlahOrderBaru = ref(0);

// const checkNotif = async () => {
//   const res = await axios.get("/mitra/notif-order-count");
//   jumlahOrderBaru.value = res.data.count;
// };

const checkNotif = async () => {
  const res = await axios.get("/mitra/notif-order");
  if (res.data.new_order && !notif.value) {
    notif.value = true;
    toast.info("📢 Ada order baru masuk!", { autoClose: 3000 });
  }
};

onMounted(() => {
  setInterval(checkNotif, 20000); // 20 detik
});


const summary = ref({
  menunggu_konfirmasi: 0,
  diproses: 0,
  siap_diambil: 0,
  selesai: 0,
});

const loadSummary = async () => {
  try {
    const res = await axios.get("/mitra/summary");

    // supaya aman kalau backend tidak lengkap
    summary.value = {
      menunggu_konfirmasi: res.data.menunggu_konfirmasi ?? 0,
      diproses: res.data.diproses ?? 0,
      siap_diambil: res.data.siap_diambil ?? 0,
      selesai: res.data.selesai ?? 0,
    };
  } catch (e) {
    console.error("Gagal load summary", e);
  }
};






onMounted(() => {
  loadSummary();
});
</script>

<style scoped>
.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem;
  margin-top: 20px;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0px 3px 10px rgba(0,0,0,0.12);
  text-align: center;
}

.card h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.card p {
  margin-top: 8px;
  font-size: 28px;
  font-weight: bold;
}
</style> -->







<template>
  <div class="landing-page">
    
    <section id="hero" class="hero-section position-relative text-white d-flex align-items-center">
      <div class="hero-gradient-bg"></div>

      <div id="heroCarousel" class="carousel slide carousel-fade w-100 h-100" data-bs-ride="carousel" data-bs-interval="5000">
        
        <div class="carousel-indicators mb-5">
          <button v-for="(slide, index) in heroSlides" :key="index" type="button" 
            data-bs-target="#heroCarousel" :data-bs-slide-to="index" 
            :class="{ active: index === 0 }"
            class="custom-indicator"></button>
        </div>
        
        <div class="carousel-inner h-100">
          <div v-for="(slide, index) in heroSlides" :key="index" 
            class="carousel-item h-100" 
            :class="{ active: index === 0 }">
            
            <div class="container h-100 d-flex align-items-center position-relative z-2">
              <div class="row w-100">
                <div class="col-lg-8 col-xl-7">
                  <div class="hero-content ps-lg-4 pt-5">
                    <span class="badge bg-white text-primary fw-bold px-3 py-2 mb-4 rounded-pill animate-fade-down shadow-sm">
                      <i class="fas fa-star me-2"></i>Platform Sewa No. 1
                    </span>
                    
                    <h1 class="display-3 fw-bolder mb-4 animate-title lh-sm text-white">
                      {{ slide.title }}
                    </h1>
                    
                    <p class="lead mb-5 text-white opacity-75 animate-subtitle fs-5" style="max-width: 600px;">
                      {{ slide.subtitle }}
                    </p>
                    
                    <div class="d-flex flex-wrap gap-3 animate-buttons">
                      <router-link to="/dashboard_pengguna" class="btn btn-light btn-lg rounded-pill px-5 py-3 shadow-lg btn-hover-lift text-primary fw-bold">
                        Sewa Sekarang <i class="fas fa-arrow-right ms-2"></i>
                      </router-link>
                      <a href="#unggulan" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3 btn-hover-lift">
                        Lihat Katalog
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="stats-section position-relative">
      <div class="container">
        <div class="stats-container bg-white shadow-xl rounded-4 p-4 p-lg-5 mt-n5 position-relative z-3">
          <div class="row g-4 divide-lg">
            <div class="col-6 col-lg-3 text-center">
              <div class="stat-item p-2">
                <div class="text-primary mb-2 display-6"><i class="fas fa-box-open"></i></div>
                <h3 class="fw-bolder mb-0 count-up">500+</h3>
                <small class="text-muted text-uppercase fw-bold ls-1">Produk Tersedia</small>
              </div>
            </div>
            <div class="col-6 col-lg-3 text-center">
              <div class="stat-item p-2">
                <div class="text-primary mb-2 display-6"><i class="fas fa-users"></i></div>
                <h3 class="fw-bolder mb-0">1000+</h3>
                <small class="text-muted text-uppercase fw-bold ls-1">Pelanggan Puas</small>
              </div>
            </div>
            <div class="col-6 col-lg-3 text-center">
              <div class="stat-item p-2">
                <div class="text-primary mb-2 display-6"><i class="fas fa-headset"></i></div>
                <h3 class="fw-bolder mb-0">24/7</h3>
                <small class="text-muted text-uppercase fw-bold ls-1">Support Siaga</small>
              </div>
            </div>
            <div class="col-6 col-lg-3 text-center">
              <div class="stat-item p-2">
                <div class="text-primary mb-2 display-6"><i class="fas fa-smile"></i></div>
                <h3 class="fw-bolder mb-0">98%</h3>
                <small class="text-muted text-uppercase fw-bold ls-1">Rating Bagus</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <hr class="mt-5"/>

    <section id="kategori" class="py-6 section-spacing">
      <div class="container">
        <div class="text-center mb-5 mw-700 mx-auto">
          <h6 class="text-primary fw-bold text-uppercase ls-2">Eksplorasi</h6>
          <h2 class="display-5 fw-bold mb-3 text-dark">Kategori Populer</h2>
          <div class="divider mx-auto bg-primary mb-3"></div>
          <p class="text-muted">Temukan peralatan elektronik terbaik sesuai kebutuhan acara Anda.</p>
        </div>
        
        <div class="row g-4 justify-content-center">
          <div v-for="(kategori, i) in kategoriList.slice(0, 6)" :key="kategori.id || i" class="col-6 col-md-4 col-lg-2">
            <div class="card category-card border-0 h-100 text-center shadow-sm">
              <div class="card-body py-4 d-flex flex-column align-items-center justify-content-center">
                <div class="icon-circle bg-light text-primary mb-3 rounded-circle">
                  <i class="fas fa-layer-group fa-lg"></i>
                </div>
                <h6 class="fw-bold text-dark mb-0">{{ kategori.name }}</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="unggulan" class="py-6">
      <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
          <div>
            <h6 class="text-primary fw-bold text-uppercase ls-2">Pilihan Terbaik</h6>
            <h2 class="display-5 fw-bold text-dark mb-0">Barang Unggulan</h2>
          </div>
          <router-link to="/dashboard_pengguna" class="btn btn-outline-primary rounded-pill px-4 d-none d-md-inline-block">
            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
          </router-link>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-4" v-for="item in items" :key="item.id">
            <div class="card product-card border-0 h-100 shadow-sm overflow-hidden group">
              <div class="position-relative product-img-wrap">
                <span class="badge bg-danger position-absolute top-0 start-0 m-3 z-2">Hot Item</span>
                <img :src="item.image || 'https://via.placeholder.com/400x300?text=Produk'" 
                  class="card-img-top object-fit-cover" 
                  style="height: 280px;"
                  :alt="item.name" />
                
                <div class="product-action d-flex justify-content-center gap-2 align-items-center">
                   <button class="btn btn-white rounded-circle shadow-sm" title="Lihat Detail"><i class="fas fa-eye text-dark"></i></button>
                   <button class="btn btn-primary rounded-circle shadow-sm" title="Sewa Sekarang"><i class="fas fa-shopping-cart"></i></button>
                </div>
              </div>
              
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <small class="text-muted text-uppercase">{{ item.category || 'Elektronik' }}</small>
                  <div class="text-warning small">
                    <i class="fas fa-star"></i> 4.9
                  </div>
                </div>
                <h5 class="card-title fw-bold mb-3 text-truncate">{{ item.name }}</h5>
                <div class="d-flex align-items-center justify-content-between mt-auto">
                   <div>
                     <span class="text-primary fw-bolder fs-4">Rp {{ item.price.toLocaleString('id-ID') }}</span>
                     <small class="text-muted">/hari</small>
                   </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <hr/>

    <section id="testimoni" class="py-6 bg-light position-relative overflow-hidden">
      <div class="container position-relative z-2">
        <div class="text-center mb-5">
          <h2 class="display-5 fw-bold text-dark">Apa Kata Mereka?</h2>
          <p class="text-muted">Testimoni asli dari pelanggan yang puas dengan layanan kami.</p>
        </div>

        <div class="row justify-content-center g-4">
          <div class="col-md-6 col-lg-5" v-for="(testi, i) in testimonials" :key="i">
            <div class="card border-0 shadow-lg h-100 testimonial-card p-5">
              <div class="card-body position-relative p-0">
                <i class="fas fa-quote-right quote-icon text-light-gray display-1 position-absolute top-0 end-0 mt-2 me-3 opacity-25"></i>
                <div class="mb-4 text-warning">
                  <i class="fas fa-star" v-for="n in 5" :key="n"></i>
                </div>
                <p class="card-text fs-5 fst-italic text-dark mb-4">"{{ testi.kesan }}"</p>
                <div class="d-flex align-items-center mt-auto">
                  <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 me-3" style="width: 50px; height: 50px;">
                    {{ testi.nama.charAt(0) }}
                  </div>
                  <div>
                    <h6 class="mb-0 fw-bolder text-dark">{{ testi.nama.split(',')[0] }}</h6>
                    <small class="text-muted">{{ testi.nama.split(',')[1] || 'Pelanggan Setia' }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="bg-dark text-white py-4 text-center mt-5">
      <div class="container">
        <small class="opacity-50">&copy; 2024 Platform Sewa Elektronik. All rights reserved.</small>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const kategoriList = ref([]); // Tambahkan lagi untuk Kategori Section

const items = [
  {
    id: 1,
    name: "Kamera DSLR Canon EOS 1500D",
    price: 150000,
    // Mengembalikan path gambar lama Anda, pastikan path ini benar di server Anda
    image: "/storage/photo/camera.jpeg",
    category: "Fotografi", 
  },
  {
    id: 2,
    name: "Laptop Dell Inspiron 15 Core i7",
    price: 200000,
    image: "/storage/photo/laptopp.jpeg",
    category: "Komputer",
  },
  {
    id: 3,
    name: "Proyektor Epson High Brightness",
    price: 180000,
    image: "/storage/photo/proyektor.jpeg",
    category: "Presentasi",
  },
];

const testimonials = [
  {
    nama: "Andi Prasetyo, Jakarta",
    kesan: "Respon admin cepat, barang bersih dan baterai penuh saat diterima. Recommended banget buat fotografer freelance!",
  },
  {
    nama: "Siska Rahmawati, Bandung",
    kesan: "Sangat terbantu untuk event kantor dadakan. Laptop spesifikasi tinggi dengan harga sewa yang sangat masuk akal.",
  },
];

onMounted(async () => {
  try {
    const response = await axios.get("/kategori-list");
    kategoriList.value = response.data;
  } catch (error) {
    // Data dummy untuk Kategori jika API gagal
    kategoriList.value = [
        { name: 'Kamera' }, { name: 'Laptop' }, { name: 'Audio' }, 
        { name: 'Drone' }, { name: 'Lighting' }, { name: 'Gaming' }
    ];
    console.error("Gagal memuat kategori (Using Dummy Data):", error);
  }
});

const heroSlides = [
  {
    title: "Tangkap Momen Terbaik",
    subtitle: "Sewa kamera profesional dan lensa premium untuk hasil foto yang memukau tanpa harus membeli.",
  },
  {
    title: "Produktivitas Tanpa Batas",
    subtitle: "Laptop high-end siap pakai untuk kebutuhan kerja, desain, hingga editing video berat.",
  },
  {
    title: "Presentasi Lebih Hidup",
    subtitle: "Proyektor kualitas HD untuk seminar, nonton bareng, atau presentasi bisnis yang meyakinkan.",
  },
];
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap');

.landing-page {
  font-family: 'Poppins', sans-serif;
  overflow-x: hidden;
  background-color: #f8f9fa;
}

/* Utilities */
.py-6 { padding-top: 5rem; padding-bottom: 5rem; }
.ls-1 { letter-spacing: 1px; }
.ls-2 { letter-spacing: 2px; }
.mt-n5 { margin-top: -4rem !important; }
.text-light-gray { color: #e9ecef; }
.object-fit-cover { object-fit: cover; }
.shadow-xl { box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important; }

/* --- HERO SECTION REVISI (GRADIENT ONLY) --- */
.hero-section {
  position: relative;
  height: 80vh; 
  min-height: 550px;
  overflow: hidden;
}

.hero-gradient-bg {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(135deg, #0b2e75 0%, #3b82f6 100%);
  z-index: 0;
}

/* --- STATS SECTION --- */
.divide-lg > div:not(:last-child) {
  border-right: 1px solid #eee;
}
@media (max-width: 991px) {
  .divide-lg > div { 
    border-right: none !important; 
    border-bottom: 1px solid #eee; 
  }
  .divide-lg > div:last-child { border-bottom: none; }
}

/* Category Cards */
.category-card {
  transition: all 0.3s ease;
  background: #fff;
  border-radius: 1rem;
}
.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
.icon-circle {
  width: 60px; height: 60px;
  display: flex; align-items: center; justify-content: center;
  transition: 0.3s;
  background-color: #f0f8ff; /* Warna soft light */
}
.category-card:hover .icon-circle {
  background-color: #0d6efd !important;
  color: #fff !important;
}

/* --- CARDS & HOVER EFFECTS --- */
.product-card, .testimonial-card {
  transition: all 0.3s ease;
  border-radius: 1rem;
}
.product-card:hover, .testimonial-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0,0,0,0.12) !important;
}

/* Product Image & Overlay */
.product-img-wrap { overflow: hidden; border-radius: 1rem 1rem 0 0; position: relative; }
.product-action {
  position: absolute;
  bottom: -60px; left: 0; right: 0;
  padding: 15px;
  transition: bottom 0.3s ease;
  background: rgba(255,255,255,0.95);
}
.product-card:hover .product-action { bottom: 0; }

.btn-hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }

/* Animations */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-title { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; }
.animate-subtitle { animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0; }
.animate-buttons { animation: fadeInUp 0.8s ease-out 0.4s forwards; opacity: 0; }
.animate-fade-down { animation: fadeInDown 0.8s ease-out forwards; }
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Scrollbar Custom */
::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: #f1f1f1; }
::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
</style>