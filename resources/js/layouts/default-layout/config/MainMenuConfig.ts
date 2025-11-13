import type { MenuItem } from "@/layouts/default-layout/config/types";

const MainMenuConfig: Array<MenuItem> = [

    {
        pages: [
            {
                heading: "Beranda",
                name: "beranda",
                route: "/beranda",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Dashboard",
                name: "dashboard",
                route: "/dashboard",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Pelanggan",
                name: "pelanggan",
                route: "/pelanggan",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Riwayat",
                name: "riwayat",
                route: "/pelanggan/riwayat",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Antar",
                name: "antar",
                route: "/pelanggan/antar",
                keenthemesIcon: "element-11",
            },
        ],
    },
    {
        pages: [
            {
                heading: "Jemput",
                name: "jemput",
                route: "/pelanggan/jemput",
                keenthemesIcon: "element-11",
            },
        ],
    },

    // WEBSITE
    {
        heading: "Website",
        route: "/dashboard/website",
        name: "website",
        pages: [
            // MASTER
            {
                sectionTitle: "Master",
                route: "/master",
                keenthemesIcon: "cube-3",
                name: "master",
                sub: [
                    {
                        sectionTitle: "User",
                        route: "/users",
                        name: "master-user",
                        sub: [
                            {
                                heading: "Role",
                                name: "master-role",
                                route: "/dashboard/master/users/roles",
                            },
                            {
                                heading: "User",
                                name: "master-user",
                                route: "/dashboard/master/users",
                            },
                        ],
                    },
                ],
            },
            {
                heading: "Setting",
                route: "/dashboard/setting",
                name: "setting",
                keenthemesIcon: "setting-2",
            },
            {
                heading: "Mitra",
                route: "/mitra/mitra",
                name: "mitra",
                keenthemesIcon: "setting-2",
            },
            // {
            //     heading: "Data Pelanggan",
            //     route: "/dashboard/data-pelanggan",
            //     name: "datapelanggan",
            //     keenthemesIcon: "setting-2",
            // },
            {
                heading: "Antar-jemput",
                route: "/admin",
                name: "antar-jemput",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Pendapatan Laundry",
                route: "/admin/pendapatan",
                name: "pendapatan",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Laundry Detail",
                route: "/dashboard/laundrydetail",
                name: "laundrydetail",
                keenthemesIcon: "bi bi-truck",
            },

            // {
            //     heading: "Transaksi Layanan",
            //     route: "/admin/transaksilayanan",
            //     name: "transaksilayanan",
            //     keenthemesIcon: "bi bi-truck",
            // },
            {
                heading: "Data Pelanggan",
                route: "/admin/tambah-pelanggan",
                name: "tambahpelanggan",
                keenthemesIcon: "bi bi-truck",
            },
            
        ],
        
    },
    {
        heading: "Layanan",
        route: "/layanan",
        name: "layanan",
        pages: [
            
            {
                heading: "Jenis Item",
                route: "/layanan/jenis-item",
                name: "jenisitem",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Jenis Layanan",
                route: "/layanan/jenis-layanan",
                name: "jenislayanan",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Harga Jenis Layanan",
                route: "/layanan/harga-jenis-layanan",
                name: "hargajenislayanan",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Layanan Prioritas",
                route: "/layanan/layanan-prioritas",
                name: "layananprioritas",
                keenthemesIcon: "bi bi-truck",
            },
            {
                heading: "Layanan Tambahan",
                route: "/layanan/layanan-tambahan",
                name: "layanantambahan",
                keenthemesIcon: "bi bi-truck",
            },
            
        ],
    },
    {
        heading: "Transaksi",
        route: "/transaksi",
        name: "transaksi",
        pages: [
            
            {
                heading: "Transaksi Layanan",
                route: "/transaksi/transaksilayanan",
                name: "transaksilayanan",
                keenthemesIcon: "bi bi-truck",
            },
        ],
        
    },
    
];

export default MainMenuConfig;
