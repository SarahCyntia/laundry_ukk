<template>
  <div class="page-container">
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="icon">🏠</div>
      <h1>Selamat Datang di Beranda</h1>
      <p class="subtitle">Platform Laundry Terpercaya di Indonesia</p>

      <div class="hero-buttons">
        <button class="btn-primary">Cari Laundry</button>
        <button class="btn-secondary" @click="confirmMitra">
          Daftar Jadi Mitra
        </button>
      </div>
    </div>

    <!-- Layanan untuk Pengguna -->
    <div class="service-section">
      <div class="content-wrapper">
        <div class="section-header">
          <h2>Layanan Laundry Pilihan</h2>
          <p class="section-desc">
            Temukan laundry terbaik di sekitar Anda dengan harga terjangkau dan pelayanan terpercaya.
          </p>
        </div>

        <!-- Filter & Search -->
        <div class="search-box">
          <input
            type="text"
            placeholder="Cari laundry berdasarkan lokasi..."
            class="search-input"
          />
          <button class="btn-search">🔍 Cari</button>
        </div>

        <!-- Laundry Cards -->
        <div class="laundry-grid">
          <div class="laundry-card" v-for="(laundry, index) in laundries" :key="index">
            <div class="laundry-image">
              <span class="laundry-badge">⭐ {{ laundry.rating }}</span>
              <div class="placeholder-img">🧺</div>
            </div>
            <div class="laundry-info">
              <h3>{{ laundry.name }}</h3>
              <p class="location">📍 {{ laundry.location }}</p>
              <p class="price">Mulai dari <strong>{{ laundry.price }}</strong></p>
              <div class="features">
                <span
                  v-for="(tag, i) in laundry.features"
                  :key="i"
                  class="tag"
                  >✓ {{ tag }}</span
                >
              </div>
              <button class="btn-order">Pilih Laundry</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Keunggulan Layanan -->
    <div class="features-section">
      <div class="content-wrapper">
        <h2 class="text-center">Kenapa Pilih Kami?</h2>
        <div class="features-grid">
          <div class="feature-box" v-for="(item, idx) in features" :key="idx">
            <div class="feature-icon">{{ item.icon }}</div>
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
  name: "Beranda",
  data() {
    return {
      laundries: [
        {
          name: "Clean & Fresh Laundry",
          location: "Surabaya Pusat",
          price: "Rp 5.000/kg",
          rating: "4.8",
          features: ["Antar Jemput", "Express"],
        },
        {
          name: "Super Wash Laundry",
          location: "Surabaya Timur",
          price: "Rp 4.500/kg",
          rating: "4.9",
          features: ["Antar Jemput", "24 Jam"],
        },
        {
          name: "Express Clean Service",
          location: "Surabaya Barat",
          price: "Rp 6.000/kg",
          rating: "4.7",
          features: ["Premium", "Setrika"],
        },
        {
          name: "Fresh & Clean Laundry",
          location: "Surabaya Selatan",
          price: "Rp 5.500/kg",
          rating: "4.6",
          features: ["Eco Friendly", "Express"],
        },
      ],
      features: [
        { icon: "🚚", title: "Antar Jemput", desc: "Gratis antar jemput area tertentu" },
        { icon: "⚡", title: "Express Service", desc: "Selesai dalam 3 jam" },
        { icon: "💳", title: "Pembayaran Mudah", desc: "Berbagai metode pembayaran" },
        { icon: "🛡️", title: "Garansi Aman", desc: "Jaminan ganti rugi" },
      ],
    };
  },
  methods: {
    confirmMitra() {
      Swal.fire({
        title: "Daftar Jadi Mitra?",
        text: "Kamu akan diarahkan ke halaman login untuk mendaftar sebagai mitra.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#667eea",
        cancelButtonColor: "#aaa",
        confirmButtonText: "Ya, lanjutkan",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          this.$router.push("/register-mitra");
        }
      });
    },
  },
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.page-container {
  min-height: 100vh;
  background: #f7f7f7;
}

.text-center {
  text-align: center;
  font-size: 32px;
  font-weight: bold;
  color: #333;
  margin-bottom: 48px;
}

.content-wrapper {
  max-width: 1200px;
  margin: 0 auto;
}

.section-header {
  text-align: center;
  margin-bottom: 48px;
}

.section-header h2 {
  font-size: 32px;
  font-weight: bold;
  color: #333;
  margin-bottom: 16px;
}

.section-desc {
  color: #666;
  max-width: 800px;
  margin: 0 auto;
  line-height: 1.6;
}

/* Hero Section */
.hero-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 20px;
  text-align: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.icon {
  font-size: 64px;
  margin-bottom: 20px;
}

.hero-section h1 {
  font-size: 42px;
  font-weight: bold;
  margin-bottom: 12px;
}

.subtitle {
  font-size: 20px;
  margin-bottom: 32px;
  opacity: 0.9;
}

.hero-buttons {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  justify-content: center;
}

.btn-primary {
  background: white;
  color: #667eea;
  padding: 14px 36px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.btn-secondary {
  background: transparent;
  color: white;
  padding: 14px 36px;
  border: 2px solid white;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-secondary:hover {
  background: rgba(255,255,255,0.1);
}

/* Service Section */
.service-section {
  background: white;
  padding: 64px 20px;
}

/* Search Box */
.search-box {
  display: flex;
  gap: 12px;
  max-width: 600px;
  margin: 0 auto 48px;
}

.search-input {
  flex: 1;
  padding: 14px 20px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
  outline: none;
  transition: border-color 0.3s;
}

.search-input:focus {
  border-color: #667eea;
}

.btn-search {
  background: #667eea;
  color: white;
  padding: 14px 28px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-search:hover {
  background: #5568d3;
}

/* Laundry Grid */
.laundry-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.laundry-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.laundry-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.laundry-image {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.laundry-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: white;
  color: #f59e0b;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: bold;
}

.placeholder-img {
  font-size: 64px;
}

.laundry-info {
  padding: 20px;
}

.laundry-info h3 {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.location {
  color: #666;
  font-size: 14px;
  margin-bottom: 8px;
}

.price {
  color: #333;
  font-size: 15px;
  margin-bottom: 12px;
}

.price strong {
  color: #667eea;
  font-size: 16px;
}

.features {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.tag {
  background: #f3f4f6;
  color: #16a34a;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
}

.btn-order {
  width: 100%;
  background: #667eea;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-order:hover {
  background: #5568d3;
}

/* Features Section */
.features-section {
  background: #f9fafb;
  padding: 64px 20px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 32px;
}

.feature-box {
  text-align: center;
  padding: 24px;
}

.feature-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.feature-box h3 {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.feature-box p {
  color: #666;
  font-size: 14px;
  line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-section h1 {
    font-size: 28px;
  }
  
  .subtitle {
    font-size: 16px;
  }
  
  .section-header h2, .text-center {
    font-size: 24px;
  }
  
  .search-box {
    flex-direction: column;
  }
}
</style>