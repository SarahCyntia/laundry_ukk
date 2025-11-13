export interface mitra {
  id: number;
  nama_mitra: string;
  pemilik: string;
  email: string;
  no_hp?: string | null;
  alamat?: string | null;
  password: string;
  status: "aktif" | "nonaktif";
  created_at?: string;
  updated_at?: string;
}
