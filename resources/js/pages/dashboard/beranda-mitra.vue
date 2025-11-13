<template>
  <div class="page-container">
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="icon">🏠</div>
      <h1>Selamat Datang di Beranda</h1>
      <p class="subtitle">Ini halaman utama kamu.</p>
      <button class="btn-primary">Mulai Sekarang</button>
    </div>

    <!-- Partnership Section -->
    <div class="partnership-section">
      <div class="content-wrapper">
        <div class="section-header">
          <h2>Bermitra Bersama Kami</h2>
          <p class="section-desc">
            Bergabunglah dengan ribuan mitra yang telah berkembang bersama kami. 
            Dapatkan keuntungan dan dukungan penuh untuk kesuksesan bisnis Anda.
          </p>
        </div>

        <!-- Partnership Cards -->
        <div class="cards-grid">
          <div class="card card-blue">
            <div class="card-icon">🤝</div>
            <h3>Kemitraan Strategis</h3>
            <p>Jalin kerjasama yang saling menguntungkan dengan dukungan penuh dari tim kami.</p>
            <button class="btn-link blue">Pelajari Lebih Lanjut →</button>
          </div>

          <div class="card card-green">
            <div class="card-icon">💼</div>
            <h3>Peluang Bisnis</h3>
            <p>Akses ke berbagai peluang bisnis yang dapat meningkatkan pendapatan Anda.</p>
            <button class="btn-link green">Pelajari Lebih Lanjut →</button>
          </div>

          <div class="card card-purple">
            <div class="card-icon">📈</div>
            <h3>Pertumbuhan Bersama</h3>
            <p>Berkembang bersama dengan pelatihan, tools, dan sumber daya terbaik.</p>
            <button class="btn-link purple">Pelajari Lebih Lanjut →</button>
          </div>
        </div>

        <!-- Benefits Section -->
        <div class="benefits-box">
          <h3>Keuntungan Menjadi Mitra</h3>
          <div class="benefits-grid">
            <div class="benefit-item">
              <div class="check">✓</div>
              <div>
                <h4>Komisi Menarik</h4>
                <p>Dapatkan komisi kompetitif dari setiap transaksi</p>
              </div>
            </div>
            <div class="benefit-item">
              <div class="check">✓</div>
              <div>
                <h4>Dukungan 24/7</h4>
                <p>Tim support siap membantu kapan saja</p>
              </div>
            </div>
            <div class="benefit-item">
              <div class="check">✓</div>
              <div>
                <h4>Pelatihan Gratis</h4>
                <p>Akses ke materi pelatihan dan webinar eksklusif</p>
              </div>
            </div>
            <div class="benefit-item">
              <div class="check">✓</div>
              <div>
                <h4>Dashboard Analytics</h4>
                <p>Monitor performa bisnis secara real-time</p>
              </div>
            </div>
          </div>
          <div style="text-align: center; margin-top: 32px;">
            <button class="btn-white">Daftar Jadi Mitra Sekarang</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section">
      <div class="content-wrapper">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number blue">5000+</div>
            <div class="stat-label">Mitra Aktif</div>
          </div>
          <div class="stat-item">
            <div class="stat-number green">98%</div>
            <div class="stat-label">Tingkat Kepuasan</div>
          </div>
          <div class="stat-item">
            <div class="stat-number purple">50+</div>
            <div class="stat-label">Kota di Indonesia</div>
          </div>
          <div class="stat-item">
            <div class="stat-number orange">24/7</div>
            <div class="stat-label">Customer Support</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Beranda",
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

/* Hero Section */
.hero-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 20px;
  text-align: center;
}

.icon {
  font-size: 64px;
  margin-bottom: 20px;
}

.hero-section h1 {
  font-size: 32px;
  font-weight: bold;
  color: #333;
  margin-bottom: 12px;
}

.subtitle {
  font-size: 18px;
  color: #666;
  margin-bottom: 32px;
}

.btn-primary {
  background: #2563eb;
  color: white;
  padding: 12px 32px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: #1d4ed8;
}

/* Partnership Section */
.partnership-section {
  background: white;
  padding: 64px 20px;
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

/* Cards Grid */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 32px;
  margin-bottom: 48px;
}

.card {
  padding: 32px;
  border-radius: 12px;
  text-align: center;
  transition: box-shadow 0.3s;
}

.card:hover {
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.card-blue {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
}

.card-green {
  background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
}

.card-purple {
  background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
}

.card-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.card h3 {
  font-size: 20px;
  font-weight: bold;
  color: #333;
  margin-bottom: 12px;
}

.card p {
  color: #666;
  margin-bottom: 16px;
  line-height: 1.5;
}

.btn-link {
  background: none;
  border: none;
  font-weight: 600;
  cursor: pointer;
  font-size: 15px;
}

.btn-link.blue {
  color: #2563eb;
}

.btn-link.green {
  color: #16a34a;
}

.btn-link.purple {
  color: #9333ea;
}

.btn-link:hover {
  opacity: 0.8;
}

/* Benefits Box */
.benefits-box {
  background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
  border-radius: 16px;
  padding: 40px;
  color: white;
}

.benefits-box h3 {
  font-size: 28px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 24px;
}

.benefits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
}

.benefit-item {
  display: flex;
  gap: 16px;
  align-items: start;
}

.check {
  font-size: 28px;
  flex-shrink: 0;
}

.benefit-item h4 {
  font-weight: bold;
  margin-bottom: 8px;
}

.benefit-item p {
  color: #bfdbfe;
  font-size: 14px;
}

.btn-white {
  background: white;
  color: #2563eb;
  padding: 12px 32px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-white:hover {
  background: #f3f4f6;
}

/* Stats Section */
.stats-section {
  background: #f7f7f7;
  padding: 64px 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 32px;
  text-align: center;
}

.stat-item {
  padding: 20px;
}

.stat-number {
  font-size: 36px;
  font-weight: bold;
  margin-bottom: 8px;
}

.stat-number.blue {
  color: #2563eb;
}

.stat-number.green {
  color: #16a34a;
}

.stat-number.purple {
  color: #9333ea;
}

.stat-number.orange {
  color: #ea580c;
}

.stat-label {
  color: #666;
  font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-section h1 {
    font-size: 24px;
  }
  
  .section-header h2 {
    font-size: 24px;
  }
  
  .benefits-box {
    padding: 24px;
  }
  
  .benefits-grid {
    grid-template-columns: 1fr;
  }
}
</style>