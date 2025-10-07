// types/hargaJenisLayanan.ts
export interface hargajenislayanan {
    id: number
    harga: number | string        // tergantung format data kamu
    jenis_satuan: string
    jenis_layanan_id: number
    jenis_item_id: number
    created_at?: string
    updated_at?: string
    deleted_at?: string | null
    // relasi opsional (kalau API mengirim relasi)
    jenis_layanan?: {
        id: number
        nama_layanan: string
        deskripsi?: string
    }
    jenis_item?: {
        id: number
        nama: string
        deskripsi?: string
    }
}
