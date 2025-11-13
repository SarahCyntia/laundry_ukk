export interface User {
    id: BigInteger;
    uuid: string;
    name: string;
    email: string;
    // nama_laundry: string;
    // alamat_laundry: string;
    // status: "aktif" | "ditolak" | "pending";
    password?: string;
    phone?: BigInteger;
    role_id: BigInteger;
}
// export interface User {
//     id: BigInteger;
//     uuid: string;
//     name: string;
//     email: string;
//     password?: string;
//     phone?: BigInteger;
//     role_id: BigInteger;
// }
