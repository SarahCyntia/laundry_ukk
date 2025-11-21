export interface mitra {
  id: number;
  user_id: number;
  nama_laundry: string;
  // pemilik: string;
  // email: string;
  // no_hp?: string | null;
  alamat_laundry?: string | null;
  foto_ktp: string;
  foto_toko: string;
  jam_buka: string;
  jam_tutup: string;
  deskripsi: string;
  status_validasi: "menunggu" | "diterima" | "ditolak";
  status_toko: "buka" | "tutup";
  created_at?: string;
  updated_at?: string;
}
