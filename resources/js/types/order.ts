export interface Order {
  id: number;
  pelanggan_id: number;
  mitra_id: number;
  jenis_layanan_id: number;

  kode_order: string;

  berat_estimasi: number | null;
  berat_aktual: number | null;

  harga_final: number | null;

  catatan: string | null;

  status: "menunggu_konfirmasi_mitra" | "diproses" | "selesai"|"diterima"|"ditolak"|"dicuci"|"dikeringkan"|"disetrika"|"siap_ambil";

  alasan_penolakan: string | null;

  estimasi_selesai: string | null;
  estimasi_jam: string | null;
  waktu_pelanggan_antar: string | null;
  waktu_diambil: string | null;

  created_at: string;
  updated_at: string;
}
