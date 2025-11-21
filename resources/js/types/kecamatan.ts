export interface Kecamatan {
  id: number; // relasi ke tabel wilayah_surabaya
  nama: string;
  created_at: string;   // karena di backend kamu pakai format tanggal
  updated_at: string;
  deleted_at: string | null;  // soft delete bisa null
}
