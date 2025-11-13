export interface mitra {
  id: number;
  user_id: number;
  nama_laundry: string;
  // pemilik: string;
  // email: string;
  // no_hp?: string | null;
  alamat_laundry?: string | null;
  foto_ktp: string;
  // password: string;
  status_validasi: "menunggu" | "diterima" | "ditolak";
  status_toko: "buka" | "tutup";
  created_at?: string;
  updated_at?: string;
}
