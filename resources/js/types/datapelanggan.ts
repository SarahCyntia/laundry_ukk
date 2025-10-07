// export interface dataPelanggan {
//   id?: number;
//   nama: string;
//   jenis_kelamin: 'L' | 'P';  // L = Laki-laki, P = Perempuan
//   telepon: string;
//   alamat: string;
//   created_at?: string;
//   updated_at?: string;
// }
export interface datapelanggan {
  id?: number;
  no?: number;
  nama: string;
  jenis_kelamin: 'L' | 'P';
  telepon: string;
  alamat: string;
  created_at?: string;
  updated_at?: string;
}
