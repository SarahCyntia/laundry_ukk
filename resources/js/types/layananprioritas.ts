export interface layananprioritas {
  id: number;            // bigint, PK, auto increment
  nama: string;          // varchar(255), not null
  deskripsi?: string;    // text, bisa null
  harga?: number;        // double, bisa null
  prioritas?: number;    // int, bisa null
  deleted_at?: string;   // timestamp, nullable
  created_at?: string;   // timestamp, nullable
  updated_at?: string;   // timestamp, nullable
}
